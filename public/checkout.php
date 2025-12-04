<?php
require_once '../includes/config.php';
require_once '../includes/models/Order.php';

if (isset($_GET['buy_now']) && is_numeric($_GET['buy_now'])) {
    $productId = (int)$_GET['buy_now'];
    $product = Product::getById($productId);
    if ($product) {
        $_SESSION['cart'][$productId] = [
            'id' => $productId,
            'name' => $product['name'],
            'price' => $product['price'],
            'image' => $product['image'],
            'category_name' => $product['category_name'],
            'quantity' => 1
        ];
    }
}

$cartItems = $_SESSION['cart'] ?? [];
if (empty($cartItems)) {
    header('Location: cart.php');
    exit;
}

$subtotal = getCartTotal();
$shipping = getShippingFee($subtotal);
$total = $subtotal + $shipping;

$success = false;
$orderNumber = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $state = trim($_POST['state'] ?? '');
    $pincode = trim($_POST['pincode'] ?? '');
    $paymentMethod = $_POST['payment_method'] ?? 'cod';
    $notes = trim($_POST['notes'] ?? '');
    
    if (empty($name) || empty($phone) || empty($address) || empty($city) || empty($pincode)) {
        $error = 'Please fill in all required fields.';
    } else {
        try {
            $orderData = [
                'customer_name' => $name,
                'customer_email' => $email,
                'customer_phone' => $phone,
                'shipping_address' => $address,
                'city' => $city,
                'state' => $state,
                'pincode' => $pincode,
                'payment_method' => $paymentMethod,
                'subtotal' => $subtotal,
                'shipping_fee' => $shipping,
                'total' => $total,
                'notes' => $notes
            ];
            
            $items = [];
            foreach ($cartItems as $item) {
                $items[] = [
                    'product_id' => $item['id'],
                    'product_name' => $item['name'],
                    'product_image' => $item['image'],
                    'unit_price' => $item['price'],
                    'quantity' => $item['quantity']
                ];
            }
            
            $result = Order::create($orderData, $items);
            $orderNumber = $result['order_number'];
            
            if ($paymentMethod === 'cod') {
                $_SESSION['cart'] = [];
                $success = true;
            } else {
                $_SESSION['pending_order_id'] = $result['id'];
                $_SESSION['pending_order_number'] = $orderNumber;
                header('Location: payment.php?order_id=' . $result['id']);
                exit;
            }
        } catch (Exception $e) {
            $error = 'Failed to place order. Please try again.';
        }
    }
}

$pageTitle = 'Checkout';
require_once '../includes/header.php';
?>

<!-- Page Banner -->
<section class="page-banner">
    <div class="page-banner-content">
        <h1>Checkout</h1>
        <div class="breadcrumb">
            <a href="index.php">Home</a>
            <span>/</span>
            <a href="cart.php">Cart</a>
            <span>/</span>
            <span>Checkout</span>
        </div>
    </div>
</section>

