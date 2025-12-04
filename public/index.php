<?php
require_once __DIR__ . '/../includes/config.php';
$pageTitle = 'Home';
require_once __DIR__ . '/../includes/header.php';

$featuredProducts = Product::getFeatured(8);
$allCategories = Category::getAll(true);
?>

<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <div class="hero-container">
            <div class="hero-content">
                <span class="hero-badge">
                    <i class="fas fa-leaf"></i> 100% Natural & Ayurvedic
                </span>
                <h1 class="hero-title">
                    Pure Herbal Products<br>
                    <span>Live Better, Live Strong</span>
                </h1>
                <p class="hero-subtitle">
                    Experience the power of Ayurveda with LIVVRA's premium range of natural health supplements and wellness products by Dr Tridosha Herbotech.
                </p>
                <div class="hero-offer">
                    <div class="hero-price">
                        <div class="price-tag">
                            <span>Starting from</span> ₹299
                        </div>
                        <a href="products.php" class="shop-now-btn">
                            SHOP NOW <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
                <p class="hero-discount">
                    <strong>Get 20% OFF</strong> on your first order | Use Code: <strong>LIVVRA20</strong>
                </p>
                <div class="hero-features">
                    <div class="hero-feature">
                        <div class="hero-feature-icon">
                            <i class="fas fa-truck"></i>
                        </div>
                        <span>Free Shipping</span>
                    </div>
                    <div class="hero-feature">
                        <div class="hero-feature-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <span>100% Genuine</span>
                    </div>
                    <div class="hero-feature">
                        <div class="hero-feature-icon">
                            <i class="fas fa-headset"></i>
                        </div>
                        <span>24/7 Support</span>
                    </div>
                </div>
            </div>
            <div class="hero-image">
                <div class="hero-bg-shape"></div>
                <div class="hero-product-wrapper">
                    <div class="hero-product-img">
                        <img src="assets/images/products/hero-product.jpg" alt="LIVVRA Products">
                    </div>
                </div>
                <div class="hero-floating-elements">
                    <i class="fas fa-leaf floating-leaf"></i>
                    <i class="fas fa-seedling floating-leaf"></i>
                    <i class="fas fa-spa floating-leaf"></i>
                    <i class="fas fa-leaf floating-leaf"></i>
                </div>
            </div>
        </div>
        <div class="hero-slider-dots">
            <span class="dot active"></span>
            <span class="dot"></span>
            <span class="dot"></span>
            <span class="dot"></span>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="section section-light">
    <div class="container">
        <div class="section-header reveal">
            <span class="section-badge"><i class="fas fa-th-large"></i> Browse Categories</span>
            <h2 class="section-title">Shop by <span>Health Concern</span></h2>
            <p class="section-subtitle">Find the perfect solution for your wellness needs</p>
        </div>
        <div class="categories-grid">
            <?php foreach ($allCategories as $cat): ?>
            <a href="products.php?category=<?php echo htmlspecialchars($cat['slug']); ?>" class="category-card reveal card-3d">
                <div class="category-icon">
                    <i class="fas <?php echo htmlspecialchars($cat['icon_class']); ?>"></i>
                </div>
                <h3><?php echo htmlspecialchars($cat['name']); ?></h3>
                <p><?php echo htmlspecialchars($cat['description']); ?></p>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Featured Products Section -->
<section class="section">
    <div class="container">
        <div class="section-header reveal">
            <span class="section-badge"><i class="fas fa-star"></i> Featured Products</span>
            <h2 class="section-title">Our <span>Best Sellers</span></h2>
            <p class="section-subtitle">Premium quality products trusted by thousands of customers</p>
        </div>
        <div class="products-grid">
            <?php foreach ($featuredProducts as $product): 
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
                            <i class="fas fa-star<?php echo $i <= floor($product['rating']) ? '' : '-half-alt'; ?>"></i>
                        <?php endfor; ?>
                        <span>(<?php echo $product['reviews_count']; ?> reviews)</span>
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
        <div style="text-align: center;">
            <a href="products.php" class="view-all-btn">
                View All Products <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</section>

