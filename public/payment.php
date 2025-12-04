<?php
require_once '../includes/config.php';
require_once '../includes/models/Order.php';

$orderId = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;

if (!$orderId || !isset($_SESSION['pending_order_id']) || $_SESSION['pending_order_id'] != $orderId) {
    header('Location: cart.php');
    exit;
}

$order = Order::getById($orderId);
if (!$order) {
    header('Location: cart.php');
    exit;
}

$razorpayKeyId = RAZORPAY_KEY_ID;

if (empty($razorpayKeyId)) {
    Order::updateStatus($orderId, 'pending');
    $_SESSION['cart'] = [];
    unset($_SESSION['pending_order_id']);
    unset($_SESSION['pending_order_number']);
    header('Location: order-success.php?order=' . $order['order_number']);
    exit;
}

$pageTitle = 'Complete Payment';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - LIVVRA</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .payment-container {
            background: #fff;
            border-radius: 20px;
            padding: 40px;
            max-width: 500px;
            width: 100%;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        .payment-logo {
            font-size: 32px;
            font-weight: 700;
            color: #C9A227;
            margin-bottom: 20px;
        }
        .payment-amount {
            font-size: 48px;
            font-weight: 700;
            color: #1a1a2e;
            margin: 20px 0;
        }
        .payment-amount span {
            font-size: 24px;
            color: #666;
        }
        .order-number {
            background: #f8f9fa;
            padding: 15px 25px;
            border-radius: 10px;
            margin: 20px 0;
            color: #666;
        }
        .order-number strong {
            color: #1a1a2e;
        }
        .pay-button {
            width: 100%;
            padding: 18px;
            background: linear-gradient(135deg, #C9A227 0%, #b8922a 100%);
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 20px;
            transition: all 0.3s ease;
        }
        .pay-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(201, 162, 39, 0.3);
        }
        .secure-badge {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
            color: #888;
            font-size: 14px;
        }
        .secure-badge i {
            color: #28a745;
        }
        .payment-methods {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 20px;
            font-size: 30px;
            color: #ccc;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #666;
            text-decoration: none;
        }
        .back-link:hover {
            color: #C9A227;
        }
    </style>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
</head>
<body>
    <div class="payment-container">
        <div class="payment-logo">LIVVRA</div>
        <h2>Complete Your Payment</h2>
        
        <div class="payment-amount">
            <span>₹</span><?php echo number_format($order['total']); ?>
        </div>
        
        <div class="order-number">
            Order: <strong><?php echo htmlspecialchars($order['order_number']); ?></strong>
        </div>
        
        <button id="payButton" class="pay-button">
            <i class="fas fa-lock"></i> Pay Now with Razorpay
        </button>
        
        <div class="secure-badge">
            <i class="fas fa-shield-alt"></i>
            <span>100% Secure Payment</span>
        </div>
        
        <div class="payment-methods">
            <i class="fab fa-cc-visa"></i>
            <i class="fab fa-cc-mastercard"></i>
            <i class="fas fa-university"></i>
            <i class="fas fa-mobile-alt"></i>
        </div>
        
        <a href="cart.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Cancel and return to cart
        </a>
    </div>
    
    <script>
        document.getElementById('payButton').onclick = function(e) {
            e.preventDefault();
            var payBtn = this;
            payBtn.disabled = true;
            payBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating order...';
            
            fetch('ajax/create_razorpay_order.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'order_id=<?php echo $orderId; ?>'
            })
            .then(function(response) { return response.json(); })
            .then(function(data) {
                if (!data.success) {
                    alert(data.message || 'Failed to create payment order');
                    payBtn.disabled = false;
                    payBtn.innerHTML = '<i class="fas fa-lock"></i> Pay Now with Razorpay';
                    return;
                }
                
                var options = {
                    "key": "<?php echo $razorpayKeyId; ?>",
                    "amount": data.amount,
                    "currency": data.currency,
                    "name": "LIVVRA",
                    "description": "Order #<?php echo $order['order_number']; ?>",
                    "order_id": data.razorpay_order_id,
                    "handler": function (response) {
                        var form = document.createElement('form');
                        form.method = 'POST';
                        form.action = 'verify-payment.php';
                        
                        var fields = {
                            'razorpay_payment_id': response.razorpay_payment_id,
                            'razorpay_order_id': response.razorpay_order_id,
                            'razorpay_signature': response.razorpay_signature,
                            'order_id': '<?php echo $orderId; ?>'
                        };
                        
                        for (var key in fields) {
                            var input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = key;
                            input.value = fields[key];
                            form.appendChild(input);
                        }
                        
                        document.body.appendChild(form);
                        form.submit();
                    },
                    "modal": {
                        "ondismiss": function() {
                            payBtn.disabled = false;
                            payBtn.innerHTML = '<i class="fas fa-lock"></i> Pay Now with Razorpay';
                        }
                    },
                    "prefill": {
                        "name": "<?php echo htmlspecialchars($order['customer_name']); ?>",
                        "email": "<?php echo htmlspecialchars($order['customer_email']); ?>",
                        "contact": "<?php echo htmlspecialchars($order['customer_phone']); ?>"
                    },
                    "theme": {
                        "color": "#C9A227"
                    }
                };
                var rzp = new Razorpay(options);
                rzp.open();
                payBtn.disabled = false;
                payBtn.innerHTML = '<i class="fas fa-lock"></i> Pay Now with Razorpay';
            })
            .catch(function(error) {
                console.error('Error:', error);
                alert('Failed to initialize payment. Please try again.');
                payBtn.disabled = false;
                payBtn.innerHTML = '<i class="fas fa-lock"></i> Pay Now with Razorpay';
            });
        };
    </script>
</body>
</html>
