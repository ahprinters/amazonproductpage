<?php

require_once '../classes/Database.php';
require_once '../classes/Order.php';
require_once '../classes/Flash.php';

$conn = Database::connect();

$orderNumber = $_GET['order_number'] ?? '';

if ($orderNumber === '') {
    Flash::set('error', 'Invalid order number.');
    header("Location: product.php");
    exit;
}

$orderModel = new Order($conn);

$order = $orderModel->findByOrderNumber($orderNumber);

if (!$order) {
    Flash::set('error', 'Order not found.');
    header("Location: product.php");
    exit;
}

$orderItems = $orderModel->getItems((int)$order['id']);
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/navbar.php'; ?>
<?php include '../includes/flash-message.php'; ?>

<main class="max-w-7xl mx-auto px-4 py-8">

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold">Order Details</h1>
            <p class="text-sm text-gray-600 mt-1">
                Order #<?= htmlspecialchars($order['id']) ?>
            </p>
        </div>

        <span class="inline-block px-4 py-2 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-800">
            <?= ucfirst(htmlspecialchars($order['order_status'])) ?>
        </span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <section class="lg:col-span-8 space-y-6">

            <div class="border rounded-lg p-5 bg-white">
                <h2 class="text-xl font-bold mb-4">Items Ordered</h2>

                <div class="space-y-4">
                    <?php foreach ($orderItems as $item): ?>
                        <div class="border rounded-lg p-4 flex justify-between gap-4">
                            <div>
                                <h3 class="font-semibold">
                                    <?= htmlspecialchars($item['product_title']) ?>
                                </h3>

                                <p class="text-sm text-gray-600">
                                    SKU: <?= htmlspecialchars($item['sku']) ?>
                                </p>

                                <p class="text-sm text-gray-600">
                                    Color: <?= htmlspecialchars($item['color_name'] ?? 'N/A') ?>
                                </p>

                                <p class="text-sm text-gray-600">
                                    Quantity: <?= (int)$item['quantity'] ?>
                                </p>
                            </div>

                            <div class="text-right">
                                <p class="font-semibold">
                                    $<?= number_format((float)$item['price'], 2) ?>
                                </p>

                                <p class="text-sm text-gray-600">
                                    Subtotal: $<?= number_format((float)$item['subtotal'], 2) ?>
                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="border rounded-lg p-5 bg-white">
                <h2 class="text-xl font-bold mb-4">Shipping Information</h2>

                <div class="space-y-2 text-sm text-gray-700">
                    <p><strong>Name:</strong> <?= htmlspecialchars($order['customer_name']) ?></p>
                    <p><strong>Phone:</strong> <?= htmlspecialchars($order['customer_phone']) ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($order['customer_email']) ?></p>
                    <p><strong>Address:</strong> <?= nl2br(htmlspecialchars($order['shipping_address'])) ?></p>
                    <p><strong>City:</strong> <?= htmlspecialchars($order['city'] ?? 'N/A') ?></p>
                    <p><strong>Postal Code:</strong> <?= htmlspecialchars($order['postal_code'] ?? 'N/A') ?></p>
                </div>
            </div>

        </section>

        <aside class="lg:col-span-4">
            <div class="border rounded-lg p-5 bg-white space-y-4 sticky top-4">

                <h2 class="text-xl font-bold">Payment Summary</h2>

                <div class="flex justify-between text-sm">
                    <span>Subtotal</span>
                    <span>$<?= number_format((float)$order['subtotal'], 2) ?></span>
                </div>

                <div class="flex justify-between text-sm">
                    <span>Delivery</span>
                    <span>
                        <?= (float)$order['delivery_charge'] > 0
                            ? '$' . number_format((float)$order['delivery_charge'], 2)
                            : 'Free'
                        ?>
                    </span>
                </div>

                <hr>

                <div class="flex justify-between text-lg font-bold">
                    <span>Total</span>
                    <span>$<?= number_format((float)$order['total_amount'], 2) ?></span>
                </div>

                <div class="text-sm text-gray-700 border-t pt-4 space-y-2">
                    <p>
                        <strong>Payment Method:</strong>
                        <?= strtoupper(htmlspecialchars($order['payment_method'])) ?>
                    </p>

                    <p>
                        <strong>Payment Status:</strong>
                        <?= ucfirst(htmlspecialchars($order['payment_status'])) ?>
                    </p>
                </div>

                <a href="order-invoice.php?order_number=<?= urlencode($order['order_number']) ?>"
                    class="block text-center w-full bg-orange-500 hover:bg-orange-600 text-white rounded-full py-2 font-semibold">
                    View / Print Invoice
                </a>

                <a href="product.php"
                    class="block text-center w-full bg-yellow-400 hover:bg-yellow-500 rounded-full py-2 font-semibold">
                    Continue Shopping
                </a>

            </div>
        </aside>

    </div>

</main>

<?php include '../includes/footer.php'; ?>