<!-- Benefits Section -->
<section class="section section-dark">
    <div class="container">
        <div class="section-header reveal">
            <span class="section-badge"><i class="fas fa-award"></i> Why LIVVRA</span>
            <h2 class="section-title">The <span>LIVVRA</span> Difference</h2>
            <p class="section-subtitle">What makes us the trusted choice for natural wellness</p>
        </div>
        <div class="benefits-grid">
            <div class="benefit-card reveal">
                <div class="benefit-icon">
                    <i class="fas fa-leaf"></i>
                </div>
                <h3>100% Natural</h3>
                <p>Pure herbal ingredients sourced from organic farms across India</p>
            </div>
            <div class="benefit-card reveal delay-100">
                <div class="benefit-icon">
                    <i class="fas fa-flask"></i>
                </div>
                <h3>Lab Tested</h3>
                <p>Every product undergoes rigorous quality testing for safety</p>
            </div>
            <div class="benefit-card reveal delay-200">
                <div class="benefit-icon">
                    <i class="fas fa-certificate"></i>
                </div>
                <h3>GMP Certified</h3>
                <p>Manufactured in state-of-the-art GMP certified facilities</p>
            </div>
            <div class="benefit-card reveal delay-300">
                <div class="benefit-icon">
                    <i class="fas fa-user-md"></i>
                </div>
                <h3>Expert Formulated</h3>
                <p>Created by experienced Ayurvedic doctors and researchers</p>
            </div>
        </div>
    </div>
</section>

<!-- About Preview Section -->
<section class="section">
    <div class="container">
        <div class="about-preview">
            <div class="about-image reveal-left">
                <img src="assets/images/banners/about-preview.jpg" alt="About LIVVRA">
                <div class="about-badge">
                    <h4>15+</h4>
                    <p>Years Experience</p>
                </div>
            </div>
            <div class="about-content reveal-right">
                <span class="section-badge"><i class="fas fa-info-circle"></i> About Us</span>
                <h2>Dr Tridosha Herbotech - <span>Healing Through Nature</span></h2>
                <p>LIVVRA is the flagship brand of Dr Tridosha Herbotech Pvt Ltd, dedicated to bringing the ancient wisdom of Ayurveda to modern wellness. Our mission is to provide pure, effective, and sustainable health solutions.</p>
                <p>Based in Bathinda, Punjab, we have been serving customers across India with our premium range of herbal products that combine traditional knowledge with modern science.</p>
                <div class="about-features">
                    <div class="about-feature">
                        <i class="fas fa-check"></i>
                        <span>Premium Quality</span>
                    </div>
                    <div class="about-feature">
                        <i class="fas fa-check"></i>
                        <span>Sustainable Sourcing</span>
                    </div>
                    <div class="about-feature">
                        <i class="fas fa-check"></i>
                        <span>Cruelty Free</span>
                    </div>
                    <div class="about-feature">
                        <i class="fas fa-check"></i>
                        <span>Expert Support</span>
                    </div>
                </div>
                <a href="about.php" class="view-all-btn">
                    Learn More <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="section section-light">
    <div class="container">
        <div class="section-header reveal">
            <span class="section-badge"><i class="fas fa-quote-left"></i> Testimonials</span>
            <h2 class="section-title">What Our <span>Customers Say</span></h2>
            <p class="section-subtitle">Real stories from real people who trust LIVVRA</p>
        </div>
        <div class="testimonials-slider">
            <div class="testimonial-card reveal">
                <div class="testimonial-rating">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
                <p class="testimonial-text">"I've been using LIVVRA's Shilajit Gold for 3 months now and the results are amazing! My energy levels have improved significantly. Highly recommended!"</p>
                <div class="testimonial-author">
                    <div class="testimonial-avatar">RK</div>
                    <div class="testimonial-info">
                        <h4>Rajesh Kumar</h4>
                        <span>Verified Buyer</span>
                    </div>
                </div>
            </div>
            <div class="testimonial-card reveal delay-100">
                <div class="testimonial-rating">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
                <p class="testimonial-text">"The Ashwagandha capsules have really helped with my stress and sleep issues. Natural products that actually work. Thank you LIVVRA!"</p>
                <div class="testimonial-author">
                    <div class="testimonial-avatar">PS</div>
                    <div class="testimonial-info">
                        <h4>Priya Sharma</h4>
                        <span>Verified Buyer</span>
                    </div>
                </div>
            </div>
            <div class="testimonial-card reveal delay-200">
                <div class="testimonial-rating">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star-half-alt"></i>
                </div>
                <p class="testimonial-text">"Great quality products and excellent customer service. The Immunity Booster has helped my whole family stay healthy. Will definitely order again!"</p>
                <div class="testimonial-author">
                    <div class="testimonial-avatar">AS</div>
                    <div class="testimonial-info">
                        <h4>Amit Singh</h4>
                        <span>Verified Buyer</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Video Section -->
<section class="video-section">
    <div class="video-content reveal">
        <h2>Discover the Power of Ayurveda</h2>
        <p>Watch how LIVVRA products are made with care and precision using traditional methods and modern technology</p>
        <a href="#" class="play-btn">
            <i class="fas fa-play"></i>
        </a>
    </div>
</section>

