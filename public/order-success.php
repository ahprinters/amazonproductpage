<?php
require_once '../classes/Flash.php';

$orderNumber = $_GET['order_number'] ?? null;
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/navbar.php'; ?>
<?php include '../includes/flash-message.php'; ?>

<main class="max-w-3xl mx-auto px-4 py-12">

    <div class="border rounded-lg p-8 bg-white text-center">

        <div class="text-5xl mb-4">✅</div>

        <h1 class="text-3xl font-bold mb-3">
            Order Placed Successfully
        </h1>

        <?php if ($orderNumber): ?>
            <p class="text-gray-700 mb-2">
                Your order number is:
                <strong><?= htmlspecialchars($orderNumber) ?></strong>
            </p>
        <?php else: ?>
            <p class="text-red-600 mb-2">
                Order number missing.
            </p>
        <?php endif; ?>

        <p class="text-gray-600 mb-6">
            Thank you for shopping with us. We will contact you soon to confirm your order.
        </p>

        <div class="flex flex-col sm:flex-row gap-3 justify-center">

            <?php if ($orderNumber): ?>
                <a href="order-details.php?order_number=<?= urlencode($orderNumber) ?>"
                   class="inline-block bg-orange-500 hover:bg-orange-600 text-white px-6 py-2 rounded-full font-semibold">
                    View Order Details
                </a>
            <?php endif; ?>

            <a href="product.php"
               class="inline-block bg-yellow-400 hover:bg-yellow-500 px-6 py-2 rounded-full font-semibold">
                Continue Shopping
            </a>

        </div>

    </div>

</main>

<?php include '../includes/footer.php'; ?>