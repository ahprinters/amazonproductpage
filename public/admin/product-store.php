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

try {
    $title = trim($_POST['title'] ?? '');
    $slug = trim($_POST['slug'] ?? '');

    if ($title === '') {
        throw new Exception("Product title is required.");
    }

    if ($slug === '') {
        throw new Exception("Product slug is required.");
    }

    $data = [
        'title' => $title,
        'slug' => strtolower($slug),
        'price' => (float)($_POST['price'] ?? 0),
        'old_price' => (float)($_POST['old_price'] ?? 0),
        'sale_price' => (float)($_POST['sale_price'] ?? ($_POST['price'] ?? 0)),
        'color' => trim($_POST['color'] ?? ''),
        'material' => trim($_POST['material'] ?? ''),
        'dimensions' => trim($_POST['dimensions'] ?? ''),
        'weight' => trim($_POST['weight'] ?? ''),
        'description' => trim($_POST['description'] ?? ''),
        'brand' => trim($_POST['brand'] ?? ''),
        'stock' => (int)($_POST['stock'] ?? 0),
        'rating' => (float)($_POST['rating'] ?? 0),
        'review_count' => (int)($_POST['review_count'] ?? 0),
        'thumbnail' => trim($_POST['thumbnail'] ?? '')
    ];

    if ($data['price'] <= 0) {
        throw new Exception("Product price must be greater than 0.");
    }

    if ($data['stock'] < 0) {
        throw new Exception("Stock cannot be negative.");
    }

    $productModel = new Product($conn);
    $productModel->createProduct($data);

    Flash::set('success', 'Product created successfully.');

    header("Location: products.php");
    exit;

} catch (Exception $e) {
    Flash::set('error', $e->getMessage());

    header("Location: product-create.php");
    exit;
}