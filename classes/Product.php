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
}