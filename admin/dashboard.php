<?php
session_start();
require_once '../includes/database.php';
require_once '../includes/models/Product.php';
require_once '../includes/models/Category.php';
require_once '../includes/models/Order.php';
require_once '../includes/models/ContactInquiry.php';

$pageTitle = 'Dashboard';
$currentPage = 'dashboard';

$totalProducts = Product::getTotalCount();
$totalCategories = count(Category::getAll());
$totalOrders = Order::getTotalCount();
$pendingOrders = Order::getTotalCount('pending');
$totalRevenue = Order::getTotalRevenue();
$newInquiries = ContactInquiry::getNewCount();
$recentOrders = Order::getRecentOrders(5);

require_once 'views/layouts/header.php';
?>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon blue">
            <i class="fas fa-box"></i>
        </div>
        <div class="stat-info">
            <h3><?php echo $totalProducts; ?></h3>
            <p>Total Products</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green">
            <i class="fas fa-shopping-bag"></i>
        </div>
        <div class="stat-info">
            <h3><?php echo $totalOrders; ?></h3>
            <p>Total Orders</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon gold">
            <i class="fas fa-rupee-sign"></i>
        </div>
        <div class="stat-info">
            <h3>₹<?php echo number_format($totalRevenue); ?></h3>
            <p>Total Revenue</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon red">
            <i class="fas fa-clock"></i>
        </div>
        <div class="stat-info">
            <h3><?php echo $pendingOrders; ?></h3>
            <p>Pending Orders</p>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2>Recent Orders</h2>
        <a href="orders.php" class="btn btn-primary btn-sm">View All</a>
    </div>
    <div class="card-body">
        <?php if (empty($recentOrders)): ?>
        <div class="empty-state">
            <i class="fas fa-shopping-cart"></i>
            <p>No orders yet</p>
        </div>
        <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Customer</th>
                    <th>Total</th>
                    <th>Payment</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recentOrders as $order): ?>
                <tr>
                    <td><a href="order-view.php?id=<?php echo $order['id']; ?>"><?php echo htmlspecialchars($order['order_number']); ?></a></td>
                    <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                    <td>₹<?php echo number_format($order['total']); ?></td>
                    <td>
                        <span class="badge badge-<?php echo $order['payment_status'] === 'paid' ? 'success' : 'warning'; ?>">
                            <?php echo ucfirst($order['payment_status']); ?>
                        </span>
                    </td>
                    <td>
                        <span class="badge badge-<?php 
                            echo match($order['order_status']) {
                                'delivered' => 'success',
                                'cancelled' => 'danger',
                                'shipped' => 'info',
                                default => 'warning'
                            };
                        ?>">
                            <?php echo ucfirst($order['order_status']); ?>
                        </span>
                    </td>
                    <td><?php echo date('d M Y', strtotime($order['placed_at'])); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'views/layouts/footer.php'; ?>
