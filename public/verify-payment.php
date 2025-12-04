<?php
require_once '../includes/config.php';
require_once '../includes/models/Order.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: cart.php');
    exit;
}

$orderId = isset($_POST['order_id']) ? (int)$_POST['order_id'] : 0;
$razorpayPaymentId = isset($_POST['razorpay_payment_id']) ? trim($_POST['razorpay_payment_id']) : '';
$razorpayOrderId = isset($_POST['razorpay_order_id']) ? trim($_POST['razorpay_order_id']) : '';
$razorpaySignature = isset($_POST['razorpay_signature']) ? trim($_POST['razorpay_signature']) : '';

if (!$orderId || !$razorpayPaymentId || !$razorpaySignature || !$razorpayOrderId) {
    error_log("Payment verification failed: Missing required parameters - order_id: $orderId, payment_id: $razorpayPaymentId, signature: $razorpaySignature, razorpay_order_id: $razorpayOrderId");
    header('Location: cart.php?error=payment_failed');
    exit;
}

$order = Order::getById($orderId);
if (!$order) {
    error_log("Payment verification failed: Order not found - order_id: $orderId");
    header('Location: cart.php?error=order_not_found');
    exit;
}

$keySecret = RAZORPAY_KEY_SECRET;
if (empty($keySecret)) {
    error_log("Payment verification failed: Razorpay key secret not configured");
    header('Location: cart.php?error=configuration_error');
    exit;
}

$generatedSignature = hash_hmac('sha256', $razorpayOrderId . '|' . $razorpayPaymentId, $keySecret);

if (!hash_equals($generatedSignature, $razorpaySignature)) {
    error_log("Payment verification failed: Signature mismatch for order_id: $orderId, razorpay_order_id: $razorpayOrderId");
    Order::updatePaymentStatus($orderId, 'failed', [
        'error' => 'Signature verification failed',
        'razorpay_order_id' => $razorpayOrderId,
        'razorpay_payment_id' => $razorpayPaymentId
    ]);
    header('Location: cart.php?error=payment_verification_failed');
    exit;
}

Order::updatePaymentStatus($orderId, 'paid', [
    'razorpay_payment_id' => $razorpayPaymentId,
    'razorpay_order_id' => $razorpayOrderId,
    'razorpay_signature' => $razorpaySignature,
    'verified_at' => date('Y-m-d H:i:s')
]);

Order::updateStatus($orderId, 'confirmed');

$_SESSION['cart'] = [];
unset($_SESSION['pending_order_id']);
unset($_SESSION['pending_order_number']);

header('Location: order-success.php?order=' . $order['order_number']);
exit;
