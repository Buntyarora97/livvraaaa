<?php
if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}
require_once __DIR__ . '/../../../includes/models/Order.php';
require_once __DIR__ . '/../../../includes/models/ContactInquiry.php';

$pendingOrders = Order::getTotalCount('pending');
$newInquiries = ContactInquiry::getNewCount();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Admin'; ?> - LIVVRA Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>
    <div class="admin-container">
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-logo">
                    LIVVRA
                    <span>Admin Panel</span>
                </div>
            </div>
            <nav class="sidebar-menu">
                <a href="dashboard.php" class="menu-item <?php echo ($currentPage ?? '') === 'dashboard' ? 'active' : ''; ?>">
                    <i class="fas fa-home"></i> Dashboard
                </a>
                
                <div class="menu-section">Catalog</div>
                <a href="categories.php" class="menu-item <?php echo ($currentPage ?? '') === 'categories' ? 'active' : ''; ?>">
                    <i class="fas fa-tags"></i> Categories
                </a>
                <a href="products.php" class="menu-item <?php echo ($currentPage ?? '') === 'products' ? 'active' : ''; ?>">
                    <i class="fas fa-box"></i> Products
                </a>
                
                <div class="menu-section">Sales</div>
                <a href="orders.php" class="menu-item <?php echo ($currentPage ?? '') === 'orders' ? 'active' : ''; ?>">
                    <i class="fas fa-shopping-bag"></i> Orders
                    <?php if ($pendingOrders > 0): ?>
                    <span class="badge badge-warning" style="margin-left: auto;"><?php echo $pendingOrders; ?></span>
                    <?php endif; ?>
                </a>
                
                <div class="menu-section">Support</div>
                <a href="inquiries.php" class="menu-item <?php echo ($currentPage ?? '') === 'inquiries' ? 'active' : ''; ?>">
                    <i class="fas fa-envelope"></i> Inquiries
                    <?php if ($newInquiries > 0): ?>
                    <span class="badge badge-danger" style="margin-left: auto;"><?php echo $newInquiries; ?></span>
                    <?php endif; ?>
                </a>
                
                <div class="menu-section">Settings</div>
                <a href="settings.php" class="menu-item <?php echo ($currentPage ?? '') === 'settings' ? 'active' : ''; ?>">
                    <i class="fas fa-cog"></i> Settings
                </a>
                <a href="logout.php" class="menu-item">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </nav>
        </aside>
        
        <main class="main-content">
            <div class="top-bar">
                <h1 class="page-title"><?php echo $pageTitle ?? 'Dashboard'; ?></h1>
                <div class="user-menu">
                    <span>Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
                    <div class="user-avatar">
                        <?php echo strtoupper(substr($_SESSION['admin_username'], 0, 2)); ?>
                    </div>
                </div>
            </div>
