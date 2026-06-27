<?php

require_once '../../classes/Database.php';
require_once '../../classes/Auth.php';
require_once '../../classes/Order.php';
require_once '../../classes/Flash.php';

$conn = Database::connect();

$auth = new Auth($conn);
$auth->requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: orders.php");
    exit;
}

$orderId = isset($_POST['order_id']) ? (int)$_POST['order_id'] : 0;
$status = $_POST['order_status'] ?? '';
$redirectTo = $_POST['redirect_to'] ?? 'orders.php';

try {
    if ($orderId <= 0) {
        throw new Exception("Invalid order ID.");
    }

    $orderModel = new Order($conn);
    $orderModel->updateStatus($orderId, $status);

    Flash::set('success', 'Order status updated successfully.');

    header("Location: " . $redirectTo);
    exit;

} catch (Exception $e) {
    Flash::set('error', $e->getMessage());

    header("Location: " . $redirectTo);
    exit;
}