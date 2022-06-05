<?php

namespace App\Model;

class CategoryManager extends AbstractManager
{
    public const TABLE = 'category';

    /**
     * Insert new item in database
     */
    public function insert(array $category): int
    {
        $statement = $this->pdo->prepare("INSERT INTO " . self::TABLE . " (`type`) VALUES (:type)");
        $statement->bindValue('type', $category['type'], \PDO::PARAM_STR);

        $statement->execute();
        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Update item in database
     */
    public function update(array $category): bool
    {
        $statement = $this->pdo->prepare("UPDATE " . self::TABLE . " SET `type` = :type WHERE id=:id");
        $statement->bindValue('type', $category['type'], \PDO::PARAM_STR);
        $statement->bindValue('id', $category['id'], \PDO::PARAM_INT);


        return $statement->execute();
    }

    public function searchCategory(array $category): array|false
    {
        $statement = $this->pdo->prepare("SELECT * FROM " . self::TABLE . " WHERE type = :type");
        $statement->bindValue('type', $category['type'], \PDO::PARAM_STR);
        $statement->execute();
        return $statement->fetch();
    }
}
