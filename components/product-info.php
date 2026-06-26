
<?php
if (!isset($product) || !is_array($product)) {
    die("Product data not found.");
}

$regularPrice = (float) $product['price'];
$salePrice = !empty($product['sale_price']) ? (float) $product['sale_price'] : $regularPrice;
$oldPrice = !empty($product['old_price']) ? (float) $product['old_price'] : $regularPrice;

$priceParts = explode('.', number_format($salePrice, 2));
$discount = max(0, $oldPrice - $salePrice);

?>

<div class="space-y-4">

    <h1 class="text-2xl font-semibold leading-snug">
        <?= htmlspecialchars($product['title']) ?>    </h1>

    <p class="text-sm">
        Brand:
        <a href="#" class="text-blue-600 hover:text-orange-600">
            <?= htmlspecialchars($product['brand']) ?>
        </a>
    </p>

    <div class="flex items-center gap-2 text-sm">
        <span class="text-orange-500 font-bold">★★★★☆</span>

        <a href="#reviews" class="text-blue-600 hover:text-orange-600">
            <?= number_format((int)$product['review_count']) ?> ratings
        </a>
    </div>
<!-- Price Section-->
    <?php
    $activePrice = (float)($defaultVariant['price'] ?? $product['sale_price'] ?? $product['price']);
    $activeOldPrice = (float)($defaultVariant['old_price'] ?? $product['old_price'] ?? $product['price']);
    $activeDiscount = max(0, $activeOldPrice - $activePrice);
    $priceParts = explode('.', number_format($activePrice, 2));
    ?>

    <div class="space-y-1">
        <div class="flex items-start gap-1">
            <span class="text-sm mt-1">$</span>

            <span id="priceMain" class="text-3xl font-semibold">
                <?= $priceParts[0] ?>
            </span>

            <span id="priceCents" class="text-sm mt-1">
                <?= $priceParts[1] ?>
            </span>
        </div>

        <p id="oldPriceRow" class="text-sm text-gray-600 <?= $activeOldPrice > $activePrice ? '' : 'hidden' ?>">
            List Price:
            <span id="oldPrice" class="line-through">
                $<?= number_format($activeOldPrice, 2) ?>
            </span>
        </p>

        <p id="discountRow" class="text-sm text-green-700 font-medium <?= $activeDiscount > 0 ? '' : 'hidden' ?>">
            Save $<span id="discountAmount"><?= number_format($activeDiscount, 2) ?></span> with this deal
        </p>
    </div>

    <hr>

    


<?php
    $selectedColor = $defaultVariant['color_name'] ?? $product['color'] ?? 'Default';
?>

<p class="text-sm text-gray-700">
    Color:
    <span id="selectedColor" class="font-semibold text-gray-900">
        <?= htmlspecialchars($selectedColor) ?>
    </span>
</p>
    <input type="hidden" name="variant_sku" id="selectedVariantSku"
        value="<?= htmlspecialchars($defaultVariant['sku'] ?? '') ?>">

    <input type="hidden" name="variant_price" id="selectedVariantPrice"
        value="<?= htmlspecialchars($defaultVariant['price'] ?? $product['sale_price'] ?? $product['price']) ?>">

    <input type="hidden" name="variant_stock" id="selectedVariantStock"
        value="<?= htmlspecialchars($defaultVariant['stock'] ?? $product['stock']) ?>">

<div class="grid grid-cols-3 gap-2">
    <?php foreach ($variants as $index => $variant): ?>
        <button
            type="button"
            class="variation-btn rounded-md px-3 py-2 text-sm text-left hover:border-orange-500 <?= $index === 0 ? 'border-2 border-orange-500' : 'border' ?>"
            onclick="selectVariant(this)"
            data-color="<?= htmlspecialchars($variant['color_name'], ENT_QUOTES) ?>"
            data-price="<?= htmlspecialchars($variant['price']) ?>"
            data-old-price="<?= htmlspecialchars($variant['old_price']) ?>"
            data-stock="<?= htmlspecialchars($variant['stock']) ?>"
            data-image="<?= htmlspecialchars($variant['image'], ENT_QUOTES) ?>"
            data-sku="<?= htmlspecialchars($variant['sku'], ENT_QUOTES) ?>"
        >
            <span class="inline-block w-4 h-4 rounded-full border mr-2 align-middle"
                  style="background-color: <?= htmlspecialchars($variant['color_code']) ?>"></span>
            <?= htmlspecialchars($variant['color_name']) ?>
        </button>
    <?php endforeach; ?>
</div>

<input type="hidden" name="variant_sku" id="selectedVariantSku"
       value="<?= htmlspecialchars($defaultVariant['sku'] ?? '') ?>">

    <script>
    function selectColor(color, element) {
        document.getElementById('selectedColor').innerText = color;

        document.querySelectorAll('.variation-btn').forEach(btn => {
            btn.classList.remove('border-2', 'border-orange-500');
            btn.classList.add('border');
        });

        element.classList.remove('border');
        element.classList.add('border-2', 'border-orange-500');
    }
    </script>

    <hr>

    <div class="mt-6 border-t pt-4">

        <h2 class="text-lg font-bold mb-3">About this item</h2>

        <ul class="space-y-2 text-sm text-gray-700">
            
            <li class="flex gap-2">
                <span>✔</span>
                <span>Built-in charging station with USB ports and AC outlets for convenience.</span>
            </li>

            <li class="flex gap-2">
                <span>✔</span>
                <span>Farmhouse modern design suitable for bedroom, living room, and office.</span>
            </li>

            <li class="flex gap-2">
                <span>✔</span>
                <span>Spacious drawer and open shelf for storage and daily essentials.</span>
            </li>

            <li class="flex gap-2">
                <span>✔</span>
                <span>Strong engineered wood structure with stable anti-slip legs.</span>
            </li>

            <li class="flex gap-2">
                <span>✔</span>
                <span>Easy assembly with step-by-step instruction manual included.</span>
            </li>

        </ul>

    </div>
    <div class="mt-6 border-t pt-4">

    <h2 class="text-lg font-bold mb-3">Product Details</h2>

        <table class="w-full text-sm border border-gray-200">
            <tbody>

                <tr class="border-b">
                    <td class="p-2 font-medium bg-gray-50 w-1/3">Brand</td>
                    <td class="p-2">FurniHome</td>
                </tr>

                <tr class="border-b">
                    <td class="p-2 font-medium bg-gray-50">Color</td>
                    <td class="p-2">Rustic Brown</td>
                </tr>

                <tr class="border-b">
                    <td class="p-2 font-medium bg-gray-50">Material</td>
                    <td class="p-2">Engineered Wood</td>
                </tr>

                <tr class="border-b">
                    <td class="p-2 font-medium bg-gray-50">Dimensions</td>
                    <td class="p-2">45 x 40 x 60 cm</td>
                </tr>

                <tr class="border-b">
                    <td class="p-2 font-medium bg-gray-50">Weight</td>
                    <td class="p-2">12 kg</td>
                </tr>

                <tr>
                    <td class="p-2 font-medium bg-gray-50">Warranty</td>
                    <td class="p-2">6 Months Manufacturer Warranty</td>
                </tr>

            </tbody>
        </table>

    </div>

</div>