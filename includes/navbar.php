<?php
require_once __DIR__ . '/../classes/Cart.php';

$navbarCart = new Cart();
$cartCount = $navbarCart->count();
?>

<header class="bg-gray-900 text-white">
    <div class="max-w-7xl mx-auto px-4 py-3 flex items-center gap-4">

        <a href="index.php" class="text-2xl font-bold">
            MyStore
        </a>

        <div class="hidden md:block text-sm leading-tight">
            <span class="block text-gray-300">Deliver to</span>
            <strong>Bangladesh</strong>
        </div>

        <form class="flex-1 flex">
            <select class="bg-gray-100 text-gray-700 px-3 rounded-l-md text-sm border-r">
                <option>All</option>
                <option>Furniture</option>
                <option>Electronics</option>
                <option>Home</option>
            </select>

            <input
                type="text"
                placeholder="Search products"
                class="w-full px-4 py-2 text-gray-900 outline-none"
            >

            <button
                type="submit"
                class="bg-yellow-400 hover:bg-yellow-500 text-gray-900 px-5 rounded-r-md font-semibold"
            >
                Search
            </button>
        </form>

        <div class="hidden md:block text-sm">
            <span class="block text-gray-300">Hello, Sign in</span>
            <strong>Account</strong>
        </div>

        <a href="cart.php" class="relative font-bold flex items-center gap-1">
            <span>Cart</span>

            <?php if ($cartCount > 0): ?>
                <span class="absolute -top-3 -right-3 bg-yellow-400 text-gray-900 text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center">
                    <?= $cartCount ?>
                </span>
            <?php endif; ?>
        </a>

    </div>
</header>