<?php

require_once __DIR__ . '/../../classes/Database.php';
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../classes/Variant.php';
require_once __DIR__ . '/../../classes/Flash.php';

$conn = Database::connect();

$auth = new Auth($conn);
$auth->requireAdmin();

$admin = $auth->admin();

$variantModel = new Variant($conn);
$colors = $variantModel->getAllColors();

?>

<?php include __DIR__ . '/../../includes/admin-header.php'; ?>
<?php include __DIR__ . '/../../includes/flash-message.php'; ?>

<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">

    <div>
        <h1 class="text-3xl font-bold text-slate-900">Product Colors</h1>
        <p class="text-slate-500 mt-1">
            Manage colors used for product variants.
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
                <h2 class="text-xl font-bold">All Colors</h2>
                <p class="text-sm text-slate-500 mt-1">
                    Total <?= count($colors) ?> color<?= count($colors) === 1 ? '' : 's' ?> found.
                </p>
            </div>

            <?php if (empty($colors)): ?>

                <div class="p-10 text-center">
                    <div class="text-5xl mb-4">🎨</div>
                    <h3 class="text-xl font-bold">No colors found</h3>
                    <p class="text-slate-500 mt-2">
                        Add your first color from the form.
                    </p>
                </div>

            <?php else: ?>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">

                        <thead class="bg-slate-50 text-slate-600">
                            <tr>
                                <th class="px-6 py-4 text-left font-semibold">Preview</th>
                                <th class="px-6 py-4 text-left font-semibold">Color Name</th>
                                <th class="px-6 py-4 text-left font-semibold">Color Code</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">

                            <?php foreach ($colors as $color): ?>
                                <tr class="hover:bg-slate-50 transition">

                                    <td class="px-6 py-4">
                                        <span
                                            class="inline-block w-10 h-10 rounded-xl border border-slate-300"
                                            style="background-color: <?= htmlspecialchars($color['color_code']) ?>;"
                                        ></span>
                                    </td>

                                    <td class="px-6 py-4 font-semibold text-slate-900">
                                        <?= htmlspecialchars($color['color_name']) ?>
                                    </td>

                                    <td class="px-6 py-4 text-slate-600">
                                        <?= htmlspecialchars($color['color_code']) ?>
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

        <form action="color-store.php" method="POST" class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden sticky top-20">

            <div class="px-6 py-5 border-b border-slate-200">
                <h2 class="text-xl font-bold">Add New Color</h2>
                <p class="text-sm text-slate-500 mt-1">
                    This color will appear in variant dropdown.
                </p>
            </div>

            <div class="p-6 space-y-5">

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Color Name
                    </label>

                    <input
                        type="text"
                        name="color_name"
                        required
                        placeholder="Beige"
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"
                    >
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Color Code
                    </label>

                    <input
                        type="color"
                        name="color_code"
                        value="#F5F5DC"
                        class="w-full h-12 rounded-xl border border-slate-300 px-2 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400"
                    >

                    <p class="text-xs text-slate-500 mt-2">
                        Example: #F5F5DC, #000000, #FFFFFF
                    </p>
                </div>

                <button type="submit"
                        class="w-full rounded-xl bg-orange-500 hover:bg-orange-600 text-white px-5 py-3 text-sm font-bold">
                    Save Color
                </button>

            </div>

        </form>

    </aside>

</div>

<?php include __DIR__ . '/../../includes/admin-footer.php'; ?>