<?php

require_once '../classes/Database.php';
require_once '../classes/Cart.php';
require_once '../classes/Variant.php';
require_once '../classes/Flash.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: product.php");
    exit;
}

$productId = $_POST['product_id'] ?? null;
$sku = $_POST['variant_sku'] ?? null;
$price = $_POST['variant_price'] ?? null;
$quantity = $_POST['quantity'] ?? 1;
$redirectUrl = $_POST['redirect_url'] ?? 'product.php';

try {
    $conn = Database::connect();

    $variantModel = new Variant($conn);
    $variant = $variantModel->findByProductAndSku((int)$productId, $sku);

    if (!$variant) {
        throw new Exception("Selected product variant not found.");
    }

    if ((int)$variant['stock'] <= 0) {
        throw new Exception("This product is out of stock.");
    }

    $cart = new Cart();

    $cart->add(
        $productId,
        $sku,
        $price,
        $quantity,
        (int)$variant['stock']
    );

    Flash::set('success', 'Product added to cart successfully.');

    header("Location: cart.php");
    exit;

} catch (Exception $e) {
    Flash::set('error', $e->getMessage());

    header("Location: " . $redirectUrl);
    exit;
}