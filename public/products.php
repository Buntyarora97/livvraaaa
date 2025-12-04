<?php
require_once '../includes/config.php';

$categorySlug = isset($_GET['category']) ? $_GET['category'] : null;
$searchQuery = isset($_GET['search']) ? $_GET['search'] : null;

$allCategories = Category::getAll(true);

if ($categorySlug) {
    $filteredProducts = Product::getByCategory($categorySlug);
    $currentCategory = Category::getBySlug($categorySlug);
    $pageTitle = $currentCategory ? $currentCategory['name'] : 'Products';
} elseif ($searchQuery) {
    $filteredProducts = Product::search($searchQuery);
    $pageTitle = 'Search Results for "' . htmlspecialchars($searchQuery) . '"';
} else {
    $filteredProducts = Product::getAll(true);
    $pageTitle = 'All Products';
}

require_once '../includes/header.php';
?>

<!-- Page Banner -->
<section class="page-banner">
    <div class="page-banner-content">
        <h1><?php echo htmlspecialchars($pageTitle); ?></h1>
        <div class="breadcrumb">
            <a href="index.php">Home</a>
            <span>/</span>
            <span>Products</span>
            <?php if ($categorySlug && isset($currentCategory)): ?>
            <span>/</span>
            <span><?php echo htmlspecialchars($currentCategory['name']); ?></span>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Products Section -->
<section class="products-section">
    <div class="container">
        <div class="products-layout">
            <!-- Sidebar -->
            <aside class="products-sidebar">
                <div class="filter-card">
                    <h4>Categories</h4>
                    <ul class="filter-list">
                        <li>
                            <a href="products.php" class="<?php echo !$categorySlug ? 'active' : ''; ?>">
                                All Products
                                <span class="filter-count"><?php echo count(Product::getAll(true)); ?></span>
                            </a>
                        </li>
                        <?php foreach ($allCategories as $cat): ?>
                        <li>
                            <a href="products.php?category=<?php echo htmlspecialchars($cat['slug']); ?>" class="<?php echo $categorySlug === $cat['slug'] ? 'active' : ''; ?>">
                                <i class="fas <?php echo htmlspecialchars($cat['icon_class']); ?>"></i> <?php echo htmlspecialchars($cat['name']); ?>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </aside>
            
            <!-- Products Grid -->
            <div class="products-content">
                <div class="products-toolbar">
                    <div class="products-count">
                        Showing <strong><?php echo count($filteredProducts); ?></strong> products
                    </div>
                    <div class="products-sort">
                        <select id="sortProducts">
                            <option value="default">Sort by: Default</option>
                            <option value="price-low">Price: Low to High</option>
                            <option value="price-high">Price: High to Low</option>
                            <option value="rating">Customer Rating</option>
                            <option value="newest">Newest First</option>
                        </select>
                    </div>
                </div>
                
                <?php if (empty($filteredProducts)): ?>
                <div class="empty-cart">
                    <i class="fas fa-search"></i>
                    <h3>No Products Found</h3>
                    <p>Try adjusting your filters or search query</p>
                    <a href="products.php" class="view-all-btn">View All Products</a>
                </div>
                <?php else: ?>
                <div class="products-grid">
                    <?php foreach ($filteredProducts as $product): 
                        $discount = $product['mrp'] > 0 ? round((($product['mrp'] - $product['price']) / $product['mrp']) * 100) : 0;
                    ?>
                    <div class="product-card reveal hover-lift">
                        <?php if ($discount > 0): ?>
                        <span class="product-badge"><?php echo $discount; ?>% OFF</span>
                        <?php endif; ?>
                        <button class="product-wishlist" title="Add to Wishlist">
                            <i class="far fa-heart"></i>
                        </button>
                        <div class="product-image">
                            <img src="assets/images/products/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                            <div class="product-actions">
                                <button class="product-action-btn" onclick="addToCart(<?php echo $product['id']; ?>)" title="Add to Cart">
                                    <i class="fas fa-shopping-cart"></i>
                                </button>
                                <a href="product-detail.php?id=<?php echo $product['id']; ?>" class="product-action-btn" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </div>
                        <div class="product-info">
                            <span class="product-category"><?php echo htmlspecialchars($product['category_name'] ?? 'Uncategorized'); ?></span>
                            <h3 class="product-name">
                                <a href="product-detail.php?id=<?php echo $product['id']; ?>"><?php echo htmlspecialchars($product['name']); ?></a>
                            </h3>
                            <div class="product-rating">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <?php if ($i <= floor($product['rating'])): ?>
                                        <i class="fas fa-star"></i>
                                    <?php elseif ($i - 0.5 <= $product['rating']): ?>
                                        <i class="fas fa-star-half-alt"></i>
                                    <?php else: ?>
                                        <i class="far fa-star"></i>
                                    <?php endif; ?>
                                <?php endfor; ?>
                                <span>(<?php echo $product['reviews_count']; ?>)</span>
                            </div>
                            <div class="product-price">
                                <span class="current-price"><?php echo CURRENCY_SYMBOL . number_format($product['price']); ?></span>
                                <?php if ($product['mrp'] > $product['price']): ?>
                                <span class="original-price"><?php echo CURRENCY_SYMBOL . number_format($product['mrp']); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php require_once '../includes/footer.php'; ?>