<section class="checkout-section">
    <div class="container">
        <?php if ($success): ?>
        <div class="order-success reveal">
            <div style="text-align: center; padding: 60px 20px;">
                <i class="fas fa-check-circle" style="font-size: 80px; color: #28a745; margin-bottom: 20px;"></i>
                <h2>Order Placed Successfully!</h2>
                <p style="font-size: 18px; color: #666; margin: 20px 0;">Thank you for your order. Your order number is:</p>
                <div style="background: #f8f9fa; padding: 20px; border-radius: 10px; display: inline-block; margin: 20px 0;">
                    <h3 style="color: var(--primary-gold); font-size: 28px;"><?php echo htmlspecialchars($orderNumber); ?></h3>
                </div>
                <p style="color: #666;">We will contact you shortly to confirm your order.</p>
                <p style="color: #666;"><strong>Payment Method:</strong> Cash on Delivery</p>
                <a href="products.php" class="view-all-btn" style="margin-top: 30px;">
                    Continue Shopping <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
        <?php else: ?>
        
        <?php if ($error): ?>
        <div class="alert alert-error" style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="checkout-layout">
                <div class="checkout-form reveal-left">
                    <h3>Shipping Details</h3>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="name">Full Name *</label>
                            <input type="text" id="name" name="name" placeholder="Enter your full name" required>
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone Number *</label>
                            <input type="tel" id="phone" name="phone" placeholder="Enter your phone number" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" placeholder="Enter your email (optional)">
                    </div>
                    
                    <div class="form-group">
                        <label for="address">Shipping Address *</label>
                        <textarea id="address" name="address" placeholder="Enter your complete address" required></textarea>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="city">City *</label>
                            <input type="text" id="city" name="city" placeholder="Enter city" required>
                        </div>
                        <div class="form-group">
                            <label for="state">State</label>
                            <input type="text" id="state" name="state" placeholder="Enter state">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="pincode">PIN Code *</label>
                        <input type="text" id="pincode" name="pincode" placeholder="Enter PIN code" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="notes">Order Notes (Optional)</label>
                        <textarea id="notes" name="notes" placeholder="Any special instructions for delivery"></textarea>
                    </div>
                    
                    <h3 style="margin-top: 30px;">Payment Method</h3>
                    
                    <div class="payment-methods">
                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="cod" checked>
                            <div class="payment-option-content">
                                <i class="fas fa-money-bill-wave"></i>
                                <div>
                                    <strong>Cash on Delivery (COD)</strong>
                                    <p>Pay when you receive your order</p>
                                </div>
                            </div>
                        </label>
                        
                        <?php if (!empty(RAZORPAY_KEY_ID)): ?>
                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="razorpay">
                            <div class="payment-option-content">
                                <i class="fas fa-credit-card"></i>
                                <div>
                                    <strong>Pay Online (Razorpay)</strong>
                                    <p>Credit/Debit Card, UPI, NetBanking</p>
                                </div>
                            </div>
                        </label>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="checkout-summary reveal-right">
                    <h3>Order Summary</h3>
                    
                    <div class="checkout-items">
                        <?php foreach ($cartItems as $item): ?>
                        <div class="checkout-item">
                            <img src="assets/images/products/<?php echo htmlspecialchars($item['image']); ?>" alt="">
                            <div class="checkout-item-info">
                                <h4><?php echo htmlspecialchars($item['name']); ?></h4>
                                <p>Qty: <?php echo $item['quantity']; ?></p>
                            </div>
                            <span class="checkout-item-price"><?php echo CURRENCY_SYMBOL . number_format($item['price'] * $item['quantity']); ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="checkout-totals">
                        <div class="summary-row">
                            <span>Subtotal</span>
                            <span><?php echo CURRENCY_SYMBOL . number_format($subtotal); ?></span>
                        </div>
                        <div class="summary-row">
                            <span>Shipping</span>
                            <span><?php echo $shipping == 0 ? 'Free' : CURRENCY_SYMBOL . number_format($shipping); ?></span>
                        </div>
                        <div class="summary-row total">
                            <span>Total</span>
                            <span><?php echo CURRENCY_SYMBOL . number_format($total); ?></span>
                        </div>
                    </div>
                    
                    <button type="submit" class="checkout-btn">
                        <i class="fas fa-lock"></i> Place Order
                    </button>
                    
                    <p style="text-align: center; font-size: 0.85rem; color: #888; margin-top: 15px;">
                        <i class="fas fa-shield-alt"></i> Your order is secure and encrypted
                    </p>
                </div>
            </div>
        </form>
        <?php endif; ?>
    </div>
</section>

<style>
.checkout-section {
    padding: 60px 0;
}

.checkout-layout {
    display: grid;
    grid-template-columns: 1.5fr 1fr;
    gap: 40px;
}

.checkout-form, .checkout-summary {
    background: #fff;
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.05);
}

.checkout-form h3, .checkout-summary h3 {
    margin-bottom: 25px;
    color: var(--dark-bg);
    font-size: 20px;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    color: var(--dark-bg);
}

.form-group input, .form-group textarea {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid #eee;
    border-radius: 8px;
    font-size: 15px;
    transition: all 0.3s ease;
}

.form-group input:focus, .form-group textarea:focus {
    outline: none;
    border-color: var(--primary-gold);
}

.form-group textarea {
    min-height: 80px;
    resize: vertical;
}

.payment-methods {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.payment-option {
    cursor: pointer;
}

.payment-option input {
    display: none;
}

.payment-option-content {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 20px;
    border: 2px solid #eee;
    border-radius: 10px;
    transition: all 0.3s ease;
}

.payment-option input:checked + .payment-option-content {
    border-color: var(--primary-gold);
    background: rgba(201, 162, 39, 0.05);
}

.payment-option-content i {
    font-size: 28px;
    color: var(--primary-gold);
}

.payment-option-content strong {
    display: block;
    color: var(--dark-bg);
}

.payment-option-content p {
    font-size: 13px;
    color: #888;
    margin: 0;
}

.checkout-items {
    margin-bottom: 20px;
}

.checkout-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px 0;
    border-bottom: 1px solid #eee;
}

.checkout-item img {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 8px;
}

.checkout-item-info {
    flex: 1;
}

.checkout-item-info h4 {
    font-size: 14px;
    margin-bottom: 5px;
}

.checkout-item-info p {
    color: #888;
    font-size: 13px;
    margin: 0;
}

.checkout-item-price {
    font-weight: 600;
    color: var(--primary-gold);
}

.checkout-totals {
    padding-top: 20px;
    border-top: 1px solid #eee;
}

.checkout-btn {
    width: 100%;
    padding: 15px;
    background: var(--primary-gold);
    color: #fff;
    border: none;
    border-radius: 10px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    margin-top: 20px;
    transition: all 0.3s ease;
}

.checkout-btn:hover {
    background: #b8922a;
}

@media (max-width: 768px) {
    .checkout-layout {
        grid-template-columns: 1fr;
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
}
</style>

<?php require_once '../includes/footer.php'; ?>
