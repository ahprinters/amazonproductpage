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

if ($productId <= 0) {
    Flash::set('error', 'Invalid product ID.');
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
$variants = $variantModel->getAllByProductId($productId);
$colors = $variantModel->getAllColors();

?>

<?php include __DIR__ . '/../../includes/admin-header.php'; ?>
<?php include __DIR__ . '/../../includes/flash-message.php'; ?>

<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">

    <div>
        <h1 class="text-3xl font-bold text-slate-900">Product Variants</h1>
        <p class="text-slate-500 mt-1">
            <?= htmlspecialchars($product['title']) ?>
        </p>
    </div>

    <a href="products.php"
       class="inline-flex items-center justify-center rounded-xl bg-slate-900 hover:bg-slate-700 text-white px-5 py-2.5 text-sm font-semibold">
        Back to Products
    </a>

</div>

<div class="grid grid-cols-1 xl:grid-cols-12 gap-6">

    <section class="xl:col-span-7">

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

            <div class="px-6 py-5 border-b border-slate-200">
                <h2 class="text-xl font-bold">Existing Variants</h2>
                <p class="text-sm text-slate-500 mt-1">
                    Total <?= count($variants) ?> active variant<?= count($variants) === 1 ? '' : 's' ?> found.
                </p>
            </div>

            <?php if (empty($variants)): ?>

                <div class="p-10 text-center">
                    <div class="text-5xl mb-4">🎨</div>
                    <h3 class="text-xl font-bold">No variants found</h3>
                    <p class="text-slate-500 mt-2">
                        Add at least one variant so this product can be added to cart.
                    </p>
                </div>

            <?php else: ?>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">

                        <thead class="bg-slate-50 text-slate-600">
                            <tr>
                                <th class="px-6 py-4 text-left font-semibold">Image</th>
                                <th class="px-6 py-4 text-left font-semibold">SKU</th>
                                <th class="px-6 py-4 text-left font-semibold">Color</th>
                                <th class="px-6 py-4 text-right font-semibold">Price</th>
                                <th class="px-6 py-4 text-left font-semibold">Stock</th>
                                <th class="px-6 py-4 text-left font-semibold">Default</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">

                            <?php foreach ($variants as $variant): ?>

                                <?php
                                    $image = $variant['image'] ?: $product['thumbnail'];
                                    $imagePath = $image ? '../../assets/images/' . $image : '';
                                ?>

                                <tr class="hover:bg-slate-50 transition">

                                    <td class="px-6 py-4">
                                        <?php if ($imagePath): ?>
                                            <img
                                                src="<?= htmlspecialchars($imagePath) ?>"
                                                class="w-14 h-14 rounded-xl object-cover border border-slate-200"
                                                alt="<?= htmlspecialchars($variant['sku']) ?>"
                                            >
                                        <?php else: ?>
                                            <div class="w-14 h-14 rounded-xl bg-slate-100 flex items-center justify-center text-2xl">
                                                📦
                                            </div>
                                        <?php endif; ?>
                                    </td>

                                    <td class="px-6 py-4">
                                        <p class="font-bold text-slate-900">
                                            <?= htmlspecialchars($variant['sku']) ?>
                                        </p>

                                        <p class="text-xs text-slate-500">
                                            Status: <?= htmlspecialchars($variant['status']) ?>
                                        </p>
                                    </td>

                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <span
                                                class="w-5 h-5 rounded-full border border-slate-300"
                                                style="background-color: <?= htmlspecialchars($variant['color_code'] ?? '#ddd') ?>;"
                                            ></span>

                                            <span class="font-semibold">
                                                <?= htmlspecialchars($variant['color_name'] ?? 'N/A') ?>
                                            </span>
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 text-right">
                                        <p class="font-bold">
                                            $<?= number_format((float)$variant['price'], 2) ?>
                                        </p>

                                        <?php if (!empty($variant['old_price'])): ?>
                                            <p class="text-xs text-slate-400 line-through">
                                                $<?= number_format((float)$variant['old_price'], 2) ?>
                                            </p>
                                        <?php endif; ?>
                                    </td>

                                    <td class="px-6 py-4">
                                        <?= (int)$variant['stock'] ?> pcs
                                    </td>

                                    <td class="px-6 py-4">
                                        <?php if ((int)$variant['is_default'] === 1): ?>
                                            <span class="inline-flex rounded-full bg-green-100 text-green-700 px-3 py-1 text-xs font-semibold">
                                                Default
                                            </span>
                                        <?php else: ?>
                                            <span class="text-slate-400 text-xs">No</span>
                                        <?php endif; ?>
                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        </tbody>

                    </table>
                </div>

            <?php endif; ?>

        </div>

    </section>

    <aside class="xl:col-span-5">

        <form action="variant-store.php" method="POST" class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden sticky top-20">

            <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">

            <div class="px-6 py-5 border-b border-slate-200">
                <h2 class="text-xl font-bold">Add New Variant</h2>
                <p class="text-sm text-slate-500 mt-1">
                    Add color, SKU, price and stock.
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
                            <option value="<?= (int)$color['id'] ?>">
                                <?= htmlspecialchars($color['color_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <p class="text-xs text-slate-500 mt-2">
                        Colors come from product_colors table.
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        SKU
                    </label>

                    <input
                        type="text"
                        name="sku"
                        required
                        placeholder="FH-NS-RB-001"
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"
                    >
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Variant Image Filename
                    </label>

                    <input
                        type="text"
                        name="image"
                        placeholder="product-1.jpg"
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"
                    >

                    <p class="text-xs text-slate-500 mt-2">
                        Put image inside assets/images folder first.
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
                            placeholder="89.99"
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
                            placeholder="119.99"
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
                        placeholder="10"
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"
                    >
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Status
                    </label>

                    <select
                        name="status"
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"
                    >
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>

                <label class="flex items-center gap-3">
                    <input type="checkbox" name="is_default" value="1" class="w-4 h-4">
                    <span class="text-sm font-semibold text-slate-700">
                        Make this default variant
                    </span>
                </label>

                <button type="submit"
                        class="w-full rounded-xl bg-orange-500 hover:bg-orange-600 text-white px-5 py-3 text-sm font-bold">
                    Save Variant
                </button>

            </div>

        </form>

    </aside>

</div>

<?php include __DIR__ . '/../../includes/admin-footer.php'; ?>