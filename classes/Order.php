<?php

class Order
{
    private $conn;

    public function __construct($connection)
    {
        $this->conn = $connection;
    }

    public function createGuestOrder($customer, $items, $subtotal, $deliveryCharge = 0)
    {
        $orderNumber = $this->generateOrderNumber();
        $totalAmount = $subtotal + $deliveryCharge;
    
        $this->conn->begin_transaction();

        try {
            $stmt = $this->conn->prepare("
                INSERT INTO orders
                (
                    order_number,
                    customer_name,
                    customer_email,
                    customer_phone,
                    shipping_address,
                    city,
                    postal_code,
                    subtotal,
                    delivery_charge,
                    total_amount,
                    payment_method
                )
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->bind_param(
                "sssssssddds",
                $orderNumber,
                $customer['customer_name'],
                $customer['customer_email'],
                $customer['customer_phone'],
                $customer['shipping_address'],
                $customer['city'],
                $customer['postal_code'],
                $subtotal,
                $deliveryCharge,
                $totalAmount,
                $customer['payment_method']
            );

            $stmt->execute();

            $orderId = $stmt->insert_id;

            foreach ($items as $item) {
                $this->reduceStock(
                    $item['product_id'],
                    $item['sku'],
                    $item['quantity']
                );

                $itemStmt = $this->conn->prepare("
                    INSERT INTO order_items
                    (
                        order_id,
                        product_id,
                        sku,
                        product_title,
                        color_name,
                        price,
                        quantity,
                        subtotal
                    )
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                ");

                $itemStmt->bind_param(
                    "iisssdid",
                    $orderId,
                    $item['product_id'],
                    $item['sku'],
                    $item['title'],
                    $item['color_name'],
                    $item['price'],
                    $item['quantity'],
                    $item['subtotal']
                );

                $itemStmt->execute();
            }

            $this->conn->commit();

            return [
                'id' => $orderId,
                'order_number' => $orderNumber
            ];

        } catch (Exception $e) {
            $this->conn->rollback();
            throw $e;
        }
    }

    public function findById($orderId)
    {
        $stmt = $this->conn->prepare("
            SELECT *
            FROM orders
            WHERE id = ?
            LIMIT 1
        ");

        $stmt->bind_param("i", $orderId);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }

    public function findByOrderNumber($orderNumber)
    {
        $stmt = $this->conn->prepare("
            SELECT *
            FROM orders
            WHERE order_number = ?
            LIMIT 1
        ");

        $stmt->bind_param("s", $orderNumber);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }

    public function getItems($orderId)
    {
        $stmt = $this->conn->prepare("
            SELECT *
            FROM order_items
            WHERE order_id = ?
            ORDER BY id ASC
        ");

        $stmt->bind_param("i", $orderId);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    public function getAllOrders()
    {
        $stmt = $this->conn->prepare("
            SELECT *
            FROM orders
            ORDER BY created_at DESC
        ");

        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    private function reduceStock($productId, $sku, $quantity)
    {
        $stmt = $this->conn->prepare("
            UPDATE product_variants
            SET stock = stock - ?
            WHERE product_id = ?
            AND sku = ?
            AND stock >= ?
        ");

        $stmt->bind_param(
            "iisi",
            $quantity,
            $productId,
            $sku,
            $quantity
        );

        $stmt->execute();

        if ($stmt->affected_rows !== 1) {
            throw new Exception("Stock is not available for one or more selected products.");
        }
    }

    private function generateOrderNumber()
    {
        return 'ORD-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));
    }
}