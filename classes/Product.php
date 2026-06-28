<?php

class Product
{
    private $conn;

    public function __construct($connection)
    {
        $this->conn = $connection;
    }

    public function findBySlug($slug)
    {
        $stmt = $this->conn->prepare("
            SELECT *
            FROM products
            WHERE slug = ?
            LIMIT 1
        ");

        $stmt->bind_param("s", $slug);
        $stmt->execute();

        $result = $stmt->get_result();

        return $result->fetch_assoc();
    }

    public function getAllProducts()
    {
        $stmt = $this->conn->prepare("
            SELECT *
            FROM products
            ORDER BY created_at DESC
        ");

        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function createProduct($data)
    {
        $stmt = $this->conn->prepare("
            INSERT INTO products
            (
                title,
                slug,
                price,
                old_price,
                color,
                material,
                dimensions,
                weight,
                sale_price,
                description,
                brand,
                stock,
                rating,
                review_count,
                thumbnail,
                created_at,
                updated_at
            )
            VALUES
            (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
        ");

        $stmt->bind_param(
            "ssddssssdssidis",
            $data['title'],
            $data['slug'],
            $data['price'],
            $data['old_price'],
            $data['color'],
            $data['material'],
            $data['dimensions'],
            $data['weight'],
            $data['sale_price'],
            $data['description'],
            $data['brand'],
            $data['stock'],
            $data['rating'],
            $data['review_count'],
            $data['thumbnail']
        );

        return $stmt->execute();
    }

    public function findById($id)
    {
        $stmt = $this->conn->prepare("
            SELECT *
            FROM products
            WHERE id = ?
            LIMIT 1
        ");

        $stmt->bind_param("i", $id);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }

    public function updateProduct($id, $data)
    {
        $stmt = $this->conn->prepare("
            UPDATE products
            SET
                title = ?,
                slug = ?,
                price = ?,
                old_price = ?,
                color = ?,
                material = ?,
                dimensions = ?,
                weight = ?,
                sale_price = ?,
                description = ?,
                brand = ?,
                stock = ?,
                rating = ?,
                review_count = ?,
                thumbnail = ?,
                updated_at = NOW()
            WHERE id = ?
            LIMIT 1
        ");

        $stmt->bind_param(
            "ssddssssdssidisi",
            $data['title'],
            $data['slug'],
            $data['price'],
            $data['old_price'],
            $data['color'],
            $data['material'],
            $data['dimensions'],
            $data['weight'],
            $data['sale_price'],
            $data['description'],
            $data['brand'],
            $data['stock'],
            $data['rating'],
            $data['review_count'],
            $data['thumbnail'],
            $id
        );

        return $stmt->execute();
    }
}