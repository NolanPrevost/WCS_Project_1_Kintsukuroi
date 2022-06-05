<?php

namespace App\Model;

class UserManager extends AbstractManager
{
    public const TABLE = 'user';

    /**
     * Insert new user in database
     */
    public function insert(array $user): int
    {
        $statement = $this->pdo->prepare("INSERT INTO " . self::TABLE .
        " (`firstname`, `lastname`, `password`, `email`, `address`, `phone`) 
        VALUES (:firstname, :lastname, :password, :email, :address, :phone)");
        $statement->bindValue('firstname', $user['firstname'], \PDO::PARAM_STR);
        $statement->bindValue('lastname', $user['lastname'], \PDO::PARAM_STR);
        $statement->bindValue('password', $user['password'], \PDO::PARAM_STR);
        $statement->bindValue('email', $user['email'], \PDO::PARAM_STR);
        $statement->bindValue('address', $user['address'], \PDO::PARAM_STR);
        $statement->bindValue('phone', $user['phone'], \PDO::PARAM_STR);

        $statement->execute();
        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Update user in database
     */
    public function update(array $user): bool
    {
        $statement = $this->pdo->prepare("UPDATE " . self::TABLE .
        " SET `firstname` = :firstname, `lastname` = :lastname, `password` = :password, 
        `email` = :email, `address` = :address, `phone` = :phone, `is_admin` = :is_admin WHERE id=:id");
        $statement->bindValue('id', $user['id'], \PDO::PARAM_INT);
        $statement->bindValue('firstname', $user['firstname'], \PDO::PARAM_STR);
        $statement->bindValue('lastname', $user['lastname'], \PDO::PARAM_STR);
        $statement->bindValue('password', $user['password'], \PDO::PARAM_STR);
        $statement->bindValue('email', $user['email'], \PDO::PARAM_STR);
        $statement->bindValue('address', $user['address'], \PDO::PARAM_STR);
        $statement->bindValue('phone', $user['phone'], \PDO::PARAM_STR);
        $statement->bindValue('is_admin', $user['is_admin'], \PDO::PARAM_INT);

        return $statement->execute();
    }

    public function selectOneByEmail(string $email): array|false
    {
        $statement = $this->pdo->prepare("SELECT * FROM " . self::TABLE . " WHERE email = :email");
        $statement->bindValue('email', $email, \PDO::PARAM_STR);
        $statement->execute();

        return $statement->fetch();
    }

    public function searchByName(string $keyWord): array
    {
        $statement = $this->pdo->prepare("SELECT * FROM "
        . static::TABLE . " WHERE LOWER(firstname) LIKE LOWER(:keyWord) OR LOWER(lastname) LIKE LOWER(:keyWord)");
        $statement->bindValue('keyWord', "%" . $keyWord . "%", \PDO::PARAM_STR);
        $statement->execute();

        return $statement->fetchAll();
    }
}
