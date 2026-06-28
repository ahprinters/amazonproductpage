<?php

require_once __DIR__ . '/../../classes/Database.php';
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../classes/Product.php';
require_once __DIR__ . '/../../classes/Flash.php';

$conn = Database::connect();

$auth = new Auth($conn);
$auth->requireAdmin();

$admin = $auth->admin();

$productModel = new Product($conn);
$products = $productModel->getAllProducts();

function stockBadgeClass($stock)
{
    if ($stock <= 0) {
        return 'bg-red-100 text-red-700 border-red-200';
    }

    if ($stock <= 5) {
        return 'bg-yellow-100 text-yellow-700 border-yellow-200';
    }

    return 'bg-green-100 text-green-700 border-green-200';
}

?>

<?php include __DIR__ . '/../../includes/admin-header.php'; ?>
<?php include __DIR__ . '/../../includes/flash-message.php'; ?>

<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">

    <div>
        <h1 class="text-3xl font-bold text-slate-900">Products</h1>
        <p class="text-slate-500 mt-1">
            View and manage all store products.
        </p>
    </div>

    <a href="product-create.php"
       class="inline-flex items-center justify-center rounded-xl bg-orange-500 hover:bg-orange-600 text-white px-5 py-2.5 text-sm font-semibold">
        Add New Product
    </a>

</div>

<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

    <div class="px-6 py-5 border-b border-slate-200 flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold">All Products</h2>
            <p class="text-sm text-slate-500 mt-1">
                Total <?= count($products) ?> product<?= count($products) === 1 ? '' : 's' ?> found.
            </p>
        </div>
    </div>

    <?php if (empty($products)): ?>

        <div class="p-10 text-center">
            <div class="text-5xl mb-4">📦</div>
            <h3 class="text-xl font-bold">No products found</h3>
            <p class="text-slate-500 mt-2">
                Products will appear here after you add them.
            </p>
        </div>

    <?php else: ?>

        <div class="overflow-x-auto">

            <table class="w-full text-sm">

                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="px-6 py-4 text-left font-semibold">Product</th>
                        <th class="px-6 py-4 text-left font-semibold">Brand</th>
                        <th class="px-6 py-4 text-right font-semibold">Price</th>
                        <th class="px-6 py-4 text-right font-semibold">Old Price</th>
                        <th class="px-6 py-4 text-left font-semibold">Stock</th>
                        <th class="px-6 py-4 text-left font-semibold">Rating</th>
                        <th class="px-6 py-4 text-left font-semibold">Created</th>
                        <th class="px-6 py-4 text-right font-semibold">Action</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">

                    <?php foreach ($products as $product): ?>

                        <?php
                            $thumbnail = $product['thumbnail'] ?? '';
                            $imagePath = $thumbnail !== ''
                                ? '../../assets/images/' . $thumbnail
                                : '';
                        ?>

                        <tr class="hover:bg-slate-50 transition">

                            <td class="px-6 py-4">

                                <div class="flex items-center gap-4">

                                    <?php if ($imagePath): ?>
                                        <img
                                            src="<?= htmlspecialchars($imagePath) ?>"
                                            alt="<?= htmlspecialchars($product['title']) ?>"
                                            class="w-14 h-14 rounded-xl object-cover border border-slate-200"
                                        >
                                    <?php else: ?>
                                        <div class="w-14 h-14 rounded-xl bg-slate-100 flex items-center justify-center text-2xl">
                                            📦
                                        </div>
                                    <?php endif; ?>

                                    <div>
                                        <p class="font-bold text-slate-900">
                                            <?= htmlspecialchars($product['title']) ?>
                                        </p>

                                        <p class="text-xs text-slate-500 mt-1">
                                            Slug: <?= htmlspecialchars($product['slug']) ?>
                                        </p>
                                    </div>

                                </div>

                            </td>

                            <td class="px-6 py-4 text-slate-700">
                                <?= htmlspecialchars($product['brand'] ?? 'N/A') ?>
                            </td>

                            <td class="px-6 py-4 text-right">
                                <span class="font-bold text-slate-900">
                                    $<?= number_format((float)$product['price'], 2) ?>
                                </span>
                            </td>

                            <td class="px-6 py-4 text-right text-slate-500">
                                <?php if (!empty($product['old_price'])): ?>
                                    $<?= number_format((float)$product['old_price'], 2) ?>
                                <?php else: ?>
                                    —
                                <?php endif; ?>
                            </td>

                            <td class="px-6 py-4">
                                <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold <?= stockBadgeClass((int)$product['stock']) ?>">
                                    <?= (int)$product['stock'] ?> in stock
                                </span>
                            </td>

                            <td class="px-6 py-4">
                                <span class="text-orange-500 font-semibold">
                                    ★ <?= number_format((float)$product['rating'], 1) ?>
                                </span>

                                <p class="text-xs text-slate-500">
                                    <?= number_format((int)$product['review_count']) ?> reviews
                                </p>
                            </td>

                            <td class="px-6 py-4 text-slate-600">
                                <?= date('d M Y', strtotime($product['created_at'])) ?>
                            </td>

                            <td class="px-6 py-4 text-right">

                                <div class="flex items-center justify-end gap-2">

                                    <a href="product-edit.php?id=<?= (int)$product['id'] ?>"
                                    class="inline-flex items-center rounded-lg bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 text-xs font-semibold">
                                        Edit
                                    </a>

                                    <a href="product-variants.php?product_id=<?= (int)$product['id'] ?>"
                                    class="inline-flex items-center rounded-lg bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 text-xs font-semibold">
                                        Variants
                                    </a>

                                    <a href="../product.php?slug=<?= urlencode($product['slug']) ?>"
                                    class="inline-flex items-center rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-800 px-4 py-2 text-xs font-semibold">
                                        View
                                    </a>

                                </div>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    <?php endif; ?>

</div>

<?php include __DIR__ . '/../../includes/admin-footer.php'; ?>