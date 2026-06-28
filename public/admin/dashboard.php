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
$stats = $orderModel->getDashboardStats();

?>

<?php include __DIR__ . '/../../includes/admin-header.php'; ?>
<?php include __DIR__ . '/../../includes/flash-message.php'; ?>

<div class="mb-8">
    <h1 class="text-3xl font-bold text-slate-900">Dashboard</h1>
    <p class="text-slate-500 mt-1">
        Welcome back, <?= htmlspecialchars($admin['name']) ?>. Here is your store overview.
    </p>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-5 gap-5 mb-8">

    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-slate-500">Total Orders</p>
                <h2 class="text-3xl font-bold mt-2">
                    <?= number_format($stats['total_orders']) ?>
                </h2>
            </div>

            <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center text-2xl">
                🛒
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-slate-500">Pending</p>
                <h2 class="text-3xl font-bold mt-2">
                    <?= number_format($stats['pending_orders']) ?>
                </h2>
            </div>

            <div class="w-12 h-12 rounded-xl bg-yellow-100 flex items-center justify-center text-2xl">
                ⏳
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-slate-500">Processing</p>
                <h2 class="text-3xl font-bold mt-2">
                    <?= number_format($stats['processing_orders']) ?>
                </h2>
            </div>

            <div class="w-12 h-12 rounded-xl bg-purple-100 flex items-center justify-center text-2xl">
                🔄
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-slate-500">Delivered</p>
                <h2 class="text-3xl font-bold mt-2">
                    <?= number_format($stats['delivered_orders']) ?>
                </h2>
            </div>

            <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center text-2xl">
                ✅
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-slate-500">Revenue</p>
                <h2 class="text-3xl font-bold mt-2">
                    $<?= number_format($stats['total_revenue'], 2) ?>
                </h2>
            </div>

            <div class="w-12 h-12 rounded-xl bg-orange-100 flex items-center justify-center text-2xl">
                💰
            </div>
        </div>
    </div>

</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
        <h2 class="text-xl font-bold mb-4">Quick Actions</h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

            <a href="orders.php"
               class="rounded-2xl border border-slate-200 p-5 hover:border-orange-400 hover:shadow-md transition block">
                <div class="text-3xl mb-3">🛒</div>
                <h3 class="font-bold text-lg">Manage Orders</h3>
                <p class="text-sm text-slate-500 mt-1">
                    View orders, update status, and print invoices.
                </p>
            </a>

            <a href="#"
               class="rounded-2xl border border-slate-200 p-5 opacity-60 cursor-not-allowed block">
                <div class="text-3xl mb-3">📦</div>
                <h3 class="font-bold text-lg">Manage Products</h3>
                <p class="text-sm text-slate-500 mt-1">
                    Product CRUD will be added next.
                </p>
            </a>

        </div>
    </div>

    <div class="bg-slate-900 text-white rounded-2xl p-6 shadow-sm">
        <h2 class="text-xl font-bold">Admin System</h2>

        <div class="mt-5 space-y-4 text-sm">
            <div class="flex justify-between border-b border-slate-700 pb-3">
                <span class="text-slate-300">Login</span>
                <span class="font-semibold text-green-400">Active</span>
            </div>

            <div class="flex justify-between border-b border-slate-700 pb-3">
                <span class="text-slate-300">Protected Pages</span>
                <span class="font-semibold text-green-400">Enabled</span>
            </div>

            <div class="flex justify-between border-b border-slate-700 pb-3">
                <span class="text-slate-300">Order Module</span>
                <span class="font-semibold text-green-400">Ready</span>
            </div>

            <div class="flex justify-between">
                <span class="text-slate-300">Product Module</span>
                <span class="font-semibold text-yellow-400">Next</span>
            </div>
        </div>
    </div>

</div>

<?php include __DIR__ . '/../../includes/admin-footer.php'; ?>