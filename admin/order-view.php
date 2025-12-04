<?php
session_start();
require_once '../includes/database.php';
require_once '../includes/models/Order.php';

$pageTitle = 'Order Details';
$currentPage = 'orders';

$id = $_GET['id'] ?? 0;
$order = Order::getById($id);

if (!$order) {
    header('Location: orders.php');
    exit;
}

$items = Order::getItems($id);

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_status'])) {
        try {
            Order::updateStatus($id, $_POST['order_status']);
            if ($_POST['order_status'] === 'delivered' && $order['payment_method'] === 'cod') {
                Order::updatePaymentStatus($id, 'paid');
            }
            $success = 'Order updated successfully!';
            $order = Order::getById($id);
        } catch (Exception $e) {
            $error = 'Failed to update order.';
        }
    }
}

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
        <h2>Order: <?php echo htmlspecialchars($order['order_number']); ?></h2>
        <a href="orders.php" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Back to Orders
        </a>
    </div>
    <div class="card-body">
        <div class="order-details">
            <div class="order-info-card">
                <h4><i class="fas fa-user"></i> Customer Information</h4>
                <p><strong>Name:</strong> <?php echo htmlspecialchars($order['customer_name']); ?></p>
                <p><strong>Phone:</strong> <?php echo htmlspecialchars($order['customer_phone']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($order['customer_email'] ?: 'N/A'); ?></p>
            </div>
            
            <div class="order-info-card">
                <h4><i class="fas fa-map-marker-alt"></i> Shipping Address</h4>
                <p><?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?></p>
                <p><?php echo htmlspecialchars($order['city']); ?>, <?php echo htmlspecialchars($order['state']); ?></p>
                <p>PIN: <?php echo htmlspecialchars($order['pincode']); ?></p>
            </div>
            
            <div class="order-info-card">
                <h4><i class="fas fa-credit-card"></i> Payment Details</h4>
                <p><strong>Method:</strong> 
                    <span class="badge badge-<?php echo $order['payment_method'] === 'cod' ? 'warning' : 'info'; ?>">
                        <?php echo strtoupper($order['payment_method']); ?>
                    </span>
                </p>
                <p><strong>Status:</strong> 
                    <span class="badge badge-<?php echo $order['payment_status'] === 'paid' ? 'success' : 'warning'; ?>">
                        <?php echo ucfirst($order['payment_status']); ?>
                    </span>
                </p>
                <?php if ($order['razorpay_payment_id']): ?>
                <p><strong>Razorpay ID:</strong> <?php echo htmlspecialchars($order['razorpay_payment_id']); ?></p>
                <?php endif; ?>
            </div>
            
            <div class="order-info-card">
                <h4><i class="fas fa-truck"></i> Order Status</h4>
                <form method="POST" action="">
                    <input type="hidden" name="update_status" value="1">
                    <select name="order_status" class="form-control" style="margin-bottom: 10px;">
                        <option value="pending" <?php echo $order['order_status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="confirmed" <?php echo $order['order_status'] === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                        <option value="processing" <?php echo $order['order_status'] === 'processing' ? 'selected' : ''; ?>>Processing</option>
                        <option value="shipped" <?php echo $order['order_status'] === 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                        <option value="delivered" <?php echo $order['order_status'] === 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                        <option value="cancelled" <?php echo $order['order_status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                    </select>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-save"></i> Update Status
                    </button>
                </form>
                <p style="margin-top: 10px;"><strong>Placed:</strong> <?php echo date('d M Y, h:i A', strtotime($order['placed_at'])); ?></p>
                <?php if ($order['delivered_at']): ?>
                <p><strong>Delivered:</strong> <?php echo date('d M Y, h:i A', strtotime($order['delivered_at'])); ?></p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="order-items-table" style="margin-top: 30px;">
            <h4 style="margin-bottom: 15px;"><i class="fas fa-box"></i> Order Items</h4>
            <table class="table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                    <tr>
                        <td>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <?php if ($item['product_image']): ?>
                                <img src="../public/assets/images/products/<?php echo htmlspecialchars($item['product_image']); ?>" alt="" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                                <?php endif; ?>
                                <?php echo htmlspecialchars($item['product_name']); ?>
                            </div>
                        </td>
                        <td>₹<?php echo number_format($item['unit_price']); ?></td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td>₹<?php echo number_format($item['line_total']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" style="text-align: right;"><strong>Subtotal:</strong></td>
                        <td>₹<?php echo number_format($order['subtotal']); ?></td>
                    </tr>
                    <tr>
                        <td colspan="3" style="text-align: right;"><strong>Shipping:</strong></td>
                        <td>₹<?php echo number_format($order['shipping_fee']); ?></td>
                    </tr>
                    <tr style="font-size: 18px;">
                        <td colspan="3" style="text-align: right;"><strong>Total:</strong></td>
                        <td><strong>₹<?php echo number_format($order['total']); ?></strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        
        <?php if ($order['notes']): ?>
        <div style="margin-top: 20px;">
            <h4><i class="fas fa-sticky-note"></i> Order Notes</h4>
            <p><?php echo nl2br(htmlspecialchars($order['notes'])); ?></p>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'views/layouts/footer.php'; ?>
