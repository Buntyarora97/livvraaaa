<?php
require_once '../includes/config.php';

$productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$product = Product::getById($productId);

if (!$product) {
    header('Location: products.php');
    exit;
}

$pageTitle = $product['name'];
$discount = $product['mrp'] > 0 ? round((($product['mrp'] - $product['price']) / $product['mrp']) * 100) : 0;

$relatedProducts = Product::getByCategory($product['category_slug']);
$relatedProducts = array_filter($relatedProducts, function($p) use ($productId) {
    return $p['id'] != $productId;
});
$relatedProducts = array_slice($relatedProducts, 0, 4);

$benefits = !empty($product['benefits']) ? explode(',', $product['benefits']) : [];

require_once '../includes/header.php';
?>

<!-- Page Banner -->
<section class="page-banner">
    <div class="page-banner-content">
        <h1><?php echo htmlspecialchars($product['name']); ?></h1>
        <div class="breadcrumb">
            <a href="index.php">Home</a>
            <span>/</span>
            <a href="products.php">Products</a>
            <span>/</span>
            <a href="products.php?category=<?php echo htmlspecialchars($product['category_slug']); ?>"><?php echo htmlspecialchars($product['category_name']); ?></a>
            <span>/</span>
            <span><?php echo htmlspecialchars($product['name']); ?></span>
        </div>
    </div>
</section>

<!-- Product Detail Section -->
<section class="product-detail">
    <div class="container">
        <div class="product-detail-grid">
            <!-- Product Gallery -->
            <div class="product-gallery reveal-left">
                <div class="main-image">
                    <img src="assets/images/products/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" id="mainProductImage">
                </div>
                <div class="thumbnail-images">
                    <div class="thumbnail active">
                        <img src="assets/images/products/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                    </div>
                </div>
            </div>
            
            <!-- Product Info -->
            <div class="product-detail-info reveal-right">
                <h1><?php echo htmlspecialchars($product['name']); ?></h1>
                
                <div class="product-meta">
                    <div class="product-detail-rating">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <?php if ($i <= floor($product['rating'])): ?>
                                <i class="fas fa-star"></i>
                            <?php elseif ($i - 0.5 <= $product['rating']): ?>
                                <i class="fas fa-star-half-alt"></i>
                            <?php else: ?>
                                <i class="far fa-star"></i>
                            <?php endif; ?>
                        <?php endfor; ?>
                        <span><?php echo $product['rating']; ?> (<?php echo $product['reviews_count']; ?> reviews)</span>
                    </div>
                    <span class="stock-status">
                        <?php if ($product['stock_qty'] > 0): ?>
                        <i class="fas fa-check-circle"></i> In Stock (<?php echo $product['stock_qty']; ?> available)
                        <?php else: ?>
                        <i class="fas fa-times-circle" style="color: red;"></i> Out of Stock
                        <?php endif; ?>
                    </span>
                </div>
                
                <div class="product-detail-price">
                    <span class="detail-current-price"><?php echo CURRENCY_SYMBOL . number_format($product['price']); ?></span>
                    <?php if ($product['mrp'] > $product['price']): ?>
                    <span class="detail-original-price"><?php echo CURRENCY_SYMBOL . number_format($product['mrp']); ?></span>
                    <span class="discount-badge"><?php echo $discount; ?>% OFF</span>
                    <?php endif; ?>
                </div>
                
                <p class="product-description"><?php echo htmlspecialchars($product['short_description']); ?></p>
                
                <?php if (!empty($benefits)): ?>
                <div class="product-benefits">
                    <h4>Key Benefits:</h4>
                    <ul class="benefits-list">
                        <?php foreach ($benefits as $benefit): ?>
                        <li><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars(trim($benefit)); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
                
                <div class="quantity-selector">
                    <label>Quantity:</label>
                    <div class="quantity-controls">
                        <button class="minus-btn" onclick="changeQuantity(-1)">-</button>
                        <input type="number" value="1" min="1" id="productQuantity">
                        <button class="plus-btn" onclick="changeQuantity(1)">+</button>
                    </div>
                </div>
                
                <div class="add-to-cart-section">
                    <button class="add-to-cart-btn" onclick="addToCartWithQty(<?php echo $product['id']; ?>)">
                        <i class="fas fa-shopping-cart"></i> Add to Cart
                    </button>
                    <a href="checkout.php?buy_now=<?php echo $product['id']; ?>" class="buy-now-btn" onclick="addToCartWithQty(<?php echo $product['id']; ?>); return true;">
                        <i class="fas fa-bolt"></i> Buy Now
                    </a>
                    <button class="wishlist-btn">
                        <i class="far fa-heart"></i>
                    </button>
                </div>
                
                <div style="margin-top: 30px; padding: 20px; background: var(--light-bg); border-radius: 12px;">
                    <div style="display: flex; gap: 25px; flex-wrap: wrap;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <i class="fas fa-truck" style="color: var(--primary-gold);"></i>
                            <span>Free Delivery above ₹<?php echo number_format(FREE_SHIPPING_ABOVE); ?></span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <i class="fas fa-undo" style="color: var(--primary-gold);"></i>
                            <span>30 Days Return</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <i class="fas fa-shield-alt" style="color: var(--primary-gold);"></i>
                            <span>100% Genuine</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Related Products -->
