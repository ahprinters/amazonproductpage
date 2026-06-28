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

function uploadProductThumbnailForUpdate($file, $oldThumbnail = '')
{
    if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return $oldThumbnail;
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception("Image upload failed.");
    }

    $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

    if (!in_array($file['type'], $allowedTypes)) {
        throw new Exception("Only JPG, PNG, WEBP and GIF images are allowed.");
    }

    $maxSize = 2 * 1024 * 1024;

    if ($file['size'] > $maxSize) {
        throw new Exception("Image size must be less than 2MB.");
    }

    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);

    $fileName = 'product-' . time() . '-' . rand(1000, 9999) . '.' . strtolower($extension);

    $uploadDir = __DIR__ . '/../../assets/images/products/';

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $destination = $uploadDir . $fileName;

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        throw new Exception("Failed to save uploaded image.");
    }

    return 'products/' . $fileName;
}

$productId = isset($_POST['id']) ? (int)$_POST['id'] : 0;

try {
    if ($productId <= 0) {
        throw new Exception("Invalid product ID.");
    }

    $productModel = new Product($conn);

    $product = $productModel->findById($productId);

    if (!$product) {
        throw new Exception("Product not found.");
    }

    $title = trim($_POST['title'] ?? '');
    $slug = trim($_POST['slug'] ?? '');

    if ($title === '') {
        throw new Exception("Product title is required.");
    }

    $slug = $productModel->makeSlug($slug);

    if ($slug === '') {
        throw new Exception("Product slug is invalid.");
    }

    if ($productModel->slugExists($slug, $productId)) {
        throw new Exception("This product slug already exists. Please use another slug.");
    }

    $thumbnail = uploadProductThumbnailForUpdate(
        $_FILES['thumbnail'] ?? null,
        $product['thumbnail'] ?? ''
    );

    $data = [
        'title' => $title,
        'slug' => $slug,
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
        'thumbnail' => $thumbnail,
        'status' -> $_POST['status']?? 'active'
    ];

    if (!in_array($data['status'], ['active', 'inactive'])) {
    throw new Exception("Invalid product status.");
    }

    if ($data['price'] <= 0) {
        throw new Exception("Product price must be greater than 0.");
    }

    if ($data['stock'] < 0) {
        throw new Exception("Stock cannot be negative.");
    }

    $productModel->updateProduct($productId, $data);

    Flash::set('success', 'Product updated successfully.');

    header("Location: products.php");
    exit;

} catch (Exception $e) {
    Flash::set('error', $e->getMessage());

    header("Location: product-edit.php?id=" . $productId);
    exit;
}