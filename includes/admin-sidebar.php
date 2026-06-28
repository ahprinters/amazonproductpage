<?php
$currentPage = basename($_SERVER['PHP_SELF']);

function adminMenuItemClass($active)
{
    return $active
        ? 'bg-orange-500 text-white'
        : 'text-slate-300 hover:bg-slate-800 hover:text-white';
}
?>

<aside class="w-64 bg-slate-900 text-white hidden md:flex md:flex-col">

    <div class="h-16 flex items-center px-6 border-b border-slate-800">
        <div>
            <h2 class="text-xl font-bold">Admin Panel</h2>
            <p class="text-xs text-slate-400">Tailwind Dashboard</p>
        </div>
    </div>

    <nav class="flex-1 px-4 py-6 space-y-2">

        <a href="dashboard.php"
           class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition <?= adminMenuItemClass($currentPage === 'dashboard.php') ?>">
            <span>📊</span>
            <span>Dashboard</span>
        </a>

        <a href="orders.php"
           class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition <?= adminMenuItemClass(in_array($currentPage, ['orders.php', 'order-view.php'])) ?>">
            <span>🛒</span>
            <span>Orders</span>
        </a>

        <a href="products.php"
            class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition <?= adminMenuItemClass(in_array($currentPage, ['products.php', 'product-create.php', 'product-edit.php', 'product-variants.php'])) ?>">
            <span>📦</span>
            <span>Products</span>
        </a>

        <a href="#"
           class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold text-slate-500 cursor-not-allowed">
            <span>👥</span>
            <span>Customers</span>
        </a>

        <a href="#"
           class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold text-slate-500 cursor-not-allowed">
            <span>⚙️</span>
            <span>Settings</span>
        </a>

    </nav>

    <div class="p-4 border-t border-slate-800">
        <div class="rounded-xl bg-slate-800 p-4">
            <p class="text-sm font-semibold">Store Status</p>
            <p class="text-xs text-slate-400 mt-1">Admin system active</p>
        </div>
    </div>

</aside>

<!-- Mobile Sidebar-->
<div id="mobileSidebar" class="fixed inset-0 z-50 hidden md:hidden">

    <div
        onclick="closeMobileSidebar()"
        class="absolute inset-0 bg-slate-900/60"
    ></div>

    <aside class="relative w-72 max-w-[85%] h-full bg-slate-900 text-white flex flex-col shadow-2xl">

        <div class="h-16 flex items-center justify-between px-5 border-b border-slate-800">

            <div>
                <h2 class="text-xl font-bold">Admin Panel</h2>
                <p class="text-xs text-slate-400">Mobile Menu</p>
            </div>

            <button
                type="button"
                onclick="closeMobileSidebar()"
                class="w-9 h-9 rounded-xl bg-slate-800 hover:bg-slate-700 flex items-center justify-center text-xl"
            >
                ×
            </button>

        </div>

        <nav class="flex-1 px-4 py-6 space-y-2">

            <a href="dashboard.php"
               class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition <?= adminMenuItemClass($currentPage === 'dashboard.php') ?>">
                <span>📊</span>
                <span>Dashboard</span>
            </a>

            <a href="orders.php"
               class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition <?= adminMenuItemClass(in_array($currentPage, ['orders.php', 'order-view.php'])) ?>">
                <span>🛒</span>
                <span>Orders</span>
            </a>

            <a href="products.php"
            class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition <?= adminMenuItemClass(in_array($currentPage, ['products.php', 'product-create.php', 'product-edit.php', 'product-variants.php'])) ?>">
                <span>📦</span>
                <span>Products</span>
            </a>

            <a href="../product.php"
               class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold text-slate-300 hover:bg-slate-800 hover:text-white">
                <span>🏬</span>
                <span>View Store</span>
            </a>

            <a href="logout.php"
               class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold text-red-300 hover:bg-red-500 hover:text-white">
                <span>🚪</span>
                <span>Logout</span>
            </a>

        </nav>

        <div class="p-4 border-t border-slate-800">
            <div class="rounded-xl bg-slate-800 p-4">
                <p class="text-sm font-semibold">Store Status</p>
                <p class="text-xs text-slate-400 mt-1">Admin system active</p>
            </div>
        </div>

    </aside>

</div>