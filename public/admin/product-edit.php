<?php

require_once __DIR__ . '/../../classes/Database.php';
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../classes/Product.php';
require_once __DIR__ . '/../../classes/Flash.php';

$conn = Database::connect();

$auth = new Auth($conn);
$auth->requireAdmin();

$admin = $auth->admin();

$productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

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

?>

<?php include __DIR__ . '/../../includes/admin-header.php'; ?>
<?php include __DIR__ . '/../../includes/flash-message.php'; ?>

<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">

    <div>
        <h1 class="text-3xl font-bold text-slate-900">Edit Product</h1>
        <p class="text-slate-500 mt-1">
            Update product information.
        </p>
    </div>

    <a href="products.php"
       class="inline-flex items-center justify-center rounded-xl bg-slate-900 hover:bg-slate-700 text-white px-5 py-2.5 text-sm font-semibold">
        Back to Products
    </a>

</div>

<form action="product-update.php" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 xl:grid-cols-12 gap-6">

    <input type="hidden" name="id" value="<?= (int)$product['id'] ?>">

    <section class="xl:col-span-8 space-y-6">

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

            <div class="px-6 py-5 border-b border-slate-200">
                <h2 class="text-xl font-bold">Basic Information</h2>
                <p class="text-sm text-slate-500 mt-1">
                    Product title, slug, brand and description.
                </p>
            </div>

            <div class="p-6 space-y-5">

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Product Title</label>

                    <input
                        type="text"
                        name="title"
                        required
                        value="<?= htmlspecialchars($product['title']) ?>"
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"
                    >
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Product Slug</label>

                    <input
                        type="text"
                        name="slug"
                        required
                        value="<?= htmlspecialchars($product['slug']) ?>"
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"
                    >
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Brand</label>

                    <input
                        type="text"
                        name="brand"
                        value="<?= htmlspecialchars($product['brand'] ?? '') ?>"
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"
                    >
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Description</label>

                    <textarea
                        name="description"
                        rows="6"
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"
                    ><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
                </div>

            </div>

        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

            <div class="px-6 py-5 border-b border-slate-200">
                <h2 class="text-xl font-bold">Product Details</h2>
                <p class="text-sm text-slate-500 mt-1">
                    Color, material, dimensions and weight.
                </p>
            </div>

            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Color</label>

                    <input
                        type="text"
                        name="color"
                        value="<?= htmlspecialchars($product['color'] ?? '') ?>"
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"
                    >
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Material</label>

                    <input
                        type="text"
                        name="material"
                        value="<?= htmlspecialchars($product['material'] ?? '') ?>"
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"
                    >
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Dimensions</label>

                    <input
                        type="text"
                        name="dimensions"
                        value="<?= htmlspecialchars($product['dimensions'] ?? '') ?>"
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"
                    >
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Weight</label>

                    <input
                        type="text"
                        name="weight"
                        value="<?= htmlspecialchars($product['weight'] ?? '') ?>"
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"
                    >
                </div>

            </div>

        </div>

    </section>

    <aside class="xl:col-span-4">

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden sticky top-20">

            <div class="px-6 py-5 border-b border-slate-200">
                <h2 class="text-xl font-bold">Pricing & Stock</h2>
                <p class="text-sm text-slate-500 mt-1">
                    Update price, stock and product image.
                </p>
            </div>

            <div class="p-6 space-y-5">

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Price</label>

                    <input
                        type="number"
                        step="0.01"
                        name="price"
                        required
                        value="<?= htmlspecialchars($product['price']) ?>"
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"
                    >
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Old Price</label>

                    <input
                        type="number"
                        step="0.01"
                        name="old_price"
                        value="<?= htmlspecialchars($product['old_price'] ?? '') ?>"
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"
                    >
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Sale Price</label>

                    <input
                        type="number"
                        step="0.01"
                        name="sale_price"
                        value="<?= htmlspecialchars($product['sale_price'] ?? $product['price']) ?>"
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"
                    >
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Stock</label>

                    <input
                        type="number"
                        name="stock"
                        required
                        value="<?= htmlspecialchars($product['stock']) ?>"
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"
                    >
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Rating</label>

                    <input
                        type="number"
                        step="0.1"
                        name="rating"
                        value="<?= htmlspecialchars($product['rating'] ?? 0) ?>"
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"
                    >
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Review Count</label>

                    <input
                        type="number"
                        name="review_count"
                        value="<?= htmlspecialchars($product['review_count'] ?? 0) ?>"
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"
                    >
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Current Thumbnail
                    </label>

                    <?php if (!empty($product['thumbnail'])): ?>
                        <img
                            src="../../assets/images/<?= htmlspecialchars($product['thumbnail']) ?>"
                            alt="<?= htmlspecialchars($product['title']) ?>"
                            class="w-28 h-28 rounded-xl object-cover border border-slate-200 mb-3"
                        >
                    <?php else: ?>
                        <div class="w-28 h-28 rounded-xl bg-slate-100 flex items-center justify-center text-3xl mb-3">
                            📦
                        </div>
                    <?php endif; ?>

                    <input
                        type="file"
                        name="thumbnail"
                        accept="image/*"
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"
                    >

                    <p class="text-xs text-slate-500 mt-2">
                        Leave empty if you do not want to change the current image.
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Product Status
                    </label>

                    <select
                        name="status"
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"
                    >
                        <option value="active" <?= ($product['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>
                            Active
                        </option>

                        <option value="inactive" <?= ($product['status'] ?? 'active') === 'inactive' ? 'selected' : '' ?>>
                            Inactive
                        </option>
                    </select>

                    <p class="text-xs text-slate-500 mt-2">
                        Inactive products will not appear on the frontend product page.
                    </p>
                </div>

                <button type="submit"
                        class="w-full rounded-xl bg-orange-500 hover:bg-orange-600 text-white px-5 py-3 text-sm font-bold">
                    Update Product
                </button>

            </div>

        </div>

    </aside>

</form>

<?php include __DIR__ . '/../../includes/admin-footer.php'; ?>