<?php

$mainImage = '';

if (!empty($defaultVariant['image'])) {
    $mainImage = $defaultVariant['image'];
} elseif (!empty($product['thumbnail'])) {
    $mainImage = $product['thumbnail'];
}

if ($mainImage === '') {
    $mainImage = 'product-1.jpg';
}

$galleryImages = [];

if (!empty($product['thumbnail'])) {
    $galleryImages[] = $product['thumbnail'];
}

if (!empty($variants)) {
    foreach ($variants as $variant) {
        if (!empty($variant['image'])) {
            $galleryImages[] = $variant['image'];
        }
    }
}

$galleryImages = array_values(array_unique(array_filter($galleryImages)));

if (empty($galleryImages)) {
    $galleryImages[] = $mainImage;
}

function productImageUrl($image)
{
    return '../assets/images/' . ltrim($image, '/');
}

?>

<div class="flex gap-4">

    <div class="hidden md:flex flex-col gap-3">

        <?php foreach ($galleryImages as $image): ?>
            <button
                type="button"
                onclick="changeMainImage('<?= htmlspecialchars(productImageUrl($image)) ?>')"
                class="w-16 h-16 border border-slate-300 rounded-lg overflow-hidden hover:border-orange-500"
            >
                <img
                    src="<?= htmlspecialchars(productImageUrl($image)) ?>"
                    alt="Product thumbnail"
                    class="w-full h-full object-cover"
                >
            </button>
        <?php endforeach; ?>

    </div>

    <div class="flex-1 border border-slate-300 rounded-lg overflow-hidden bg-white">

        <img
            id="mainProductImage"
            src="<?= htmlspecialchars(productImageUrl($mainImage)) ?>"
            alt="<?= htmlspecialchars($product['title']) ?>"
            class="w-full h-[420px] object-cover"
        >

    </div>

</div>

<script>
    function changeMainImage(imageUrl) {
        const mainImage = document.getElementById('mainProductImage');

        if (mainImage) {
            mainImage.src = imageUrl;
        }
    }
</script>