<!-- Why Choose Us Section -->
<section class="section">
    <div class="container">
        <div class="section-header reveal">
            <span class="section-badge"><i class="fas fa-trophy"></i> Our Promise</span>
            <h2 class="section-title">Why Choose <span>LIVVRA</span></h2>
            <p class="section-subtitle">Your trusted partner in natural wellness</p>
        </div>
        <div class="why-choose-grid">
            <div class="why-choose-card reveal">
                <div class="why-choose-icon">
                    <i class="fas fa-shipping-fast"></i>
                </div>
                <h3>Free & Fast Delivery</h3>
                <p>Enjoy free shipping on orders above ₹<?php echo number_format(FREE_SHIPPING_ABOVE); ?> with delivery within 3-5 business days across India.</p>
            </div>
            <div class="why-choose-card reveal delay-100">
                <div class="why-choose-icon">
                    <i class="fas fa-undo-alt"></i>
                </div>
                <h3>Easy Returns</h3>
                <p>Not satisfied? Return products within 30 days for a full refund. No questions asked.</p>
            </div>
            <div class="why-choose-card reveal delay-200">
                <div class="why-choose-icon">
                    <i class="fas fa-lock"></i>
                </div>
                <h3>Secure Payments</h3>
                <p>100% secure payment processing with multiple payment options including COD.</p>
            </div>
        </div>
    </div>
</section>

<!-- Special Offers Section -->
<section class="section section-light">
    <div class="container">
        <div class="section-header reveal">
            <span class="section-badge"><i class="fas fa-tags"></i> Special Offers</span>
            <h2 class="section-title">Exclusive <span>Deals</span> For You</h2>
            <p class="section-subtitle">Limited time offers on our best-selling products</p>
        </div>
        <div class="offers-grid">
            <div class="offer-card offer-gold reveal-left">
                <div class="offer-content">
                    <div class="offer-discount">30% OFF</div>
                    <h3 class="offer-title">Wellness Bundle</h3>
                    <p class="offer-desc">Get Ashwagandha + Immunity Booster combo at special price</p>
                    <a href="products.php" class="offer-btn">
                        Shop Now <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                <img src="assets/images/banners/offer1.png" alt="Wellness Bundle" class="offer-image">
            </div>
            <div class="offer-card offer-green reveal-right">
                <div class="offer-content">
                    <div class="offer-discount">25% OFF</div>
                    <h3 class="offer-title">Men's Health Pack</h3>
                    <p class="offer-desc">Shilajit Gold + Energy Drink for ultimate vitality</p>
                    <a href="products.php?category=mens-health" class="offer-btn">
                        Shop Now <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                <img src="assets/images/banners/offer2.png" alt="Men's Health Pack" class="offer-image">
            </div>
        </div>
    </div>
</section>

<!-- Newsletter Section -->
<section class="newsletter">
    <div class="container">
        <div class="newsletter-content">
            <div class="newsletter-text">
                <h3>Subscribe to Our Newsletter</h3>
                <p>Get exclusive offers, health tips, and new product updates directly in your inbox</p>
            </div>
            <form class="newsletter-form" action="#" method="post">
                <input type="email" name="email" placeholder="Enter your email address" required>
                <button type="submit">Subscribe</button>
            </form>
        </div>
    </div>
</section>

<!-- Map Section -->
<section class="map-section">
    <div class="container">
        <div class="map-container">
            <div class="map-info reveal-left">
                <h3>Visit Our <span>Store</span></h3>
                <div class="map-details">
                    <div class="map-detail">
                        <i class="fas fa-map-marker-alt"></i>
                        <div class="map-detail-text">
                            <h4>Address</h4>
                            <p><?php echo SITE_ADDRESS; ?></p>
                        </div>
                    </div>
                    <div class="map-detail">
                        <i class="fas fa-envelope"></i>
                        <div class="map-detail-text">
                            <h4>Email</h4>
                            <a href="mailto:<?php echo SITE_EMAIL; ?>"><?php echo SITE_EMAIL; ?></a>
                        </div>
                    </div>
                    <div class="map-detail">
                        <i class="fas fa-phone"></i>
                        <div class="map-detail-text">
                            <h4>Phone</h4>
                            <a href="tel:<?php echo SITE_PHONE; ?>"><?php echo SITE_PHONE; ?></a>
                        </div>
                    </div>
                    <div class="map-detail">
                        <i class="fas fa-clock"></i>
                        <div class="map-detail-text">
                            <h4>Working Hours</h4>
                            <p>Mon - Sat: 9:00 AM - 7:00 PM</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="map-frame reveal-right">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3444.5!2d74.9455!3d30.2110!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMzDCsDEyJzM5LjYiTiA3NMKwNTYnNDMuOCJF!5e0!3m2!1sen!2sin!4v1234567890" allowfullscreen="" loading="lazy"></iframe>
            </div>
        </div>
    </div>
</section>

<?php require_once '../includes/footer.php'; ?>
