<?php

namespace App\Model;

class InvoiceManager extends AbstractManager
{
    public const TABLE = 'invoice';

    /**
     * Get all rows from database.
     */
    public function selectAll(string $orderBy = '', string $direction = 'ASC'): array
    {
        $query = "SELECT i.id, u.id AS user, CONCAT(u.firstname, ' ', u.lastname) AS fullname,
        i.created_at, i.total, i.is_treated, i.user_id
        FROM " . static::TABLE . " i
        INNER JOIN user u ON i.user_id = u.id";
        if ($orderBy) {
            $query .= ' ORDER BY ' . $orderBy . ' ' . $direction;
        }

        return $this->pdo->query($query)->fetchAll();
    }

    /**
     * Get all invoices from database by user_id.
     */
    public function selectInvoicesByUser(int $id): array
    {
        $statement = $this->pdo->prepare('SELECT i.id, u.id AS user, i.created_at, i.total, i.is_treated
        FROM ' . static::TABLE . ' AS i
        INNER JOIN user AS u ON i.user_id = u.id
        WHERE u.id = :id ORDER BY i.created_at DESC');
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }

    /**
     * Get one invoice from database by ID.
     */
    public function selectOneById(int $id): array|false
    {
        // prepared request
        $statement = $this->pdo->prepare("SELECT i.id, u.id AS user,
        CONCAT(u.firstname, ' ', u.lastname) AS fullname,
        u.address, u.phone, u.email, i.recipient_firstname, i.recipient_lastname, 
        i.delivery_address, i.payment, i.created_at, i.total, i.is_treated
        FROM " . static::TABLE . " AS i
        INNER JOIN user AS u ON i.user_id = u.id
        WHERE i.id = :id");
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->execute();

        $invoice = $statement->fetch();

        // if ($invoice['is_treated'] === 0) {
        //     $invoice['is_treated'] = 'en cours';
        // } elseif ($invoice['is_treated'] === 1) {
        //     $invoice['is_treated'] = 'terminée';
        // }

        return $invoice;
    }

    /**
     * Insert new invoice in database
     */
    public function insert(array $invoice): int
    {
        $statement = $this->pdo->prepare("INSERT INTO " . self::TABLE . " 
        (user_id, created_at, recipient_firstname, recipient_lastname, delivery_address, payment, total) 
        VALUES (:user_id, :created_at, :recipient_firstname, :recipient_lastname,
        :delivery_address, :payment, :total)");
        $statement->bindValue('user_id', $invoice['user_id'], \PDO::PARAM_INT);
        $statement->bindValue('created_at', date("d-m-y H:i:s"), \PDO::PARAM_STR);
        $statement->bindValue('recipient_firstname', $invoice['recipient_firstname'], \PDO::PARAM_STR);
        $statement->bindValue('recipient_lastname', $invoice['recipient_lastname'], \PDO::PARAM_STR);
        $statement->bindValue('delivery_address', $invoice['delivery_address'], \PDO::PARAM_STR);
        $statement->bindValue('payment', $invoice['payment'], \PDO::PARAM_STR);
        $statement->bindValue('total', $invoice['total'], \PDO::PARAM_INT);

        $statement->execute();

        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Update invoice in database
     */
    public function update(array $invoice): bool
    {
        $statement = $this->pdo->prepare("UPDATE " . self::TABLE . " SET user_id = :user_id, total = :total 
        WHERE id=:id");
        $statement->bindValue('id', $invoice['id'], \PDO::PARAM_INT);
        $statement->bindValue('user_id', $invoice['user_id'], \PDO::PARAM_INT);
        $statement->bindValue('total', $invoice['total'], \PDO::PARAM_STR);

        return $statement->execute();
    }

    public function treatedInvoices(): array|false
    {
        $query = "SELECT i.id, u.id AS user, CONCAT(u.firstname, ' ', u.lastname) AS fullname,
        i.created_at, i.total, i.is_treated
        FROM " . static::TABLE . " AS i
        INNER JOIN user AS u ON i.user_id = u.id
        WHERE is_treated = 1";

        return $this->pdo->query($query)->fetchAll();
    }

    public function notTreatedInvoices(): array|false
    {
        $query = "SELECT i.id, u.id AS user, CONCAT(u.firstname, ' ', u.lastname) AS fullname,
        i.created_at, i.total, i.is_treated
        FROM " . static::TABLE . " AS i
        INNER JOIN user AS u ON i.user_id = u.id
        WHERE is_treated = 0";

        return $this->pdo->query($query)->fetchAll();
    }

    public function setAsTreatedInvoice(int $id, int $yesOrNo): void
    {
        $statement = $this->pdo->prepare("UPDATE " . self::TABLE . " SET is_treated = :yesOrNo 
        WHERE id=:id");
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->bindValue('yesOrNo', $yesOrNo, \PDO::PARAM_INT);
        $statement->execute();
    }
}
