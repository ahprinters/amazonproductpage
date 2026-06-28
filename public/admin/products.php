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

$search = trim($_GET['search'] ?? '');
$statusFilter = trim($_GET['status'] ?? '');

if (!in_array($statusFilter, ['active', 'inactive'])) {
    $statusFilter = '';
}

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

if ($page < 1) {
    $page = 1;
}

$perPage = 10;

$totalProducts = $productModel->countProducts($search, $statusFilter);

$totalPages = (int)ceil($totalProducts / $perPage);

if ($totalPages < 1) {
    $totalPages = 1;
}

if ($page > $totalPages) {
    $page = $totalPages;
}

$offset = ($page - 1) * $perPage;

$products = $productModel->getProductsPaginated(
    $search,
    $statusFilter,
    $perPage,
    $offset
);
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

function productPageUrl($page, $search, $statusFilter)
{
    $params = [
        'page' => $page
    ];

    if ($search !== '') {
        $params['search'] = $search;
    }

    if ($statusFilter !== '') {
        $params['status'] = $statusFilter;
    }

    return 'products.php?' . http_build_query($params);
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


<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 mb-6">

    <form action="products.php" method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">

        <div class="md:col-span-6">
            <label class="block text-sm font-semibold text-slate-700 mb-2">
                Search Product
            </label>

            <input
                type="text"
                name="search"
                value="<?= htmlspecialchars($search) ?>"
                placeholder="Search by title, slug or brand..."
                class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"
            >
        </div>

        <div class="md:col-span-3">
            <label class="block text-sm font-semibold text-slate-700 mb-2">
                Status
            </label>

            <select
                name="status"
                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"
            >
                <option value="">All Status</option>
                <option value="active" <?= $statusFilter === 'active' ? 'selected' : '' ?>>
                    Active
                </option>
                <option value="inactive" <?= $statusFilter === 'inactive' ? 'selected' : '' ?>>
                    Inactive
                </option>
            </select>
        </div>

        <div class="md:col-span-3 flex gap-2">

            <button
                type="submit"
                class="flex-1 rounded-xl bg-slate-900 hover:bg-slate-700 text-white px-5 py-3 text-sm font-bold"
            >
                Filter
            </button>

            <a
                href="products.php"
                class="rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 px-5 py-3 text-sm font-bold"
            >
                Reset
            </a>

        </div>

    </form>

</div>

<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

    <div class="px-6 py-5 border-b border-slate-200 flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold">All Products</h2>
            <p class="text-sm text-slate-500 mt-1">
Total <?= number_format($totalProducts) ?> product<?= $totalProducts === 1 ? '' : 's' ?> found.
Showing page <?= $page ?> of <?= $totalPages ?>.            </p>
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

                                        <p class="text-xs mt-1">
                                            <?php if (($product['status'] ?? 'active') === 'active'): ?>
                                                <span class="inline-flex rounded-full bg-green-100 text-green-700 px-2 py-0.5 font-semibold">
                                                    Active
                                                </span>
                                            <?php else: ?>
                                                <span class="inline-flex rounded-full bg-red-100 text-red-700 px-2 py-0.5 font-semibold">
                                                    Inactive
                                                </span>
                                            <?php endif; ?>
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

                                <?php if (($product['status'] ?? 'active') === 'active'): ?>

                                    <form action="product-status-update.php" method="POST"
                                        onsubmit="return confirm('Are you sure you want to make this product inactive?');">

                                        <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">
                                        <input type="hidden" name="status" value="inactive">

                                        <button type="submit"
                                                class="inline-flex items-center rounded-lg bg-red-500 hover:bg-red-600 text-white px-4 py-2 text-xs font-semibold">
                                            Inactive
                                        </button>

                                    </form>

                                <?php else: ?>

                                    <form action="product-status-update.php" method="POST">

                                        <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">
                                        <input type="hidden" name="status" value="active">

                                        <button type="submit"
                                                class="inline-flex items-center rounded-lg bg-green-500 hover:bg-green-600 text-white px-4 py-2 text-xs font-semibold">
                                            Active
                                        </button>

                                    </form>

                                <?php endif; ?>

                            </div>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>
        
          <?php if ($totalPages > 1): ?>

            <div class="px-6 py-5 border-t border-slate-200 flex flex-col md:flex-row md:items-center md:justify-between gap-4">

                <p class="text-sm text-slate-500">
                    Page <?= $page ?> of <?= $totalPages ?>
                </p>

                <div class="flex items-center gap-2">

                    <?php if ($page > 1): ?>
                        <a href="<?= htmlspecialchars(productPageUrl($page - 1, $search, $statusFilter)) ?>"
                        class="rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-800 px-4 py-2 text-sm font-semibold">
                            Previous
                        </a>
                    <?php endif; ?>

                    <?php
                        $startPage = max(1, $page - 2);
                        $endPage = min($totalPages, $page + 2);
                    ?>

                    <?php for ($i = $startPage; $i <= $endPage; $i++): ?>

                        <a href="<?= htmlspecialchars(productPageUrl($i, $search, $statusFilter)) ?>"
                        class="rounded-lg px-4 py-2 text-sm font-semibold
                        <?= $i === $page
                                ? 'bg-orange-500 text-white'
                                : 'bg-slate-100 hover:bg-slate-200 text-slate-800'
                        ?>">
                            <?= $i ?>
                        </a>

                    <?php endfor; ?>

                    <?php if ($page < $totalPages): ?>
                        <a href="<?= htmlspecialchars(productPageUrl($page + 1, $search, $statusFilter)) ?>"
                        class="rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-800 px-4 py-2 text-sm font-semibold">
                            Next
                        </a>
                    <?php endif; ?>

                </div>

            </div>

        <?php endif; ?>                              
    <?php endif; ?>

</div>

<?php include __DIR__ . '/../../includes/admin-footer.php'; ?>