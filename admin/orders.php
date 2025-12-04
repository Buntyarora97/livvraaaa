<?php
session_start();
require_once '../includes/database.php';
require_once '../includes/models/Order.php';

$pageTitle = 'Orders';
$currentPage = 'orders';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_status'])) {
        try {
            Order::updateStatus($_POST['order_id'], $_POST['order_status']);
            $success = 'Order status updated successfully!';
        } catch (Exception $e) {
            $error = 'Failed to update order status.';
        }
    }
}

$statusFilter = $_GET['status'] ?? null;
$orders = Order::getAll($statusFilter);

require_once 'views/layouts/header.php';
?>

<?php if ($success): ?>
<div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
<?php endif; ?>

<?php if ($error): ?>
<div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h2>All Orders (<?php echo count($orders); ?>)</h2>
        <div style="display: flex; gap: 10px;">
            <a href="orders.php" class="btn btn-<?php echo !$statusFilter ? 'primary' : 'secondary'; ?> btn-sm">All</a>
            <a href="orders.php?status=pending" class="btn btn-<?php echo $statusFilter === 'pending' ? 'primary' : 'secondary'; ?> btn-sm">Pending</a>
            <a href="orders.php?status=confirmed" class="btn btn-<?php echo $statusFilter === 'confirmed' ? 'primary' : 'secondary'; ?> btn-sm">Confirmed</a>
            <a href="orders.php?status=shipped" class="btn btn-<?php echo $statusFilter === 'shipped' ? 'primary' : 'secondary'; ?> btn-sm">Shipped</a>
            <a href="orders.php?status=delivered" class="btn btn-<?php echo $statusFilter === 'delivered' ? 'primary' : 'secondary'; ?> btn-sm">Delivered</a>
        </div>
    </div>
    <div class="card-body">
        <?php if (empty($orders)): ?>
        <div class="empty-state">
            <i class="fas fa-shopping-bag"></i>
            <p>No orders found.</p>
        </div>
        <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Customer</th>
                    <th>Phone</th>
                    <th>Total</th>
                    <th>Payment</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                <tr>
                    <td>
                        <a href="order-view.php?id=<?php echo $order['id']; ?>">
                            <strong><?php echo htmlspecialchars($order['order_number']); ?></strong>
                        </a>
                    </td>
                    <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                    <td><?php echo htmlspecialchars($order['customer_phone']); ?></td>
                    <td><strong>₹<?php echo number_format($order['total']); ?></strong></td>
                    <td>
                        <span class="badge badge-<?php echo $order['payment_method'] === 'cod' ? 'warning' : 'info'; ?>">
                            <?php echo strtoupper($order['payment_method']); ?>
                        </span>
                        <br>
                        <small class="badge badge-<?php echo $order['payment_status'] === 'paid' ? 'success' : 'warning'; ?>">
                            <?php echo ucfirst($order['payment_status']); ?>
                        </small>
                    </td>
                    <td>
                        <form method="POST" action="" style="display: inline;">
                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                            <input type="hidden" name="update_status" value="1">
                            <select name="order_status" class="status-select" onchange="this.form.submit()">
                                <option value="pending" <?php echo $order['order_status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="confirmed" <?php echo $order['order_status'] === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                <option value="processing" <?php echo $order['order_status'] === 'processing' ? 'selected' : ''; ?>>Processing</option>
                                <option value="shipped" <?php echo $order['order_status'] === 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                <option value="delivered" <?php echo $order['order_status'] === 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                <option value="cancelled" <?php echo $order['order_status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                            </select>
                        </form>
                    </td>
                    <td><?php echo date('d M Y', strtotime($order['placed_at'])); ?></td>
                    <td>
                        <a href="order-view.php?id=<?php echo $order['id']; ?>" class="btn btn-secondary btn-sm">
                            <i class="fas fa-eye"></i> View
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'views/layouts/footer.php'; ?>
