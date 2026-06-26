<?php

require_once '../classes/Database.php';
require_once '../classes/Cart.php';
require_once '../classes/Variant.php';

$conn = Database::connect();

$cart = new Cart();
$cartData = $cart->all();

$variantModel = new Variant($conn);

$cartItems = [];
$total = 0;

foreach ($cartData as $cartKey => $item) {
    $productData = $variantModel->getCartVariant(
        (int)$item['product_id'],
        $item['sku']
    );

    if ($productData) {
        $subtotal = (float)$item['price'] * (int)$item['quantity'];
        $total += $subtotal;

        $cartItems[] = [
            'key' => $cartKey,
            'title' => $productData['title'],
            'sku' => $productData['sku'],
            'image' => $productData['image'],
            'color' => $productData['color_name'],
            'price' => (float)$item['price'],
            'quantity' => (int)$item['quantity'],
            'subtotal' => $subtotal
        ];
    }
}
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/navbar.php'; ?>
<?php include '../includes/flash-message.php'; ?>

<main class="max-w-7xl mx-auto px-4 py-8">

    <h1 class="text-3xl font-bold mb-6">Shopping Cart</h1>

    <?php if (empty($cartItems)): ?>

        <div class="border rounded-lg p-8 text-center">
            <p class="text-lg font-semibold">Your cart is empty.</p>

            <a href="product.php" class="inline-block mt-4 bg-yellow-400 hover:bg-yellow-500 px-6 py-2 rounded-full font-semibold">
                Continue Shopping
            </a>
        </div>

    <?php else: ?>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            <section class="lg:col-span-8 space-y-4">

                <?php foreach ($cartItems as $cartItem): ?>
                    <div class="border rounded-lg p-4 flex gap-4 bg-white">

                        <img 
                            src="../assets/images/<?= htmlspecialchars($cartItem['image'] ?? 'placeholder.jpg') ?>"
                            onerror="this.src='../assets/images/placeholder.jpg'"
                            class="w-28 h-28 object-cover rounded"
                            alt="<?= htmlspecialchars($cartItem['title']) ?>"
                        >

                        <div class="flex-1">
                            <h2 class="font-semibold text-lg">
                                <?= htmlspecialchars($cartItem['title']) ?>
                            </h2>

                            <p class="text-sm text-gray-600">
                                SKU: <?= htmlspecialchars($cartItem['sku']) ?>
                            </p>

                            <p class="text-sm text-gray-600">
                                Color: <?= htmlspecialchars($cartItem['color'] ?? 'N/A') ?>
                            </p>

                            <form action="cart-update.php" method="POST" class="mt-2 flex items-center gap-2">
                                <input type="hidden" name="key" value="<?= htmlspecialchars($cartItem['key']) ?>">

                                <label class="text-sm text-gray-600">
                                    Quantity:
                                </label>

                                <select name="quantity" class="border rounded-md px-2 py-1 text-sm">
                                    <?php for ($i = 1; $i <= 10; $i++): ?>
                                        <option value="<?= $i ?>" <?= (int)$cartItem['quantity'] === $i ? 'selected' : '' ?>>
                                            <?= $i ?>
                                        </option>
                                    <?php endfor; ?>
                                </select>

                                <button type="submit" class="text-sm bg-gray-200 hover:bg-gray-300 px-3 py-1 rounded-md">
                                    Update
                                </button>
                            </form>

                            <a href="cart-remove.php?key=<?= urlencode($cartItem['key']) ?>"
                               class="inline-block mt-1 text-sm text-red-600 hover:text-red-800 hover:underline">
                                Remove
                            </a>

                            <p class="font-semibold mt-2">
                                $<?= number_format($cartItem['price'], 2) ?>
                            </p>
                        </div>

                        <div class="font-bold">
                            $<?= number_format($cartItem['subtotal'], 2) ?>
                        </div>

                    </div>
                <?php endforeach; ?>

            </section>

            <aside class="lg:col-span-4">
                <div class="border rounded-lg p-5 bg-white space-y-4">
                    <h2 class="text-xl font-bold">Order Summary</h2>

                    <div class="flex justify-between text-sm">
                        <span>Subtotal</span>
                        <span>$<?= number_format($total, 2) ?></span>
                    </div>

                    <div class="flex justify-between text-sm">
                        <span>Delivery</span>
                        <span>Free</span>
                    </div>

                    <hr>

                    <div class="flex justify-between text-lg font-bold">
                        <span>Total</span>
                        <span>$<?= number_format($total, 2) ?></span>
                    </div>

                    <a href="checkout.php"
                        class="block text-center w-full bg-orange-500 hover:bg-orange-600 text-white rounded-full py-2 font-semibold">
                        Proceed to Checkout
                    </a>

                    <a href="cart-clear.php"
                    onclick="return confirm('Are you sure you want to clear the cart?')"
                    class="block text-center w-full bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-full py-2 font-semibold">
                        Clear Cart
                    </a>
                </div>
            </aside>

        </div>

    <?php endif; ?>

</main>

<?php include '../includes/footer.php'; ?>