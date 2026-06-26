<?php

require_once '../classes/Database.php';
require_once '../classes/Cart.php';
require_once '../classes/Variant.php';
require_once '../classes/Order.php';
require_once '../classes/Flash.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: checkout.php");
    exit;
}

try {
    $customerName = trim($_POST['customer_name'] ?? '');
    $customerEmail = trim($_POST['customer_email'] ?? '');
    $customerPhone = trim($_POST['customer_phone'] ?? '');
    $shippingAddress = trim($_POST['shipping_address'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $postalCode = trim($_POST['postal_code'] ?? '');
    $paymentMethod = $_POST['payment_method'] ?? 'cod';

    if ($customerName === '' || $customerEmail === '' || $customerPhone === '' || $shippingAddress === '') {
        throw new Exception("Please fill in all required fields.");
    }

    if (!filter_var($customerEmail, FILTER_VALIDATE_EMAIL)) {
        throw new Exception("Please enter a valid email address.");
    }

    $conn = Database::connect();

    $cart = new Cart();
    $cartData = $cart->all();

    if (empty($cartData)) {
        throw new Exception("Your cart is empty.");
    }

    $variantModel = new Variant($conn);

    $orderItems = [];
    $subtotal = 0;
    $deliveryCharge = 0;

    foreach ($cartData as $item) {
        $variant = $variantModel->getCheckoutVariant(
            (int)$item['product_id'],
            $item['sku']
        );

        if (!$variant) {
            throw new Exception("One of your selected products is no longer available.");
        }

        $quantity = (int)$item['quantity'];

        if ($quantity <= 0) {
            throw new Exception("Invalid product quantity.");
        }

        if ($quantity > (int)$variant['stock']) {
            throw new Exception("Stock is not available for " . $variant['title']);
        }

        $price = (float)$variant['price'];
        $itemSubtotal = $price * $quantity;

        $subtotal += $itemSubtotal;

        $orderItems[] = [
            'product_id' => (int)$variant['product_id'],
            'sku' => $variant['sku'],
            'title' => $variant['title'],
            'color_name' => $variant['color_name'] ?? null,
            'price' => $price,
            'quantity' => $quantity,
            'subtotal' => $itemSubtotal
        ];
    }

    $customer = [
        'customer_name' => $customerName,
        'customer_email' => $customerEmail,
        'customer_phone' => $customerPhone,
        'shipping_address' => $shippingAddress,
        'city' => $city,
        'postal_code' => $postalCode,
        'payment_method' => $paymentMethod
    ];

    $orderModel = new Order($conn);

    $orderResult = $orderModel->createGuestOrder(
        $customer,
        $orderItems,
        $subtotal,
        $deliveryCharge
    );

    $cart->clear();

    Flash::set('success', 'Your order has been placed successfully.');

    header("Location: order-success.php?order_number=" .urlencode($orderResult['order_number']));
    exit;

} catch (Exception $e) {
    Flash::set('error', $e->getMessage());

    header("Location: checkout.php");
    exit;
}