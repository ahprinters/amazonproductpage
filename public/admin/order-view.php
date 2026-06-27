<?php

require_once '../../classes/Database.php';
require_once '../../classes/Auth.php';
require_once '../../classes/Order.php';
require_once '../../classes/Flash.php';

$conn = Database::connect();

$auth = new Auth($conn);
$auth->requireAdmin();

$admin = $auth->admin();

$orderNumber = trim($_GET['order_number'] ?? '');

if ($orderNumber === '') {
    Flash::set('error', 'Invalid order number.');
    header("Location: orders.php");
    exit;
}

$orderModel = new Order($conn);

$order = $orderModel->findByOrderNumber($orderNumber);

if (!$order) {
    Flash::set('error', 'Order not found.');
    header("Location: orders.php");
    exit;
}

$orderItems = $orderModel->getItems((int)$order['id']);

$statuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
?>

<?php include '../../includes/header.php'; ?>
<?php include '../../includes/flash-message.php'; ?>

<main class="max-w-7xl mx-auto px-4 py-8">

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold">Admin Order Details</h1>
            <p class="text-sm text-gray-600 mt-1">
                Order Number:
                <strong><?= htmlspecialchars($order['order_number']) ?></strong>
            </p>
        </div>

        <a href="orders.php"
           class="inline-block bg-gray-200 hover:bg-gray-300 px-5 py-2 rounded-full font-semibold">
            Back to Orders
        </a>

         <a href="logout.php"
                class="inline-block bg-red-500 hover:bg-red-600 text-white px-5 py-2 rounded-full font-semibold">
            Logout
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <section class="lg:col-span-8 space-y-6">

            <div class="border rounded-lg p-5 bg-white">
                <h2 class="text-xl font-bold mb-4">Customer Information</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-700">
                    <p>
                        <strong>Name:</strong>
                        <?= htmlspecialchars($order['customer_name']) ?>
                    </p>

                    <p>
                        <strong>Phone:</strong>
                        <?= htmlspecialchars($order['customer_phone']) ?>
                    </p>

                    <p>
                        <strong>Email:</strong>
                        <?= htmlspecialchars($order['customer_email']) ?>
                    </p>

                    <p>
                        <strong>City:</strong>
                        <?= htmlspecialchars($order['city'] ?? 'N/A') ?>
                    </p>

                    <p class="md:col-span-2">
                        <strong>Shipping Address:</strong><br>
                        <?= nl2br(htmlspecialchars($order['shipping_address'])) ?>
                    </p>

                    <p>
                        <strong>Postal Code:</strong>
                        <?= htmlspecialchars($order['postal_code'] ?? 'N/A') ?>
                    </p>
                </div>
            </div>

            <div class="border rounded-lg p-5 bg-white">
                <h2 class="text-xl font-bold mb-4">Ordered Items</h2>

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
            </div>

        </section>

        <aside class="lg:col-span-4">
            <div class="border rounded-lg p-5 bg-white space-y-5 sticky top-4">

                <h2 class="text-xl font-bold">Order Summary</h2>

                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span>Subtotal</span>
                        <span>$<?= number_format((float)$order['subtotal'], 2) ?></span>
                    </div>

                    <div class="flex justify-between">
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
                </div>

                <div class="border-t pt-4 text-sm text-gray-700 space-y-2">
                    <p>
                        <strong>Payment Method:</strong>
                        <?= strtoupper(htmlspecialchars($order['payment_method'])) ?>
                    </p>

                    <p>
                        <strong>Payment Status:</strong>
                        <?= ucfirst(htmlspecialchars($order['payment_status'])) ?>
                    </p>

                    <p>
                        <strong>Order Date:</strong>
                        <?= date('d M Y, h:i A', strtotime($order['created_at'])) ?>
                    </p>
                </div>

                <form action="order-status-update.php" method="POST" class="border-t pt-4 space-y-3">
                    <input type="hidden" name="order_id" value="<?= (int)$order['id'] ?>">
                    <input type="hidden" name="redirect_to" value="order-view.php?order_number=<?= urlencode($order['order_number']) ?>">

                    <label class="block text-sm font-medium">
                        Update Order Status
                    </label>

                    <select name="order_status" class="w-full border rounded-md px-3 py-2 text-sm">
                        <?php foreach ($statuses as $status): ?>
                            <option value="<?= $status ?>" <?= $order['order_status'] === $status ? 'selected' : '' ?>>
                                <?= ucfirst($status) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <button type="submit"
                            class="w-full bg-gray-900 hover:bg-gray-700 text-white rounded-full py-2 font-semibold">
                        Update Status
                    </button>
                </form>

                <a href="../order-invoice.php?order_number=<?= urlencode($order['order_number']) ?>"
                   class="block text-center w-full bg-orange-500 hover:bg-orange-600 text-white rounded-full py-2 font-semibold">
                    View / Print Invoice
                </a>

            </div>
        </aside>

    </div>

</main>

<?php include '../../includes/footer.php'; ?>