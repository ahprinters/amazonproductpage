<?php

class Variant
{
    private $conn;

    public function __construct($connection)
    {
        $this->conn = $connection;
    }

    public function getByProductId($productId)
    {
        $stmt = $this->conn->prepare("
            SELECT
                pv.*,
                pc.color_name,
                pc.color_code
            FROM product_variants pv
            LEFT JOIN product_colors pc ON pv.color_id = pc.id
            WHERE pv.product_id = ?
            AND pv.status = 'active'
            ORDER BY pv.is_default DESC, pv.id ASC
        ");

        $stmt->bind_param("i", $productId);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getCartVariant($productId, $sku)
    {
        $stmt = $this->conn->prepare("
            SELECT 
                p.title,
                p.thumbnail,
                pv.sku,
                COALESCE(pv.image, p.thumbnail) AS image,
                pc.color_name
            FROM product_variants pv
            JOIN products p ON pv.product_id = p.id
            LEFT JOIN product_colors pc ON pv.color_id = pc.id
            WHERE pv.product_id = ?
            AND pv.sku = ?
            LIMIT 1
        ");

        $stmt->bind_param("is", $productId, $sku);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }

    public function findByProductAndSku($productId, $sku)
    {
        $stmt = $this->conn->prepare("
            SELECT *
            FROM product_variants
            WHERE product_id = ?
            AND sku = ?
            AND status = 'active'
            LIMIT 1
        ");

        $stmt->bind_param("is", $productId, $sku);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }

    public function getCheckoutVariant($productId, $sku)
    {
        $stmt = $this->conn->prepare("
            SELECT 
                p.id AS product_id,
                p.title,
                p.thumbnail,
                pv.sku,
                COALESCE(pv.image, p.thumbnail) AS image,
                pv.price,
                pv.stock,
                pc.color_name
            FROM product_variants pv
            JOIN products p ON pv.product_id = p.id
            LEFT JOIN product_colors pc ON pv.color_id = pc.id
            WHERE pv.product_id = ?
            AND pv.sku = ?
            AND pv.status = 'active'
            LIMIT 1
        ");

        $stmt->bind_param("is", $productId, $sku);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }
}