<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' . SITE_NAME : SITE_NAME . ' - ' . SITE_TAGLINE; ?></title>
    <meta name="description" content="LIVVRA - Premium Ayurvedic and Herbal products for better health. Live Better Live Strong with Dr Tridosha Herbotech.">
    <link rel="icon" type="image/png" href="assets/images/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/animations.css">
</head>
<body>
    <!-- Preloader -->
    <div class="preloader" id="preloader">
        <div class="preloader-inner">
            <div class="preloader-logo">
                <img src="assets/images/logo.png" alt="LIVVRA">
            </div>
            <div class="preloader-spinner"></div>
        </div>
    </div>

    <!-- Header -->
    <header class="header" id="header">
        <div class="header-top">
            <div class="container">
                <div class="header-top-content">
                    <div class="header-contact">
                        <span><i class="fas fa-envelope"></i> <?php echo SITE_EMAIL; ?></span>
                        <span><i class="fas fa-phone"></i> <?php echo SITE_PHONE; ?></span>
                    </div>
                    <div class="header-social">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
            </div>
        </div>
        <nav class="navbar">
            <div class="container">
                <div class="navbar-content">
                    <a href="index.php" class="logo">
                        <img src="assets/images/logo.png" alt="LIVVRA Logo">
                        <div class="logo-text">
                            <span class="logo-name">LIVVRA</span>
                            <span class="logo-tagline">Live Better Live Strong</span>
                        </div>
                    </a>
                    
                    <form class="search-box" action="products.php" method="get">
                        <input type="text" name="search" placeholder="Search for products..." id="searchInput">
                        <button type="submit"><i class="fas fa-search"></i></button>
                    </form>
                    
                    <div class="nav-actions">
                        <a href="admin/" class="nav-action-btn" title="Admin">
                            <i class="fas fa-user"></i>
                            <span>Admin</span>
                        </a>
                        <a href="cart.php" class="nav-action-btn cart-btn" title="Cart">
                            <i class="fas fa-shopping-cart"></i>
                            <span>Cart</span>
                            <span class="cart-count"><?php echo getCartCount(); ?></span>
                        </a>
                        <button class="mobile-menu-btn" id="mobileMenuBtn">
                            <span></span>
                            <span></span>
                            <span></span>
                        </button>
                    </div>
                </div>
                
                <div class="nav-menu" id="navMenu">
                    <ul class="nav-links">
                        <li><a href="index.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">Home</a></li>
                        <li class="has-dropdown">
                            <a href="products.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'products.php' ? 'active' : ''; ?>">Products <i class="fas fa-chevron-down"></i></a>
                            <ul class="dropdown-menu">
                                <?php foreach ($categories as $cat): ?>
                                <li><a href="products.php?category=<?php echo htmlspecialchars($cat['slug']); ?>"><i class="fas <?php echo htmlspecialchars($cat['icon_class']); ?>"></i> <?php echo htmlspecialchars($cat['name']); ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                        <li><a href="about.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'about.php' ? 'active' : ''; ?>">About Us</a></li>
                        <li><a href="contact.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'contact.php' ? 'active' : ''; ?>">Contact</a></li>
                    </ul>
                </div>
            </div>
        </nav>
        
        <!-- Category Bar -->
        <div class="category-bar">
            <div class="container">
                <div class="category-scroll">
                    <span class="category-label"><i class="fas fa-filter"></i> SELECT CONCERN:</span>
                    <?php foreach ($categories as $cat): ?>
                    <a href="products.php?category=<?php echo htmlspecialchars($cat['slug']); ?>" class="category-chip">
                        <i class="fas <?php echo htmlspecialchars($cat['icon_class']); ?>"></i>
                        <span><?php echo htmlspecialchars($cat['name']); ?></span>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </header>
    
    <main class="main-content">
