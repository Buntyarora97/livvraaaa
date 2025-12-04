<?php
require_once '../../includes/config.php';
require_once '../../includes/models/Order.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$orderId = isset($_POST['order_id']) ? (int)$_POST['order_id'] : 0;

if (!$orderId) {
    echo json_encode(['success' => false, 'message' => 'Invalid order ID']);
    exit;
}

if (!isset($_SESSION['pending_order_id']) || $_SESSION['pending_order_id'] != $orderId) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized order access']);
    exit;
}

$order = Order::getById($orderId);
if (!$order) {
    echo json_encode(['success' => false, 'message' => 'Order not found']);
    exit;
}

$keyId = RAZORPAY_KEY_ID;
$keySecret = RAZORPAY_KEY_SECRET;

if (empty($keyId) || empty($keySecret)) {
    echo json_encode(['success' => false, 'message' => 'Payment gateway not configured']);
    exit;
}

$amountInPaise = (int)($order['total'] * 100);

$orderData = [
    'amount' => $amountInPaise,
    'currency' => 'INR',
    'receipt' => $order['order_number'],
    'notes' => [
        'order_id' => $orderId,
        'customer_name' => $order['customer_name']
    ]
];

$ch = curl_init('https://api.razorpay.com/v1/orders');
curl_setopt($ch, CURLOPT_USERPWD, $keyId . ':' . $keySecret);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($orderData));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$curlError = curl_error($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($curlError) {
    error_log("Razorpay CURL error: " . $curlError);
    echo json_encode(['success' => false, 'message' => 'Payment gateway connection failed']);
    exit;
}

if ($httpCode !== 200) {
    error_log("Razorpay order creation failed (HTTP $httpCode): " . $response);
    echo json_encode(['success' => false, 'message' => 'Failed to create payment order']);
    exit;
}

$razorpayOrder = json_decode($response, true);

if (!isset($razorpayOrder['id'])) {
    error_log("Razorpay order response missing ID: " . $response);
    echo json_encode(['success' => false, 'message' => 'Invalid payment gateway response']);
    exit;
}

echo json_encode([
    'success' => true,
    'razorpay_order_id' => $razorpayOrder['id'],
    'amount' => $amountInPaise,
    'currency' => 'INR'
]);
