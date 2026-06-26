
<script>
function toggleFAQ(btn) {
    const content = btn.nextElementSibling;
    content.classList.toggle("hidden");
}

function selectVariant(button) {
    const color = button.dataset.color;
    const sku = button.dataset.sku;
    const image = button.dataset.image;
    const price = parseFloat(button.dataset.price || 0);
    const oldPrice = parseFloat(button.dataset.oldPrice || price);
    const stock = parseInt(button.dataset.stock || 0);

    document.getElementById('selectedColor').innerText = color;
    document.getElementById('selectedVariantSku').value = sku;
    document.getElementById('selectedVariantPrice').value = price;
    document.getElementById('selectedVariantStock').value = stock;
    document.getElementById('cartVariantSku').value = sku;
    document.getElementById('cartVariantPrice').value = price;

    const mainImage = document.getElementById('mainProductImage');
    if (mainImage && image) {
        mainImage.src = "../assets/images/" + image;
    }

    const formattedPrice = price.toFixed(2);
    const parts = formattedPrice.split('.');

    const priceMain = document.getElementById('priceMain');
    const priceCents = document.getElementById('priceCents');
    const buyBoxPrice = document.getElementById('buyBoxPrice');

    if (priceMain) priceMain.innerText = parts[0];
    if (priceCents) priceCents.innerText = parts[1];
    if (buyBoxPrice) buyBoxPrice.innerText = formattedPrice;

    const oldPriceRow = document.getElementById('oldPriceRow');
    const oldPriceText = document.getElementById('oldPrice');
    const discountRow = document.getElementById('discountRow');
    const discountAmount = document.getElementById('discountAmount');
    const buyBoxOldPrice = document.getElementById('buyBoxOldPrice');

    if (oldPrice > price) {
        const discount = oldPrice - price;

        if (oldPriceRow) oldPriceRow.classList.remove('hidden');
        if (discountRow) discountRow.classList.remove('hidden');
        if (oldPriceText) oldPriceText.innerText = '$' + oldPrice.toFixed(2);
        if (discountAmount) discountAmount.innerText = discount.toFixed(2);

        if (buyBoxOldPrice) {
            buyBoxOldPrice.classList.remove('hidden');
            buyBoxOldPrice.innerText = '$' + oldPrice.toFixed(2);
        }
    } else {
        if (oldPriceRow) oldPriceRow.classList.add('hidden');
        if (discountRow) discountRow.classList.add('hidden');
        if (buyBoxOldPrice) buyBoxOldPrice.classList.add('hidden');
    }

    const stockText = document.getElementById('stockText');
    if (stockText) {
        stockText.innerText = stock > 0 ? 'In Stock' : 'Out of Stock';
        stockText.classList.remove('text-green-700', 'text-red-600');
        stockText.classList.add(stock > 0 ? 'text-green-700' : 'text-red-600');
    }

    document.querySelectorAll('.variation-btn').forEach(btn => {
        btn.classList.remove('border-2', 'border-orange-500');
        btn.classList.add('border');
    });

    button.classList.remove('border');
    button.classList.add('border-2', 'border-orange-500');
}


</script>

</body>
</html>