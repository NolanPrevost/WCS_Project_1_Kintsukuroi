<?php

namespace App\Model;

class ColorManager extends AbstractManager
{
    public const TABLE = 'color';

    /**
    * Insert new color in database
    */
    public function insert(array $color): int
    {
        $statement = $this->pdo->prepare("INSERT INTO color (name) VALUES (:name)");
        $statement->bindValue('name', $color['name'], \PDO::PARAM_STR);
        $statement->execute();
        return (int)$this->pdo->lastInsertId();
    }

    /**
    * Update color in database
    */
    public function update(array $color): bool
    {
        $statement = $this->pdo->prepare("UPDATE " . self::TABLE . " SET name = :name WHERE id=:id");
        $statement->bindValue('id', $color['id'], \PDO::PARAM_INT);
        $statement->bindValue('name', $color['name'], \PDO::PARAM_STR);

        return $statement->execute();
    }

    public function searchColor(array $color): array|false
    {
        $statement = $this->pdo->prepare("SELECT * FROM " . self::TABLE . " WHERE name=:name");
        $statement->bindValue('name', $color['name'], \PDO::PARAM_STR);
        $statement->execute();

        return $statement->fetch();
    }
}
