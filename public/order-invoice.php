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

<style>
    @media print {
        .no-print {
            display: none !important;
        }

        body {
            background: #ffffff !important;
        }

        .invoice-box {
            box-shadow: none !important;
            border: none !important;
        }
    }
</style>

<div class="no-print">
    <?php include '../includes/navbar.php'; ?>
    <?php include '../includes/flash-message.php'; ?>
</div>

<main class="max-w-4xl mx-auto px-4 py-8">

    <div class="invoice-box border rounded-lg p-8 bg-white">

        <div class="flex justify-between items-start border-b pb-5 mb-6">
            <div>
                <h1 class="text-3xl font-bold">Invoice</h1>
                <p class="text-sm text-gray-600 mt-1">
                    Order Number:
                    <strong><?= htmlspecialchars($order['order_number']) ?></strong>
                </p>
                <p class="text-sm text-gray-600">
                    Date:
                    <?= date('d M Y, h:i A', strtotime($order['created_at'])) ?>
                </p>
            </div>

            <div class="text-right">
                <h2 class="text-2xl font-bold">MyStore</h2>
                <p class="text-sm text-gray-600">Online Shopping Store</p>
                <p class="text-sm text-gray-600">Bangladesh</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">

            <div>
                <h3 class="font-bold mb-2">Bill To</h3>
                <div class="text-sm text-gray-700 space-y-1">
                    <p><?= htmlspecialchars($order['customer_name']) ?></p>
                    <p><?= htmlspecialchars($order['customer_email']) ?></p>
                    <p><?= htmlspecialchars($order['customer_phone']) ?></p>
                </div>
            </div>

            <div>
                <h3 class="font-bold mb-2">Shipping Address</h3>
                <div class="text-sm text-gray-700 space-y-1">
                    <p><?= nl2br(htmlspecialchars($order['shipping_address'])) ?></p>
                    <p><?= htmlspecialchars($order['city'] ?? 'N/A') ?></p>
                    <p><?= htmlspecialchars($order['postal_code'] ?? 'N/A') ?></p>
                </div>
            </div>

        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm border border-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border p-3 text-left">Product</th>
                        <th class="border p-3 text-left">SKU</th>
                        <th class="border p-3 text-left">Color</th>
                        <th class="border p-3 text-center">Qty</th>
                        <th class="border p-3 text-right">Price</th>
                        <th class="border p-3 text-right">Subtotal</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($orderItems as $item): ?>
                        <tr>
                            <td class="border p-3">
                                <?= htmlspecialchars($item['product_title']) ?>
                            </td>

                            <td class="border p-3">
                                <?= htmlspecialchars($item['sku']) ?>
                            </td>

                            <td class="border p-3">
                                <?= htmlspecialchars($item['color_name'] ?? 'N/A') ?>
                            </td>

                            <td class="border p-3 text-center">
                                <?= (int)$item['quantity'] ?>
                            </td>

                            <td class="border p-3 text-right">
                                $<?= number_format((float)$item['price'], 2) ?>
                            </td>

                            <td class="border p-3 text-right">
                                $<?= number_format((float)$item['subtotal'], 2) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="mt-6 flex justify-end">
            <div class="w-full md:w-80 space-y-3">

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

                <div class="text-sm text-gray-700 pt-2">
                    <p>
                        <strong>Payment Method:</strong>
                        <?= strtoupper(htmlspecialchars($order['payment_method'])) ?>
                    </p>

                    <p>
                        <strong>Payment Status:</strong>
                        <?= ucfirst(htmlspecialchars($order['payment_status'])) ?>
                    </p>

                    <p>
                        <strong>Order Status:</strong>
                        <?= ucfirst(htmlspecialchars($order['order_status'])) ?>
                    </p>
                </div>

            </div>
        </div>

        <div class="mt-8 border-t pt-4 text-sm text-gray-600">
            Thank you for shopping with us.
        </div>

    </div>

    <div class="no-print mt-6 flex gap-3 justify-center">
        <button onclick="window.print()"
                class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-2 rounded-full font-semibold">
            Print Invoice
        </button>

        <a href="order-details.php?order_number=<?= urlencode($order['order_number']) ?>"
           class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-2 rounded-full font-semibold">
            Back to Order Details
        </a>
    </div>

</main>

<?php include '../includes/footer.php'; ?>