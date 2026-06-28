<?php

require_once __DIR__ . '/../../classes/Database.php';
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../classes/Product.php';
require_once __DIR__ . '/../../classes/Flash.php';

$conn = Database::connect();

$auth = new Auth($conn);
$auth->requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: products.php");
    exit;
}

$productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$status = $_POST['status'] ?? '';

try {
    if ($productId <= 0) {
        throw new Exception("Invalid product ID.");
    }

    if (!in_array($status, ['active', 'inactive'])) {
        throw new Exception("Invalid product status.");
    }

    $productModel = new Product($conn);

    $product = $productModel->findById($productId);

    if (!$product) {
        throw new Exception("Product not found.");
    }

    $productModel->updateStatus($productId, $status);

    Flash::set('success', 'Product status updated successfully.');

    header("Location: products.php");
    exit;

} catch (Exception $e) {
    Flash::set('error', $e->getMessage());

    header("Location: products.php");
    exit;
}