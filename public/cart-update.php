<?php

require_once '../classes/Database.php';
require_once '../classes/Cart.php';
require_once '../classes/Variant.php';
require_once '../classes/Flash.php';


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: cart.php");
    exit;
}

$key = $_POST['key'] ?? null;
$quantity = $_POST['quantity'] ?? 1;

try {
    if (!$key) {
        throw new Exception("Cart item key missing.");
    }

    $cart = new Cart();
    $cartItems = $cart->all();

    if (!isset($cartItems[$key])) {
        throw new Exception("Cart item not found.");
    }

    $item = $cartItems[$key];

    $conn = Database::connect();
    $variantModel = new Variant($conn);

    $variant = $variantModel->findByProductAndSku(
        (int)$item['product_id'],
        $item['sku']
    );

    if (!$variant) {
        throw new Exception("Selected product variant not found.");
    }

    $cart->updateQuantity(
        $key,
        $quantity,
        (int)$variant['stock']
    );

    Flash::set('success', 'Cart quantity updated successfully.');


    header("Location: cart.php");
    exit;

} catch (Exception $e) {
    Flash::set('error', $e->getMessage());

    header("Location: cart.php");
    exit;
}