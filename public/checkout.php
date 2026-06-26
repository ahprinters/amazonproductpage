<?php

require_once '../classes/Database.php';
require_once '../classes/Cart.php';
require_once '../classes/Variant.php';
require_once '../classes/Flash.php';

$conn = Database::connect();

$cart = new Cart();
$cartData = $cart->all();

if (empty($cartData)) {
    Flash::set('error', 'Your cart is empty. Please add a product before checkout.');
    header("Location: cart.php");
    exit;
}

$variantModel = new Variant($conn);

$cartItems = [];
$subtotal = 0;
$deliveryCharge = 0;

foreach ($cartData as $cartKey => $item) {
    $productData = $variantModel->getCartVariant(
        (int)$item['product_id'],
        $item['sku']
    );

    if ($productData) {
        $itemSubtotal = (float)$item['price'] * (int)$item['quantity'];
        $subtotal += $itemSubtotal;

        $cartItems[] = [
            'key' => $cartKey,
            'product_id' => (int)$item['product_id'],
            'title' => $productData['title'],
            'sku' => $productData['sku'],
            'image' => $productData['image'],
            'color' => $productData['color_name'],
            'price' => (float)$item['price'],
            'quantity' => (int)$item['quantity'],
            'subtotal' => $itemSubtotal
        ];
    }
}

$total = $subtotal + $deliveryCharge;
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/navbar.php'; ?>
<?php include '../includes/flash-message.php'; ?>

<main class="max-w-7xl mx-auto px-4 py-8">

    <h1 class="text-3xl font-bold mb-6">Checkout</h1>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Checkout Form -->
        <section class="lg:col-span-8">

            <form action="checkout-process.php" method="POST" class="border rounded-lg p-6 bg-white space-y-5">

                <h2 class="text-xl font-bold">Customer Information</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <div>
                        <label class="block text-sm font-medium mb-1">
                            Full Name
                        </label>
                        <input 
                            type="text" 
                            name="customer_name" 
                            required
                            class="w-full border rounded-md px-3 py-2 text-sm"
                            placeholder="Enter your full name"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">
                            Phone Number
                        </label>
                        <input 
                            type="text" 
                            name="customer_phone" 
                            required
                            class="w-full border rounded-md px-3 py-2 text-sm"
                            placeholder="Enter your phone number"
                        >
                    </div>

                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">
                        Email Address
                    </label>
                    <input 
                        type="email" 
                        name="customer_email" 
                        required
                        class="w-full border rounded-md px-3 py-2 text-sm"
                        placeholder="Enter your email address"
                    >
                </div>

                <h2 class="text-xl font-bold pt-4 border-t">Shipping Address</h2>

                <div>
                    <label class="block text-sm font-medium mb-1">
                        Full Address
                    </label>
                    <textarea 
                        name="shipping_address" 
                        rows="4"
                        required
                        class="w-full border rounded-md px-3 py-2 text-sm"
                        placeholder="House, road, area, district"
                    ></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <div>
                        <label class="block text-sm font-medium mb-1">
                            City
                        </label>
                        <input 
                            type="text" 
                            name="city"
                            class="w-full border rounded-md px-3 py-2 text-sm"
                            placeholder="City"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">
                            Postal Code
                        </label>
                        <input 
                            type="text" 
                            name="postal_code"
                            class="w-full border rounded-md px-3 py-2 text-sm"
                            placeholder="Postal code"
                        >
                    </div>

                </div>

                <h2 class="text-xl font-bold pt-4 border-t">Payment Method</h2>

                <div class="border rounded-md p-4">
                    <label class="flex items-center gap-2 text-sm">
                        <input type="radio" name="payment_method" value="cod" checked>
                        <span>Cash on Delivery</span>
                    </label>
                </div>

                <button 
                    type="submit"
                    class="w-full bg-orange-500 hover:bg-orange-600 text-white rounded-full py-3 font-semibold"
                >
                    Place Order
                </button>

            </form>

        </section>

        <!-- Order Summary -->
        <aside class="lg:col-span-4">
            <div class="border rounded-lg p-5 bg-white space-y-4 sticky top-4">

                <h2 class="text-xl font-bold">Order Summary</h2>

                <div class="space-y-4">
                    <?php foreach ($cartItems as $cartItem): ?>
                        <div class="flex gap-3 border-b pb-3">

                            <img 
                                src="../assets/images/<?= htmlspecialchars($cartItem['image'] ?? 'placeholder.jpg') ?>"
                                onerror="this.src='../assets/images/placeholder.jpg'"
                                class="w-16 h-16 object-cover rounded"
                                alt="<?= htmlspecialchars($cartItem['title']) ?>"
                            >

                            <div class="flex-1">
                                <p class="text-sm font-semibold leading-snug">
                                    <?= htmlspecialchars($cartItem['title']) ?>
                                </p>

                                <p class="text-xs text-gray-600">
                                    <?= htmlspecialchars($cartItem['color'] ?? 'N/A') ?> · Qty: <?= (int)$cartItem['quantity'] ?>
                                </p>

                                <p class="text-sm font-semibold mt-1">
                                    $<?= number_format($cartItem['subtotal'], 2) ?>
                                </p>
                            </div>

                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="flex justify-between text-sm">
                    <span>Subtotal</span>
                    <span>$<?= number_format($subtotal, 2) ?></span>
                </div>

                <div class="flex justify-between text-sm">
                    <span>Delivery</span>
                    <span><?= $deliveryCharge > 0 ? '$' . number_format($deliveryCharge, 2) : 'Free' ?></span>
                </div>

                <hr>

                <div class="flex justify-between text-lg font-bold">
                    <span>Total</span>
                    <span>$<?= number_format($total, 2) ?></span>
                </div>

            </div>
        </aside>

    </div>

</main>

<?php include '../includes/footer.php'; ?>