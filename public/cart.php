<?php
require_once '../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
        
        switch ($_POST['action']) {
            case 'add':
                $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
                $product = Product::getById($productId);
                if ($product) {
                    if (isset($_SESSION['cart'][$productId])) {
                        $_SESSION['cart'][$productId]['quantity'] += $quantity;
                    } else {
                        $_SESSION['cart'][$productId] = [
                            'id' => $productId,
                            'name' => $product['name'],
                            'price' => $product['price'],
                            'image' => $product['image'],
                            'category_name' => $product['category_name'],
                            'quantity' => $quantity
                        ];
                    }
                }
                break;
                
            case 'update':
                $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
                if (isset($_SESSION['cart'][$productId])) {
                    if ($quantity > 0) {
                        $_SESSION['cart'][$productId]['quantity'] = $quantity;
                    } else {
                        unset($_SESSION['cart'][$productId]);
                    }
                }
                break;
                
            case 'remove':
                if (isset($_SESSION['cart'][$productId])) {
                    unset($_SESSION['cart'][$productId]);
                }
                break;
        }
        
        header('Location: cart.php');
        exit;
    }
}

$pageTitle = 'Shopping Cart';
$cartItems = $_SESSION['cart'] ?? [];
$subtotal = getCartTotal();
$shipping = getShippingFee($subtotal);
$total = $subtotal + $shipping;

require_once '../includes/header.php';
?>

<!-- Page Banner -->
<section class="page-banner">
    <div class="page-banner-content">
        <h1>Shopping Cart</h1>
        <div class="breadcrumb">
            <a href="index.php">Home</a>
            <span>/</span>
            <span>Cart</span>
        </div>
    </div>
</section>

<!-- Cart Section -->
<section class="cart-section">
    <div class="container">
        <?php if (empty($cartItems)): ?>
        <div class="empty-cart reveal">
            <i class="fas fa-shopping-cart"></i>
            <h3>Your Cart is Empty</h3>
            <p>Looks like you haven't added any products to your cart yet.</p>
            <a href="products.php" class="view-all-btn">
                Start Shopping <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        <?php else: ?>
        <div class="cart-layout">
            <!-- Cart Items -->
            <div class="cart-items reveal-left">
                <div class="cart-header">
                    <span>Product</span>
                    <span>Price</span>
                    <span>Quantity</span>
                    <span>Total</span>
                    <span></span>
                </div>
                
                <?php foreach ($cartItems as $item): ?>
                <div class="cart-item">
                    <div class="cart-product">
                        <div class="cart-product-image">
                            <img src="assets/images/products/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                        </div>
                        <div>
                            <h4 class="cart-product-name"><?php echo htmlspecialchars($item['name']); ?></h4>
                            <span class="cart-product-category"><?php echo htmlspecialchars($item['category_name'] ?? ''); ?></span>
                        </div>
                    </div>
                    <div class="cart-price">
                        <?php echo CURRENCY_SYMBOL . number_format($item['price']); ?>
                    </div>
                    <div class="cart-quantity">
                        <form action="cart.php" method="post" style="display: flex;">
                            <input type="hidden" name="action" value="update">
                            <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                            <button type="button" class="minus-btn" onclick="this.parentNode.querySelector('input[name=quantity]').stepDown(); this.form.submit();">-</button>
                            <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" onchange="this.form.submit()">
                            <button type="button" class="plus-btn" onclick="this.parentNode.querySelector('input[name=quantity]').stepUp(); this.form.submit();">+</button>
                        </form>
                    </div>
                    <div class="cart-item-total">
                        <strong><?php echo CURRENCY_SYMBOL . number_format($item['price'] * $item['quantity']); ?></strong>
                    </div>
                    <form action="cart.php" method="post">
                        <input type="hidden" name="action" value="remove">
                        <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                        <button type="submit" class="cart-remove" title="Remove">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Cart Summary -->
            <div class="cart-summary reveal-right">
                <h3>Order Summary</h3>
                
                <div class="summary-row">
                    <span>Subtotal</span>
                    <span class="cart-subtotal"><?php echo CURRENCY_SYMBOL . number_format($subtotal); ?></span>
                </div>
                
                <div class="summary-row">
                    <span>Shipping</span>
                    <span><?php echo $shipping == 0 ? 'Free' : CURRENCY_SYMBOL . number_format($shipping); ?></span>
                </div>
                
                <?php if ($subtotal < FREE_SHIPPING_ABOVE): ?>
                <div style="background: #FFF3CD; color: #856404; padding: 12px; border-radius: 8px; font-size: 0.9rem; margin: 15px 0;">
                    <i class="fas fa-info-circle"></i> Add <?php echo CURRENCY_SYMBOL . number_format(FREE_SHIPPING_ABOVE - $subtotal); ?> more for free shipping!
                </div>
                <?php endif; ?>
                
                <div class="summary-row total">
                    <span>Total</span>
                    <span class="cart-total"><?php echo CURRENCY_SYMBOL . number_format($total); ?></span>
                </div>
                
                <a href="checkout.php" class="checkout-btn">
                    <i class="fas fa-lock"></i> Proceed to Checkout
                </a>
                
                <a href="products.php" class="continue-shopping">
                    <i class="fas fa-arrow-left"></i> Continue Shopping
                </a>
                
                <div style="margin-top: 25px; padding-top: 20px; border-top: 1px solid var(--border-color);">
                    <p style="font-size: 0.85rem; color: var(--text-light); text-align: center;">
                        <i class="fas fa-shield-alt" style="color: var(--primary-green);"></i> Secure Checkout - 100% Safe & Secure
                    </p>
                    <div style="display: flex; justify-content: center; gap: 15px; margin-top: 15px; font-size: 1.8rem; color: var(--text-muted);">
                        <i class="fab fa-cc-visa"></i>
                        <i class="fab fa-cc-mastercard"></i>
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once '../includes/footer.php'; ?>
