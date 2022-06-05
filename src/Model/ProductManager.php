<?php

namespace App\Model;

class ProductManager extends AbstractManager
{
    public const TABLE = 'product';

    public function selectAll(string $orderBy = '', string $direction = 'ASC'): array
    {
        $query = "SELECT p.id, p.name, p.description, p.price, p.image, 
        p.quantity, p.color_id, p.category_id, col.name AS color, cat.type AS category
        FROM " . static::TABLE . " AS p
        INNER JOIN color AS col
        ON p.color_id = col.id
        INNER JOIN category AS cat
        ON p.category_id = cat.id";
        if ($orderBy) {
            $query .= ' ORDER BY ' . $orderBy . ' ' . $direction;
        }

        return $this->pdo->query($query)->fetchAll();
    }

    public function selectOneById(int $id): array|false
    {
        $statement = $this->pdo->prepare("SELECT p.id, p.name, p.description, p.price, p.image, 
        p.quantity, p.color_id, p.category_id, col.name AS color, cat.type AS category
        FROM " . static::TABLE . " AS p
        INNER JOIN color AS col
        ON p.color_id = col.id
        INNER JOIN category AS cat
        ON p.category_id = cat.id
        WHERE p.id=:id");
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetch();
    }

    /**
     * Insert new product in database
     */
    public function insert(array $product): int
    {
        $statement = $this->pdo->prepare("INSERT INTO product (name, description, price, image, quantity,
        color_id, category_id) VALUES (:name, :description, :price, :image, :quantity, :color_id, :category_id)");
        $statement->bindValue('name', $product['name'], \PDO::PARAM_STR);
        $statement->bindValue('description', $product['description'], \PDO::PARAM_STR);
        $statement->bindValue('price', $product['price'], \PDO::PARAM_INT);
        $statement->bindValue('image', $product['image'], \PDO::PARAM_STR);
        $statement->bindValue('quantity', $product['quantity'], \PDO::PARAM_INT);
        $statement->bindValue('color_id', $product['color_id'], \PDO::PARAM_INT);
        $statement->bindValue('category_id', $product['category_id'], \PDO::PARAM_INT);

        $statement->execute();
        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Update product in database
     */
    public function update(array $product): bool
    {
        $statement = $this->pdo->prepare("UPDATE " . self::TABLE . " SET `name` = :name, `description` = :description,
        `price`= :price, `image` = :image, `quantity` = :quantity, `color_id` = :color_id, `category_id` = :category_id
        WHERE id=:id");
        $statement->bindValue('id', $product['id'], \PDO::PARAM_INT);
        $statement->bindValue('name', $product['name'], \PDO::PARAM_STR);
        $statement->bindValue('description', $product['description'], \PDO::PARAM_STR);
        $statement->bindValue('price', $product['price'], \PDO::PARAM_INT);
        $statement->bindValue('image', $product['image'], \PDO::PARAM_STR);
        $statement->bindValue('quantity', $product['quantity'], \PDO::PARAM_INT);
        $statement->bindValue('color_id', $product['color_id'], \PDO::PARAM_INT);
        $statement->bindValue('category_id', $product['category_id'], \PDO::PARAM_INT);

        return $statement->execute();
    }

    /**
     * Update quantity of a product in database after purchasing
     */
    public function updateQuantity(int $productId, int $quantity): bool
    {
        $statement = $this->pdo->prepare("UPDATE " . self::TABLE . " SET `quantity` = :quantity WHERE id=:id");
        $statement->bindValue('id', $productId, \PDO::PARAM_INT);
        $statement->bindValue('quantity', $quantity, \PDO::PARAM_INT);

        return $statement->execute();
    }

    /**
     * Test if the name of a product is in database
     */

    public function isInDatabase(string $name): bool
    {
        $productManager = new ProductManager();
        $products = $productManager->selectAll('name');
        foreach ($products as $productName) {
            if ($name === $productName['name']) {
                return true;
            }
        }
        return false;
    }

    public function isEmpty(array $product): bool
    {
        foreach ($product as $key => $caracteristic) {
            if (empty($caracteristic)) {
                if ($key === 'id') {
                    continue;
                }
                return true;
            }
        }
        return false;
    }

    public function selectAllByColByCat(int $col, int $cat)
    {
        $statement = $this->pdo->prepare("SELECT p.id, p.name, p.description, p.price, p.image, 
        p.quantity, p.color_id, p.category_id, col.name AS color, cat.type AS category
        FROM product p 
        INNER JOIN category as cat 
        ON p.category_id = cat.id  
        INNER JOIN color as col 
        ON p.color_id = col.id 
        WHERE p.category_id=:cat and p.color_id=:col");
        $statement->bindValue('cat', $cat, \PDO::PARAM_INT);
        $statement->bindValue('col', $col, \PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchAll();
    }

    public function selectAllByCategory(int $id): array
    {
        $statement = $this->pdo->prepare("SELECT p.id, p.name, p.description, p.price, p.image, 
        p.quantity, p.color_id, p.category_id, col.name AS color, cat.type AS category
        FROM " . static::TABLE . " AS p
        INNER JOIN color AS col
        ON p.color_id = col.id
        INNER JOIN category AS cat
        ON p.category_id = cat.id
        WHERE cat.id=:id");
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }

    public function searchByName(string $keyWord): array
    {
        $statement = $this->pdo->prepare("SELECT p.id, p.name, p.description, p.price, p.image
        FROM " . static::TABLE . " p WHERE LOWER(p.name) LIKE LOWER(:keyWord) 
        OR LOWER(description) LIKE LOWER(:keyWord)");
        $statement->bindValue('keyWord', "%" . $keyWord . "%", \PDO::PARAM_STR);
        $statement->execute();

        return $statement->fetchAll();
    }

    public function searchByColorId(string $id): array
    {
        $statement = $this->pdo->prepare("SELECT p.id, p.name, p.description, p.price, p.image, 
        p.quantity, p.color_id, p.category_id, col.name AS color, cat.type AS category
        FROM " . static::TABLE . " AS p
        INNER JOIN color AS col
        ON p.color_id = col.id
        INNER JOIN category AS cat
        ON p.category_id = cat.id
        WHERE col.id=:id");
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }

    public function searchByCategoryId(string $id): array
    {
        $statement = $this->pdo->prepare("SELECT category_id
        FROM " . static::TABLE . " WHERE category_id = :category_id");
        $statement->bindValue('category_id', $id, \PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }
}
