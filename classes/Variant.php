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

    public function getAllColors()
    {
        $stmt = $this->conn->prepare("
            SELECT *
            FROM product_colors
            ORDER BY color_name ASC
        ");

        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function createVariant($data)
    {
        if ((int)$data['is_default'] === 1) {
            $resetStmt = $this->conn->prepare("
                UPDATE product_variants
                SET is_default = 0
                WHERE product_id = ?
            ");

            $resetStmt->bind_param("i", $data['product_id']);
            $resetStmt->execute();
        }

        $stmt = $this->conn->prepare("
            INSERT INTO product_variants
            (
                product_id,
                color_id,
                size_id,
                sku,
                image,
                price,
                old_price,
                stock,
                is_default,
                status,
                created_at,
                updated_at
            )
            VALUES
            (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
        ");

        $stmt->bind_param(
            "iiissddiis",
            $data['product_id'],
            $data['color_id'],
            $data['size_id'],
            $data['sku'],
            $data['image'],
            $data['price'],
            $data['old_price'],
            $data['stock'],
            $data['is_default'],
            $data['status']
        );

        return $stmt->execute();
    }

    public function getAllByProductId($productId)
    {
        $stmt = $this->conn->prepare("
            SELECT
                pv.*,
                pc.color_name,
                pc.color_code
            FROM product_variants pv
            LEFT JOIN product_colors pc ON pv.color_id = pc.id
            WHERE pv.product_id = ?
            ORDER BY pv.is_default DESC, pv.id ASC
        ");

        $stmt->bind_param("i", $productId);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function findById($id)
    {
        $stmt = $this->conn->prepare("
            SELECT *
            FROM product_variants
            WHERE id = ?
            LIMIT 1
        ");

        $stmt->bind_param("i", $id);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }

    public function updateVariant($variantId, $data)
    {
        if ((int)$data['is_default'] === 1) {
            $resetStmt = $this->conn->prepare("
                UPDATE product_variants
                SET is_default = 0
                WHERE product_id = ?
                AND id != ?
            ");

            $resetStmt->bind_param(
                "ii",
                $data['product_id'],
                $variantId
            );

            $resetStmt->execute();
        }

        $stmt = $this->conn->prepare("
            UPDATE product_variants
            SET
                color_id = ?,
                size_id = ?,
                sku = ?,
                image = ?,
                price = ?,
                old_price = ?,
                stock = ?,
                is_default = ?,
                status = ?,
                updated_at = NOW()
            WHERE id = ?
            LIMIT 1
        ");

        $stmt->bind_param(
            "iissddiisi",
            $data['color_id'],
            $data['size_id'],
            $data['sku'],
            $data['image'],
            $data['price'],
            $data['old_price'],
            $data['stock'],
            $data['is_default'],
            $data['status'],
            $variantId
        );

        return $stmt->execute();
    }
}