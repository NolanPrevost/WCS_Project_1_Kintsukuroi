<?php

namespace App\Model;

class InvProdManager extends AbstractManager
{
    public const TABLE = 'invoice_product';

    /**
     * Get all rows from database.
     */
    public function selectAll(string $orderBy = '', string $direction = 'ASC'): array
    {
        $query = "SELECT ip.invoice_id AS invoice_id, ip.quantity, p.name, col.name AS color, 
        cat.type AS category, i.id AS invoice_id 
        FROM " . static::TABLE . " AS ip
        INNER JOIN product AS p ON ip.product_id = p.id
        INNER JOIN invoice AS i ON ip.invoice_id = i.id
        INNER JOIN color AS col ON p.color_id = col.id
        INNER JOIN category AS cat ON p.category_id = cat.id";
        if ($orderBy) {
            $query .= ' ORDER BY ' . $orderBy . ' ' . $direction;
        }

        return $this->pdo->query($query)->fetchAll();
    }

    /**
     * Get all invprod from database by invoice.
     */
    public function selectInvProdByInvId(int $id): array|false
    {
        // prepared request
        $statement = $this->pdo->prepare('SELECT ip.invoice_id, ip.quantity, p.name, col.name AS color,
        cat.type AS category, i.id, p.price, p.id AS product_id 
        FROM ' . static::TABLE . ' AS ip
        INNER JOIN product AS p ON ip.product_id = p.id
        INNER JOIN invoice AS i ON ip.invoice_id = i.id
        INNER JOIN color AS col ON p.color_id = col.id
        INNER JOIN category AS cat ON p.category_id = cat.id
        WHERE invoice_id = :id');
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }

    /**
     * Insert new invProd in database
     */
    public function insert(array $invProd): int
    {
        $statement = $this->pdo->prepare("INSERT INTO " . self::TABLE .
        " (invoice_id, product_id, quantity) VALUES (:invoice_id, :product_id, :quantity)");
        $statement->bindValue('invoice_id', $invProd['invoice_id'], \PDO::PARAM_INT);
        $statement->bindValue('product_id', $invProd['product_id'], \PDO::PARAM_INT);
        $statement->bindValue('quantity', $invProd['quantity'], \PDO::PARAM_INT);

        $statement->execute();
        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Update invProd in database
     */
    public function update(array $invProd): bool
    {
        $statement = $this->pdo->prepare("UPDATE " . self::TABLE .
        " SET invoice_id = :invoice_id, product_id = :product_id, quantity = :quantity WHERE id=:id");
        $statement->bindValue('id', $invProd['id'], \PDO::PARAM_INT);
        $statement->bindValue('invoice_id', $invProd['invoice_id'], \PDO::PARAM_INT);
        $statement->bindValue('product_id', $invProd['product_id'], \PDO::PARAM_INT);
        $statement->bindValue('quantity', $invProd['quantity'], \PDO::PARAM_INT);

        return $statement->execute();
    }
}
