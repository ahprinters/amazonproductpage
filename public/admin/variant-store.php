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

function uploadVariantImage($file)
{
    if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return '';
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception("Variant image upload failed.");
    }

    $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    $mimeType = mime_content_type($file['tmp_name']);

    if (!in_array($mimeType, $allowedTypes)) {
        throw new Exception("Only JPG, PNG, WEBP and GIF images are allowed.");
    }

    $maxSize = 2 * 1024 * 1024;

    if ($file['size'] > $maxSize) {
        throw new Exception("Variant image size must be less than 2MB.");
    }

    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);

    $fileName = 'variant-' . time() . '-' . rand(1000, 9999) . '.' . strtolower($extension);

    $uploadDir = __DIR__ . '/../../assets/images/variants/';

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $destination = $uploadDir . $fileName;

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        throw new Exception("Failed to save variant image.");
    }

    return 'variants/' . $fileName;
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

    $sku = strtoupper($sku);

    $variantModel = new Variant($conn);

    if ($variantModel->skuExists($sku)) {
        throw new Exception("This SKU already exists. Please use another SKU.");
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

    $variantImage = uploadVariantImage($_FILES['image'] ?? null);

    $data = [
        'product_id' => $productId,
        'color_id' => $colorId,
        'size_id' => null,
        'sku' => $sku,
        'image' => $variantImage,
        'price' => $price,
        'old_price' => $oldPrice,
        'stock' => $stock,
        'is_default' => isset($_POST['is_default']) ? 1 : 0,
        'status' => $_POST['status'] ?? 'active'
    ];

    $variantModel->createVariant($data);

    Flash::set('success', 'Product variant created successfully.');

    header("Location: product-variants.php?product_id=" . $productId);
    exit;

} catch (Exception $e) {
    Flash::set('error', $e->getMessage());

    header("Location: product-variants.php?product_id=" . $productId);
    exit;
}