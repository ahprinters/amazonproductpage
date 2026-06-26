<?php
    require_once '../classes/Database.php';
    require_once '../classes/Product.php';
    require_once '../classes/Variant.php';


    $conn = Database::connect();

    $slug = $_GET['slug'] ?? 'farmhouse-nightstand-with-charging-station';

    $productModel = new Product($conn);
    $product = $productModel->findBySlug($slug);

    if (!$product) {
        die("Product not found.");
    }

    $variantModel = new Variant($conn);
    $variants = $variantModel->getByProductId((int)$product['id']);

    $defaultVariant = $variants[0] ?? null;

    $displayPrice = $defaultVariant['price'] ?? $product['sale_price'] ?? $product['price'];
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/navbar.php'; ?>
<?php include '../includes/flash-message.php'; ?>

<div class="max-w-7xl mx-auto px-4 pt-4">
    <nav class="text-sm text-gray-500">
        <a href="#" class="hover:text-orange-600">Home</a>
        <span class="mx-2">›</span>
        <a href="#" class="hover:text-orange-600">Furniture</a>
        <span class="mx-2">›</span>
        <a href="#" class="hover:text-orange-600">Nightstands</a>
    </nav>
</div>

<main class="max-w-7xl mx-auto px-4 py-6 pb-20 md:pb-0">

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <section class="lg:col-span-5">
            <?php include '../components/gallery.php'; ?>
        </section>

        <section class="lg:col-span-4">
            <?php include '../components/product-info.php'; ?>
        </section>

        <aside class="lg:col-span-3">
            <?php include '../components/buy-box.php'; ?>
        </aside>
    </div>

    <!-- Trust & Feature Section -->
    <section class="mt-10 border-t pt-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

            <div class="border rounded-lg p-4 text-center">
                <div class="text-2xl">🚚</div>
                <h3 class="font-semibold mt-2">Fast Delivery</h3>
                <p class="text-sm text-gray-600">3–5 business days delivery across country</p>
            </div>

            <div class="border rounded-lg p-4 text-center">
                <div class="text-2xl">🔒</div>
                <h3 class="font-semibold mt-2">Secure Payment</h3>
                <p class="text-sm text-gray-600">100% secure checkout with encryption</p>
            </div>

            <div class="border rounded-lg p-4 text-center">
                <div class="text-2xl">↩️</div>
                <h3 class="font-semibold mt-2">Easy Return</h3>
                <p class="text-sm text-gray-600">7 days hassle-free return policy</p>
            </div>

        </div>
    </section>

    <!-- Reviews Section -->
    <section id="reviews" class="mt-10 border-t pt-6">
        <h2 class="text-2xl font-bold mb-6">Customer Reviews</h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            <div class="space-y-3">
                <div class="text-4xl font-bold">
                    <?= htmlspecialchars($product['rating']) ?>
                </div>

                <div class="text-yellow-400 text-xl">★★★★☆</div>

                <p class="text-sm text-gray-600">
                    <?= number_format($product['review_count']) ?> global ratings
                </p>

                <div class="space-y-2 text-sm">
                    <?php
                    $bars = [
                        '5★' => 70,
                        '4★' => 20,
                        '3★' => 5,
                        '2★' => 3,
                        '1★' => 2
                    ];
                    ?>

                    <?php foreach ($bars as $star => $percent): ?>
                        <div class="flex items-center gap-2">
                            <span><?= $star ?></span>
                            <div class="w-full bg-gray-200 h-2 rounded">
                                <div class="bg-yellow-400 h-2 rounded" style="width:<?= $percent ?>%"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="md:col-span-2 space-y-4">

                <div class="border rounded-lg p-4">
                    <p class="font-semibold">John D.</p>
                    <p class="text-yellow-400 text-sm">★★★★★</p>
                    <p class="text-sm text-gray-700 mt-2">
                        Very solid build quality. Charging ports work perfectly and design looks premium.
                    </p>
                </div>

                <div class="border rounded-lg p-4">
                    <p class="font-semibold">Sarah M.</p>
                    <p class="text-yellow-400 text-sm">★★★★☆</p>
                    <p class="text-sm text-gray-700 mt-2">
                        Nice product, easy to assemble. Slight delay in delivery but overall satisfied.
                    </p>
                </div>

                <div class="border rounded-lg p-4">
                    <p class="font-semibold">Michael K.</p>
                    <p class="text-yellow-400 text-sm">★★★★★</p>
                    <p class="text-sm text-gray-700 mt-2">
                        Exactly as described. Very useful bedside table.
                    </p>
                </div>

            </div>

        </div>
    </section>

    <!-- FAQ Section -->
    <section class="mt-10 border-t pt-6">
        <h2 class="text-2xl font-bold mb-6">Frequently Asked Questions</h2>

        <div class="space-y-3">

            <div class="border rounded-lg">
                <button onclick="toggleFAQ(this)" class="w-full text-left p-4 font-semibold flex justify-between">
                    Is assembly required?
                    <span>+</span>
                </button>
                <div class="hidden p-4 pt-0 text-sm text-gray-700">
                    Yes, simple assembly is required. All tools and instructions are included.
                </div>
            </div>

            <div class="border rounded-lg">
                <button onclick="toggleFAQ(this)" class="w-full text-left p-4 font-semibold flex justify-between">
                    Does it include charging ports?
                    <span>+</span>
                </button>
                <div class="hidden p-4 pt-0 text-sm text-gray-700">
                    Yes, it includes USB ports and AC power outlets for easy charging.
                </div>
            </div>

            <div class="border rounded-lg">
                <button onclick="toggleFAQ(this)" class="w-full text-left p-4 font-semibold flex justify-between">
                    What is the return policy?
                    <span>+</span>
                </button>
                <div class="hidden p-4 pt-0 text-sm text-gray-700">
                    You can return the product within 7 days if unused and in original condition.
                </div>
            </div>

        </div>
    </section>

    <!-- Related Products -->
    <section class="mt-10 border-t pt-6">
        <h2 class="text-2xl font-bold mb-6">Customers Also Viewed</h2>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">

            <div class="border rounded-lg p-3 hover:shadow-md transition">
                <img src="../assets/images/product-2.jpg"
                     onerror="this.src='../assets/images/placeholder.jpg'"
                     class="w-full h-40 object-cover rounded"
                     alt="Modern Side Table">
                <p class="text-sm font-semibold mt-2">Modern Side Table</p>
                <p class="text-sm text-gray-600">$59.99</p>
            </div>

            <div class="border rounded-lg p-3 hover:shadow-md transition">
                <img src="../assets/images/product-3.jpg"
                     onerror="this.src='../assets/images/placeholder.jpg'"
                     class="w-full h-40 object-cover rounded"
                     alt="Wood Storage Nightstand">
                <p class="text-sm font-semibold mt-2">Wood Storage Nightstand</p>
                <p class="text-sm text-gray-600">$74.99</p>
            </div>

            <div class="border rounded-lg p-3 hover:shadow-md transition">
                <img src="../assets/images/product-4.jpg"
                     onerror="this.src='../assets/images/placeholder.jpg'"
                     class="w-full h-40 object-cover rounded"
                     alt="Minimal Bedside Table">
                <p class="text-sm font-semibold mt-2">Minimal Bedside Table</p>
                <p class="text-sm text-gray-600">$49.99</p>
            </div>

            <div class="border rounded-lg p-3 hover:shadow-md transition">
                <img src="../assets/images/product-5.jpg"
                     onerror="this.src='../assets/images/placeholder.jpg'"
                     class="w-full h-40 object-cover rounded"
                     alt="Smart Charging Desk">
                <p class="text-sm font-semibold mt-2">Smart Charging Desk</p>
                <p class="text-sm text-gray-600">$99.99</p>
            </div>

        </div>
    </section>

    <!-- Sticky Mobile Buy Bar -->
    <div class="fixed bottom-0 left-0 right-0 bg-white border-t shadow-lg p-3 flex items-center justify-between md:hidden z-50">
        <div>
            <p class="text-sm font-bold">
                $<?= number_format($displayPrice, 2) ?>
            </p>

            <p class="text-xs <?= $product['stock'] > 0 ? 'text-green-600' : 'text-red-600' ?>">
                <?= $product['stock'] > 0 ? 'In Stock' : 'Out of Stock' ?>
            </p>
        </div>

        <button class="bg-yellow-400 hover:bg-yellow-500 px-5 py-2 rounded-full font-semibold">
            Add to Cart
        </button>
    </div>

</main>


<?php include '../includes/footer.php'; ?>