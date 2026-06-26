<?php
$activePrice = (float)($defaultVariant['price'] ?? $product['sale_price'] ?? $product['price']);
$activeOldPrice = (float)($defaultVariant['old_price'] ?? $product['old_price'] ?? $product['price']);
$activeStock = (int)($defaultVariant['stock'] ?? $product['stock']);
$activeSku = $defaultVariant['sku'] ?? '';
?>

<form action="../public/cart-add.php" method="POST" class="border rounded-lg p-5 space-y-4 bg-white">

    <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['id']) ?>">
    <input type="hidden" name="redirect_url" value="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">

    <input type="hidden" name="variant_sku" id="cartVariantSku" value="<?= htmlspecialchars($activeSku) ?>">

    <input type="hidden" name="variant_price" id="cartVariantPrice" value="<?= htmlspecialchars($activePrice) ?>">

    <div>
        <span class="text-sm">$</span>

        <span id="buyBoxPrice" class="text-3xl font-semibold">
            <?= number_format($activePrice, 2) ?>
        </span>

        <p id="buyBoxOldPrice" class="text-sm text-gray-500 line-through <?= $activeOldPrice > $activePrice ? '' : 'hidden' ?>">
            $<?= number_format($activeOldPrice, 2) ?>
        </p>
    </div>

    <p id="stockText" class="<?= $activeStock > 0 ? 'text-green-700' : 'text-red-600' ?> font-semibold">
        <?= $activeStock > 0 ? 'In Stock' : 'Out of Stock' ?>
    </p>

    <div class="text-sm text-gray-700 space-y-1">
        <p>🚚 Free delivery</p>
        <p>📦 Delivery: <span class="font-semibold">3–5 business days</span></p>
        <p>🔒 Secure transaction</p>
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Quantity</label>

        <select name="quantity" class="w-full border rounded-md px-3 py-2 text-sm bg-gray-50">
            <?php for ($i = 1; $i <= 10; $i++): ?>
                <option value="<?= $i ?>"><?= $i ?></option>
            <?php endfor; ?>
        </select>
    </div>

    <button
        type="submit"
        class="w-full bg-yellow-400 hover:bg-yellow-500 rounded-full py-2 font-semibold <?= $activeStock > 0 ? '' : 'opacity-50 cursor-not-allowed' ?>"
        <?= $activeStock > 0 ? '' : 'disabled' ?>
    >
        Add to Cart
    </button>

    <button
        type="button"
        class="w-full bg-orange-500 hover:bg-orange-600 text-white rounded-full py-2 font-semibold <?= $activeStock > 0 ? '' : 'opacity-50 cursor-not-allowed' ?>"
        <?= $activeStock > 0 ? '' : 'disabled' ?>
    >
        Buy Now
    </button>

    <div class="text-xs text-gray-500 pt-2 border-t">
        Secure payment · Easy returns · Customer support available
    </div>

</form>