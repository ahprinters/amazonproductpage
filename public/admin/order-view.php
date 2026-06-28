<?php

require_once __DIR__ . '/../../classes/Database.php';
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../classes/Order.php';
require_once __DIR__ . '/../../classes/Flash.php';

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

function adminOrderStatusClass($status)
{
    switch ($status) {
        case 'pending':
            return 'bg-yellow-100 text-yellow-700 border-yellow-200';
        case 'processing':
            return 'bg-blue-100 text-blue-700 border-blue-200';
        case 'shipped':
            return 'bg-purple-100 text-purple-700 border-purple-200';
        case 'delivered':
            return 'bg-green-100 text-green-700 border-green-200';
        case 'cancelled':
            return 'bg-red-100 text-red-700 border-red-200';
        default:
            return 'bg-slate-100 text-slate-700 border-slate-200';
    }
}

?>

<?php include __DIR__ . '/../../includes/admin-header.php'; ?>
<?php include __DIR__ . '/../../includes/flash-message.php'; ?>

<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">

    <div>
        <h1 class="text-3xl font-bold text-slate-900">Order Details</h1>

        <p class="text-slate-500 mt-1">
            Order Number:
            <span class="font-semibold text-slate-900">
                <?= htmlspecialchars($order['order_number']) ?>
            </span>
        </p>
    </div>

    <div class="flex gap-2">
        <a href="orders.php"
           class="inline-flex items-center justify-center rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-900 px-5 py-2.5 text-sm font-semibold">
            Back to Orders
        </a>

        <a href="../order-invoice.php?order_number=<?= urlencode($order['order_number']) ?>"
           class="inline-flex items-center justify-center rounded-xl bg-orange-500 hover:bg-orange-600 text-white px-5 py-2.5 text-sm font-semibold">
            Print Invoice
        </a>
    </div>

</div>

