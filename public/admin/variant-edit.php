<?php

require_once __DIR__ . '/../../classes/Database.php';
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../classes/Product.php';
require_once __DIR__ . '/../../classes/Variant.php';
require_once __DIR__ . '/../../classes/Flash.php';

$conn = Database::connect();

$auth = new Auth($conn);
$auth->requireAdmin();

$admin = $auth->admin();

$productId = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;
$variantId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($productId <= 0 || $variantId <= 0) {
    Flash::set('error', 'Invalid product or variant ID.');
    header("Location: products.php");
    exit;
}

$productModel = new Product($conn);
$product = $productModel->findById($productId);

if (!$product) {
    Flash::set('error', 'Product not found.');
    header("Location: products.php");
    exit;
}

$variantModel = new Variant($conn);
$variant = $variantModel->findById($variantId);

if (!$variant || (int)$variant['product_id'] !== $productId) {
    Flash::set('error', 'Variant not found for this product.');
    header("Location: product-variants.php?product_id=" . $productId);
    exit;
}

$colors = $variantModel->getAllColors();

?>

<?php include __DIR__ . '/../../includes/admin-header.php'; ?>
<?php include __DIR__ . '/../../includes/flash-message.php'; ?>

<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">

    <div>
        <h1 class="text-3xl font-bold text-slate-900">Edit Variant</h1>
        <p class="text-slate-500 mt-1">
            Product: <?= htmlspecialchars($product['title']) ?>
        </p>
    </div>

    <a href="product-variants.php?product_id=<?= (int)$product['id'] ?>"
       class="inline-flex items-center justify-center rounded-xl bg-slate-900 hover:bg-slate-700 text-white px-5 py-2.5 text-sm font-semibold">
        Back to Variants
    </a>

</div>

<form action="variant-update.php" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 xl:grid-cols-12 gap-6">

    <input type="hidden" name="id" value="<?= (int)$variant['id'] ?>">
    <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">

    <section class="xl:col-span-8">

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

            <div class="px-6 py-5 border-b border-slate-200">
                <h2 class="text-xl font-bold">Variant Information</h2>
                <p class="text-sm text-slate-500 mt-1">
                    Update variant color, SKU, image, price and stock.
                </p>
            </div>

            <div class="p-6 space-y-5">

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Color
                    </label>

                    <select
                        name="color_id"
                        required
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"
                    >
                        <option value="">Select Color</option>

                        <?php foreach ($colors as $color): ?>
                            <option
                                value="<?= (int)$color['id'] ?>"
                                <?= (int)$variant['color_id'] === (int)$color['id'] ? 'selected' : '' ?>
                            >
                                <?= htmlspecialchars($color['color_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        SKU
                    </label>

                    <input
                        type="text"
                        name="sku"
                        required
                        value="<?= htmlspecialchars($variant['sku']) ?>"
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"
                    >
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Variant Image
                    </label>

                    <?php if (!empty($variant['image'])): ?>
                        <img
                            src="../../assets/images/<?= htmlspecialchars($variant['image']) ?>"
                            alt="<?= htmlspecialchars($variant['sku']) ?>"
                            class="w-28 h-28 rounded-xl object-cover border border-slate-200 mb-3"
                        >
                    <?php else: ?>
                        <div class="w-28 h-28 rounded-xl bg-slate-100 flex items-center justify-center text-3xl mb-3">
                            📦
                        </div>
                    <?php endif; ?>

                    <input
                        type="file"
                        name="image"
                        accept="image/*"
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"
                    >

                    <p class="text-xs text-slate-500 mt-2">
                        Leave empty if you do not want to change current variant image.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Price
                        </label>

                        <input
                            type="number"
                            step="0.01"
                            name="price"
                            required
                            value="<?= htmlspecialchars($variant['price']) ?>"
                            class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Old Price
                        </label>

                        <input
                            type="number"
                            step="0.01"
                            name="old_price"
                            value="<?= htmlspecialchars($variant['old_price'] ?? '') ?>"
                            class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"
                        >
                    </div>

                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Stock
                    </label>

                    <input
                        type="number"
                        name="stock"
                        required
                        value="<?= htmlspecialchars($variant['stock']) ?>"
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"
                    >
                </div>

            </div>

        </div>

    </section>

    <aside class="xl:col-span-4">

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden sticky top-20">

            <div class="px-6 py-5 border-b border-slate-200">
                <h2 class="text-xl font-bold">Status Settings</h2>
                <p class="text-sm text-slate-500 mt-1">
                    Control default and active status.
                </p>
            </div>

            <div class="p-6 space-y-5">

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Status
                    </label>

                    <select
                        name="status"
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"
                    >
                        <option value="active" <?= $variant['status'] === 'active' ? 'selected' : '' ?>>
                            Active
                        </option>

                        <option value="inactive" <?= $variant['status'] === 'inactive' ? 'selected' : '' ?>>
                            Inactive
                        </option>
                    </select>
                </div>

                <label class="flex items-center gap-3">
                    <input
                        type="checkbox"
                        name="is_default"
                        value="1"
                        class="w-4 h-4"
                        <?= (int)$variant['is_default'] === 1 ? 'checked' : '' ?>
                    >

                    <span class="text-sm font-semibold text-slate-700">
                        Make this default variant
                    </span>
                </label>

                <button type="submit"
                        class="w-full rounded-xl bg-orange-500 hover:bg-orange-600 text-white px-5 py-3 text-sm font-bold">
                    Update Variant
                </button>

            </div>

        </div>

    </aside>

</form>

<?php include __DIR__ . '/../../includes/admin-footer.php'; ?>