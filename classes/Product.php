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
            AND status = 'active'
            LIMIT 1
        ");

        $stmt->bind_param("s", $slug);
        $stmt->execute();

        $result = $stmt->get_result();

        return $result->fetch_assoc();
    }

    public function getAllProducts($search = '', $status = '')
    {
        
        $search = trim($search);
        $status = trim($status);

        if (!in_array($status, ['active', 'inactive'])) {
            $status = '';
        }
    
        $stmt = $this->conn->prepare("
            SELECT *
            FROM products
            WHERE
            (
                ?=''
                OR title LIKE CONCAT('%', ?, '%')
                OR slug LIKE CONCAT('%', ?, '%')
                OR brand LIKE CONCAT('%', ?, '%')
            )
            AND
            (
                ? = ''
                OR status = ?
            )

            ORDER BY created_at DESC
        ");

            $stmt->bind_param(
            "ssssss",
            $search,
            $search,
            $search,
            $search,
            $status,
            $status
        );

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
        if (!in_array($data['status'], ['active', 'inactive'])) {
            throw new Exception("Invalid product status.");
        }

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
                status = ?,
                updated_at = NOW()
            WHERE id = ?
            LIMIT 1
        ");

        $stmt->bind_param(
            "ssddssssdssidissi",
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
            $data['status'],
            $id
        );

        return $stmt->execute();
    }

    public function updateStatus($id, $status)
    {
        if (!in_array($status, ['active', 'inactive'])) {
            throw new Exception("Invalid product status.");
        }

        $stmt = $this->conn->prepare("
            UPDATE products
            SET status = ?, updated_at = NOW()
            WHERE id = ?
            LIMIT 1
        ");

        $stmt->bind_param("si", $status, $id);

        return $stmt->execute();
    }

    public function countProducts($search = '', $status = '')
    {
        $search = trim($search);
        $status = trim($status);

        if (!in_array($status, ['active', 'inactive'])) {
            $status = '';
        }

        $stmt = $this->conn->prepare("
            SELECT COUNT(*) AS total
            FROM products
            WHERE
                (
                    ? = ''
                    OR title LIKE CONCAT('%', ?, '%')
                    OR slug LIKE CONCAT('%', ?, '%')
                    OR brand LIKE CONCAT('%', ?, '%')
                )
            AND
                (
                    ? = ''
                    OR status = ?
                )
        ");

        $stmt->bind_param(
            "ssssss",
            $search,
            $search,
            $search,
            $search,
            $status,
            $status
        );

        $stmt->execute();

        $result = $stmt->get_result()->fetch_assoc();

        return (int)($result['total'] ?? 0);
    }

public function getProductsPaginated($search = '', $status = '', $limit = 10, $offset = 0)
    {
        $search = trim($search);
        $status = trim($status);

        if (!in_array($status, ['active', 'inactive'])) {
            $status = '';
        }

        $limit = (int)$limit;
        $offset = (int)$offset;

        $stmt = $this->conn->prepare("
            SELECT *
            FROM products
            WHERE
                (
                    ? = ''
                    OR title LIKE CONCAT('%', ?, '%')
                    OR slug LIKE CONCAT('%', ?, '%')
                    OR brand LIKE CONCAT('%', ?, '%')
                )
            AND
                (
                    ? = ''
                    OR status = ?
                )
            ORDER BY created_at DESC
            LIMIT ? OFFSET ?
        ");

        $stmt->bind_param(
            "ssssssii",
            $search,
            $search,
            $search,
            $search,
            $status,
            $status,
            $limit,
            $offset
        );

        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function slugExists($slug, $excludeId = 0)
    {
        $excludeId = (int)$excludeId;

        $stmt = $this->conn->prepare("
            SELECT id
            FROM products
            WHERE slug = ?
            AND id != ?
            LIMIT 1
        ");

        $stmt->bind_param("si", $slug, $excludeId);
        $stmt->execute();

        $result = $stmt->get_result();

        return $result->num_rows > 0;
    }
    public function makeSlug($text)
    {
        $text = strtolower(trim($text));

        $text = preg_replace('/[^a-z0-9\s-]/', '', $text);

        $text = preg_replace('/[\s-]+/', '-', $text);

        $text = trim($text, '-');

        return $text;
    }
}