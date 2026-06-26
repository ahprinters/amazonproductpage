<?php
if (!isset($product) || !is_array($product)) {
    die("Product data not found.");
}

?>

<div class="flex gap-4">
    <div class="hidden md:flex flex-col gap-3">
        <?php for ($i = 1; $i <= 5; $i++): ?>
            <button class="w-16 h-16 border rounded-md hover:border-orange-500 overflow-hidden">
                <img 
                    id="mainProductImage"
                    src="../assets/images/<?= htmlspecialchars($defaultVariant['image'] ?? $product['thumbnail'] ?? 'placeholder.jpg') ?>" 
                    alt="<?= htmlspecialchars($product['title']) ?>"
                    class="w-full max-h-[520px] object-contain"
                >
            </button>
        <?php endfor; ?>
    </div>

    <div class="flex-1 border rounded-lg p-4 bg-white">
        <img 
            src="../assets/images/product-1.jpg" 
            alt="Product Image"
            class="w-full max-h-[520px] object-contain"
        >
    </div>
</div>