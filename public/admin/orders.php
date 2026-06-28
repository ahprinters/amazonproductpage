<?php

require_once __DIR__ . '/../../classes/Database.php';
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../classes/Order.php';
require_once __DIR__ . '/../../classes/Flash.php';

$conn = Database::connect();

$auth = new Auth($conn);
$auth->requireAdmin();

$admin = $auth->admin();

$orderModel = new Order($conn);
$orders = $orderModel->getAllOrders();

function orderStatusClass($status)
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

$statuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];

?>

<?php include __DIR__ . '/../../includes/admin-header.php'; ?>
<?php include __DIR__ . '/../../includes/flash-message.php'; ?>

<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
    <div>
        <h1 class="text-3xl font-bold text-slate-900">Orders</h1>
        <p class="text-slate-500 mt-1">
            View, manage, and update customer orders.
        </p>
    </div>

    <a href="dashboard.php"
       class="inline-flex items-center justify-center rounded-xl bg-slate-900 hover:bg-slate-700 text-white px-5 py-2.5 text-sm font-semibold">
        Back to Dashboard
    </a>
</div>

<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

    <div class="px-6 py-5 border-b border-slate-200 flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold">All Orders</h2>
            <p class="text-sm text-slate-500 mt-1">
                Total <?= count($orders) ?> order<?= count($orders) === 1 ? '' : 's' ?> found.
            </p>
        </div>
    </div>

    <?php if (empty($orders)): ?>

        <div class="p-10 text-center">
            <div class="text-5xl mb-4">🛒</div>
            <h3 class="text-xl font-bold">No orders found</h3>
            <p class="text-slate-500 mt-2">
                When customers place orders, they will appear here.
            </p>
        </div>

    <?php else: ?>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="px-6 py-4 text-left font-semibold">Order</th>
                        <th class="px-6 py-4 text-left font-semibold">Customer</th>
                        <th class="px-6 py-4 text-left font-semibold">Phone</th>
                        <th class="px-6 py-4 text-right font-semibold">Total</th>
                        <th class="px-6 py-4 text-left font-semibold">Payment</th>
                        <th class="px-6 py-4 text-left font-semibold">Status</th>
                        <th class="px-6 py-4 text-left font-semibold">Date</th>
                        <th class="px-6 py-4 text-right font-semibold">Action</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    <?php foreach ($orders as $order): ?>

                        <tr class="hover:bg-slate-50 transition">

                            <td class="px-6 py-4">
                                <p class="font-bold text-slate-900">
                                    <?= htmlspecialchars($order['order_number']) ?>
                                </p>
                                <p class="text-xs text-slate-500">
                                    ID: <?= (int)$order['id'] ?>
                                </p>
                            </td>

                            <td class="px-6 py-4">
                                <p class="font-semibold text-slate-900">
                                    <?= htmlspecialchars($order['customer_name']) ?>
                                </p>
                                <p class="text-xs text-slate-500">
                                    <?= htmlspecialchars($order['customer_email']) ?>
                                </p>
                            </td>

                            <td class="px-6 py-4 text-slate-700">
                                <?= htmlspecialchars($order['customer_phone']) ?>
                            </td>

                            <td class="px-6 py-4 text-right">
                                <span class="font-bold text-slate-900">
                                    $<?= number_format((float)$order['total_amount'], 2) ?>
                                </span>
                            </td>

                            <td class="px-6 py-4">
                                <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">
                                    <?= strtoupper(htmlspecialchars($order['payment_method'])) ?>
                                </span>
                            </td>

                            <td class="px-6 py-4">
                                <form action="order-status-update.php" method="POST" class="flex items-center gap-2">
                                    <input type="hidden" name="order_id" value="<?= (int)$order['id'] ?>">

                                    <select name="order_status"
                                            class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-orange-400">
                                        <?php foreach ($statuses as $status): ?>
                                            <option value="<?= $status ?>" <?= $order['order_status'] === $status ? 'selected' : '' ?>>
                                                <?= ucfirst($status) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>

                                    <button type="submit"
                                            class="rounded-lg bg-slate-900 hover:bg-slate-700 text-white px-3 py-2 text-xs font-semibold">
                                        Save
                                    </button>
                                </form>

                                <div class="mt-2">
                                    <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold <?= orderStatusClass($order['order_status']) ?>">
                                        <?= ucfirst(htmlspecialchars($order['order_status'])) ?>
                                    </span>
                                </div>
                            </td>

                            <td class="px-6 py-4 text-slate-600">
                                <?= date('d M Y', strtotime($order['created_at'])) ?>
                                <p class="text-xs text-slate-400">
                                    <?= date('h:i A', strtotime($order['created_at'])) ?>
                                </p>
                            </td>

                            <td class="px-6 py-4 text-right">
                                <a href="order-view.php?order_number=<?= urlencode($order['order_number']) ?>"
                                   class="inline-flex items-center rounded-lg bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 text-xs font-semibold">
                                    View
                                </a>
                            </td>

                        </tr>

                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    <?php endif; ?>

</div>

<?php include __DIR__ . '/../../includes/admin-footer.php'; ?>