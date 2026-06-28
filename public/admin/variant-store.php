<?php

require_once __DIR__ . '/../../classes/Database.php';
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../classes/Product.php';
require_once __DIR__ . '/../../classes/Variant.php';
require_once __DIR__ . '/../../classes/Flash.php';

$conn = Database::connect();

$auth = new Auth($conn);
$auth->requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: products.php");
    exit;
}

$productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;

try {
    if ($productId <= 0) {
        throw new Exception("Invalid product ID.");
    }

    $productModel = new Product($conn);
    $product = $productModel->findById($productId);

    if (!$product) {
        throw new Exception("Product not found.");
    }

    $colorId = isset($_POST['color_id']) ? (int)$_POST['color_id'] : 0;
    $sku = trim($_POST['sku'] ?? '');

    if ($colorId <= 0) {
        throw new Exception("Please select a color.");
    }

    if ($sku === '') {
        throw new Exception("SKU is required.");
    }

    $price = (float)($_POST['price'] ?? 0);
    $oldPrice = (float)($_POST['old_price'] ?? 0);
    $stock = (int)($_POST['stock'] ?? 0);

    if ($price <= 0) {
        throw new Exception("Variant price must be greater than 0.");
    }

    if ($stock < 0) {
        throw new Exception("Stock cannot be negative.");
    }

    $data = [
        'product_id' => $productId,
        'color_id' => $colorId,
        'size_id' => null,
        'sku' => $sku,
        'image' => trim($_POST['image'] ?? ''),
        'price' => $price,
        'old_price' => $oldPrice,
        'stock' => $stock,
        'is_default' => isset($_POST['is_default']) ? 1 : 0,
        'status' => $_POST['status'] ?? 'active'
    ];

    $variantModel = new Variant($conn);
    $variantModel->createVariant($data);

    Flash::set('success', 'Product variant created successfully.');

    header("Location: product-variants.php?product_id=" . $productId);
    exit;

} catch (Exception $e) {
    Flash::set('error', $e->getMessage());

    header("Location: product-variants.php?product_id=" . $productId);
    exit;
}