<?php if (!empty($relatedProducts)): ?>
<section class="section section-light">
    <div class="container">
        <div class="section-header reveal">
            <span class="section-badge"><i class="fas fa-th-large"></i> Related Products</span>
            <h2 class="section-title">You May Also <span>Like</span></h2>
        </div>
        <div class="products-grid">
            <?php foreach ($relatedProducts as $relProduct): 
                $relDiscount = $relProduct['mrp'] > 0 ? round((($relProduct['mrp'] - $relProduct['price']) / $relProduct['mrp']) * 100) : 0;
            ?>
            <div class="product-card reveal hover-lift">
                <?php if ($relDiscount > 0): ?>
                <span class="product-badge"><?php echo $relDiscount; ?>% OFF</span>
                <?php endif; ?>
                <button class="product-wishlist" title="Add to Wishlist">
                    <i class="far fa-heart"></i>
                </button>
                <div class="product-image">
                    <img src="assets/images/products/<?php echo htmlspecialchars($relProduct['image']); ?>" alt="<?php echo htmlspecialchars($relProduct['name']); ?>">
                    <div class="product-actions">
                        <button class="product-action-btn" onclick="addToCart(<?php echo $relProduct['id']; ?>)" title="Add to Cart">
                            <i class="fas fa-shopping-cart"></i>
                        </button>
                        <a href="product-detail.php?id=<?php echo $relProduct['id']; ?>" class="product-action-btn" title="View Details">
                            <i class="fas fa-eye"></i>
                        </a>
                    </div>
                </div>
                <div class="product-info">
                    <span class="product-category"><?php echo htmlspecialchars($relProduct['category_name']); ?></span>
                    <h3 class="product-name">
                        <a href="product-detail.php?id=<?php echo $relProduct['id']; ?>"><?php echo htmlspecialchars($relProduct['name']); ?></a>
                    </h3>
                    <div class="product-rating">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <i class="fas fa-star"></i>
                        <?php endfor; ?>
                        <span>(<?php echo $relProduct['reviews_count']; ?>)</span>
                    </div>
                    <div class="product-price">
                        <span class="current-price"><?php echo CURRENCY_SYMBOL . number_format($relProduct['price']); ?></span>
                        <?php if ($relProduct['mrp'] > $relProduct['price']): ?>
                        <span class="original-price"><?php echo CURRENCY_SYMBOL . number_format($relProduct['mrp']); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<script>
function changeQuantity(delta) {
    var input = document.getElementById('productQuantity');
    var newVal = parseInt(input.value) + delta;
    if (newVal >= 1) {
        input.value = newVal;
    }
}

function addToCartWithQty(productId) {
    var quantity = document.getElementById('productQuantity').value;
    var formData = new FormData();
    formData.append('product_id', productId);
    formData.append('quantity', quantity);
    
    fetch('ajax/add_to_cart.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateCartCount(data.cart_count);
            showNotification('Product added to cart!');
        }
    });
}
</script>

<?php require_once '../includes/footer.php'; ?>
