<?php

require_once __DIR__ . '/../../classes/Database.php';
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../classes/Flash.php';

$conn = Database::connect();

$auth = new Auth($conn);
$auth->requireAdmin();

$admin = $auth->admin();

?>

<?php include __DIR__ . '/../../includes/admin-header.php'; ?>
<?php include __DIR__ . '/../../includes/flash-message.php'; ?>

<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">

    <div>
        <h1 class="text-3xl font-bold text-slate-900">Add New Product</h1>
        <p class="text-slate-500 mt-1">
            Create a new product for your store.
        </p>
    </div>

    <a href="products.php"
       class="inline-flex items-center justify-center rounded-xl bg-slate-900 hover:bg-slate-700 text-white px-5 py-2.5 text-sm font-semibold">
        Back to Products
    </a>

</div>

<form action="product-store.php" method="POST" class="grid grid-cols-1 xl:grid-cols-12 gap-6">

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
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Product Title
                    </label>

                    <input
                        type="text"
                        name="title"
                        required
                        placeholder="Farmhouse Nightstand with Charging Station"
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"
                    >
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Product Slug
                    </label>

                    <input
                        type="text"
                        name="slug"
                        required
                        placeholder="farmhouse-nightstand-with-charging-station"
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"
                    >

                    <p class="text-xs text-slate-500 mt-2">
                        Use small letters, numbers and hyphen only.
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Brand
                    </label>

                    <input
                        type="text"
                        name="brand"
                        placeholder="FurniHome"
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"
                    >
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Description
                    </label>

                    <textarea
                        name="description"
                        rows="6"
                        placeholder="Write product description..."
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"
                    ></textarea>
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
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Color
                    </label>

                    <input
                        type="text"
                        name="color"
                        placeholder="Rustic Brown"
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"
                    >
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Material
                    </label>

                    <input
                        type="text"
                        name="material"
                        placeholder="Engineered Wood"
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"
                    >
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Dimensions
                    </label>

                    <input
                        type="text"
                        name="dimensions"
                        placeholder="45 x 40 x 60 cm"
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"
                    >
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Weight
                    </label>

                    <input
                        type="text"
                        name="weight"
                        placeholder="12 kg"
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
                    Set price, stock and product image.
                </p>
            </div>

            <div class="p-6 space-y-5">

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

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Sale Price
                    </label>

                    <input
                        type="number"
                        step="0.01"
                        name="sale_price"
                        placeholder="89.99"
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"
                    >
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
                        Rating
                    </label>

                    <input
                        type="number"
                        step="0.1"
                        name="rating"
                        placeholder="4.5"
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"
                    >
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Review Count
                    </label>

                    <input
                        type="number"
                        name="review_count"
                        placeholder="1248"
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"
                    >
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Thumbnail Filename
                    </label>

                    <input
                        type="text"
                        name="thumbnail"
                        placeholder="product-1.jpg"
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"
                    >

                    <p class="text-xs text-slate-500 mt-2">
                        Put image inside assets/images folder first.
                    </p>
                </div>

                <button type="submit"
                        class="w-full rounded-xl bg-orange-500 hover:bg-orange-600 text-white px-5 py-3 text-sm font-bold">
                    Save Product
                </button>

            </div>

        </div>

    </aside>

</form>

<?php include __DIR__ . '/../../includes/admin-footer.php'; ?>