<div class="grid grid-cols-1 xl:grid-cols-12 gap-6">

    <section class="xl:col-span-8 space-y-6">

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

            <div class="px-6 py-5 border-b border-slate-200">
                <h2 class="text-xl font-bold">Customer Information</h2>
                <p class="text-sm text-slate-500 mt-1">
                    Customer contact and shipping details.
                </p>
            </div>

            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5 text-sm">

                <div>
                    <p class="text-slate-500">Customer Name</p>
                    <p class="font-semibold text-slate-900 mt-1">
                        <?= htmlspecialchars($order['customer_name']) ?>
                    </p>
                </div>

                <div>
                    <p class="text-slate-500">Phone</p>
                    <p class="font-semibold text-slate-900 mt-1">
                        <?= htmlspecialchars($order['customer_phone']) ?>
                    </p>
                </div>

                <div>
                    <p class="text-slate-500">Email</p>
                    <p class="font-semibold text-slate-900 mt-1">
                        <?= htmlspecialchars($order['customer_email']) ?>
                    </p>
                </div>

                <div>
                    <p class="text-slate-500">City</p>
                    <p class="font-semibold text-slate-900 mt-1">
                        <?= htmlspecialchars($order['city'] ?? 'N/A') ?>
                    </p>
                </div>

                <div>
                    <p class="text-slate-500">Postal Code</p>
                    <p class="font-semibold text-slate-900 mt-1">
                        <?= htmlspecialchars($order['postal_code'] ?? 'N/A') ?>
                    </p>
                </div>

                <div>
                    <p class="text-slate-500">Order Date</p>
                    <p class="font-semibold text-slate-900 mt-1">
                        <?= date('d M Y, h:i A', strtotime($order['created_at'])) ?>
                    </p>
                </div>

                <div class="md:col-span-2">
                    <p class="text-slate-500">Shipping Address</p>
                    <p class="font-semibold text-slate-900 mt-1 leading-6">
                        <?= nl2br(htmlspecialchars($order['shipping_address'])) ?>
                    </p>
                </div>

            </div>

        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

            <div class="px-6 py-5 border-b border-slate-200">
                <h2 class="text-xl font-bold">Ordered Items</h2>
                <p class="text-sm text-slate-500 mt-1">
                    Products included in this order.
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">

                    <thead class="bg-slate-50 text-slate-600">
                        <tr>
                            <th class="px-6 py-4 text-left font-semibold">Product</th>
                            <th class="px-6 py-4 text-left font-semibold">SKU</th>
                            <th class="px-6 py-4 text-left font-semibold">Color</th>
                            <th class="px-6 py-4 text-center font-semibold">Qty</th>
                            <th class="px-6 py-4 text-right font-semibold">Price</th>
                            <th class="px-6 py-4 text-right font-semibold">Subtotal</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($orderItems as $item): ?>
                            <tr class="hover:bg-slate-50 transition">

                                <td class="px-6 py-4">
                                    <p class="font-semibold text-slate-900">
                                        <?= htmlspecialchars($item['product_title']) ?>
                                    </p>
                                </td>

                                <td class="px-6 py-4 text-slate-600">
                                    <?= htmlspecialchars($item['sku']) ?>
                                </td>

                                <td class="px-6 py-4 text-slate-600">
                                    <?= htmlspecialchars($item['color_name'] ?? 'N/A') ?>
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-slate-100 font-semibold">
                                        <?= (int)$item['quantity'] ?>
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-right text-slate-700">
                                    $<?= number_format((float)$item['price'], 2) ?>
                                </td>

                                <td class="px-6 py-4 text-right font-bold text-slate-900">
                                    $<?= number_format((float)$item['subtotal'], 2) ?>
                                </td>

                            </tr>
                        <?php endforeach; ?>
                    </tbody>

                </table>
            </div>

        </div>

    </section>

    <aside class="xl:col-span-4">

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden sticky top-20">

            <div class="px-6 py-5 border-b border-slate-200">
                <h2 class="text-xl font-bold">Order Summary</h2>
                <p class="text-sm text-slate-500 mt-1">
                    Payment and status information.
                </p>
            </div>

            <div class="p-6 space-y-6">

                <div>
                    <span class="inline-flex items-center rounded-full border px-4 py-2 text-sm font-semibold <?= adminOrderStatusClass($order['order_status']) ?>">
                        <?= ucfirst(htmlspecialchars($order['order_status'])) ?>
                    </span>
                </div>

                <div class="space-y-3 text-sm">

                    <div class="flex justify-between">
                        <span class="text-slate-500">Subtotal</span>
                        <span class="font-semibold">
                            $<?= number_format((float)$order['subtotal'], 2) ?>
                        </span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-slate-500">Delivery Charge</span>
                        <span class="font-semibold">
                            <?= (float)$order['delivery_charge'] > 0
                                ? '$' . number_format((float)$order['delivery_charge'], 2)
                                : 'Free'
                            ?>
                        </span>
                    </div>

                    <div class="border-t border-slate-200 pt-3 flex justify-between text-lg">
                        <span class="font-bold">Total</span>
                        <span class="font-bold text-orange-600">
                            $<?= number_format((float)$order['total_amount'], 2) ?>
                        </span>
                    </div>

                </div>

                <div class="border-t border-slate-200 pt-5 space-y-3 text-sm">

                    <div class="flex justify-between">
                        <span class="text-slate-500">Payment Method</span>
                        <span class="font-semibold">
                            <?= strtoupper(htmlspecialchars($order['payment_method'])) ?>
                        </span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-slate-500">Payment Status</span>
                        <span class="font-semibold">
                            <?= ucfirst(htmlspecialchars($order['payment_status'])) ?>
                        </span>
                    </div>

                </div>

                <form action="order-status-update.php" method="POST" class="border-t border-slate-200 pt-5 space-y-3">

                    <input type="hidden" name="order_id" value="<?= (int)$order['id'] ?>">

                    <input type="hidden"
                           name="redirect_to"
                           value="order-view.php?order_number=<?= urlencode($order['order_number']) ?>">

                    <label class="block text-sm font-semibold text-slate-700">
                        Update Order Status
                    </label>

                    <select name="order_status"
                            class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-orange-400">

                        <?php foreach ($statuses as $status): ?>
                            <option value="<?= $status ?>" <?= $order['order_status'] === $status ? 'selected' : '' ?>>
                                <?= ucfirst($status) ?>
                            </option>
                        <?php endforeach; ?>

                    </select>

                    <button type="submit"
                            class="w-full rounded-xl bg-slate-900 hover:bg-slate-700 text-white px-5 py-3 text-sm font-semibold">
                        Update Status
                    </button>

                </form>

                <a href="../order-invoice.php?order_number=<?= urlencode($order['order_number']) ?>"
                   class="block text-center w-full rounded-xl bg-orange-500 hover:bg-orange-600 text-white px-5 py-3 text-sm font-semibold">
                    View / Print Invoice
                </a>

            </div>

        </div>

    </aside>

</div>

<?php include __DIR__ . '/../../includes/admin-footer.php'; ?>