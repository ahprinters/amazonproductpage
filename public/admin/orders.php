<?php

require_once '../../classes/Database.php';
require_once '../../classes/Auth.php';
require_once '../../classes/Order.php';
require_once '../../classes/Flash.php';

$conn = Database::connect();

$auth = new Auth($conn);
$auth->requireAdmin();

$admin = $auth->admin();

$orderModel = new Order($conn);
$orders = $orderModel->getAllOrders();
?>

<?php include '../../includes/header.php'; ?>
<?php include '../../includes/flash-message.php'; ?>

<main class="max-w-7xl mx-auto px-4 py-8">

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold">Admin Orders</h1>
            <p class="text-sm text-gray-600 mt-1">
                Manage customer orders from here.
            </p>
        </div>

        <div class="flex gap-2">
            <a href="../product.php"
            class="inline-block bg-gray-200 hover:bg-gray-300 px-5 py-2 rounded-full font-semibold">
                Back to Store
            </a>

            <a href="logout.php"
            class="inline-block bg-red-500 hover:bg-red-600 text-white px-5 py-2 rounded-full font-semibold">
                Logout
            </a>
        </div>
    </div>

    <?php if (empty($orders)): ?>

        <div class="border rounded-lg p-8 text-center bg-white">
            <p class="text-lg font-semibold">No orders found.</p>
        </div>

    <?php else: ?>

        <div class="overflow-x-auto border rounded-lg bg-white">

            <table class="w-full text-sm">
                <thead class="bg-gray-100 text-left">
                    <tr>
                        <th class="p-3 border-b">Order Number</th>
                        <th class="p-3 border-b">Customer</th>
                        <th class="p-3 border-b">Phone</th>
                        <th class="p-3 border-b">Total</th>
                        <th class="p-3 border-b">Payment</th>
                        <th class="p-3 border-b">Order Status</th>
                        <th class="p-3 border-b">Date</th>
                        <th class="p-3 border-b text-right">Action</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="p-3 border-b font-semibold">
                                <?= htmlspecialchars($order['order_number']) ?>
                            </td>

                            <td class="p-3 border-b">
                                <?= htmlspecialchars($order['customer_name']) ?>
                            </td>

                            <td class="p-3 border-b">
                                <?= htmlspecialchars($order['customer_phone']) ?>
                            </td>

                            <td class="p-3 border-b font-semibold">
                                $<?= number_format((float)$order['total_amount'], 2) ?>
                            </td>

                            <td class="p-3 border-b">
                                <?= strtoupper(htmlspecialchars($order['payment_method'])) ?>
                            </td>

                            <td class="p-3 border-b">
                                <form action="order-status-update.php" method="POST" class="flex items-center gap-2">
                                    <input type="hidden" name="order_id" value="<?= (int)$order['id'] ?>">

                                    <select name="order_status" class="border rounded-md px-2 py-1 text-xs">
                                        <?php
                                            $statuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
                                        ?>

                                        <?php foreach ($statuses as $status): ?>
                                            <option value="<?= $status ?>" <?= $order['order_status'] === $status ? 'selected' : '' ?>>
                                                <?= ucfirst($status) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>

                                    <button type="submit"
                                            class="bg-gray-900 text-white px-3 py-1 rounded-md text-xs hover:bg-gray-700">
                                        Update
                                    </button>
                                </form>
                            </td>

                            <td class="p-3 border-b">
                                <?= date('d M Y', strtotime($order['created_at'])) ?>
                            </td>

                            <td class="p-3 border-b text-right">
                                <a href="order-view.php?order_number=<?= urlencode($order['order_number']) ?>"
                                    class="text-blue-600 hover:text-orange-600 font-semibold">
                                    View
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        </div>

    <?php endif; ?>

</main>

<?php include '../../includes/footer.php'; ?>