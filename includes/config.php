<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/database.php';
require_once __DIR__ . '/models/Category.php';
require_once __DIR__ . '/models/Product.php';
require_once __DIR__ . '/models/Setting.php';

if (!defined('SITE_NAME')) {
    define('SITE_NAME', Setting::get('site_name', 'LIVVRA'));
    define('SITE_TAGLINE', Setting::get('site_tagline', 'Live Better Live Strong'));
    define('SITE_EMAIL', Setting::get('site_email', 'livvraindia@gmail.com'));
    define('SITE_PHONE', Setting::get('site_phone', '+91 9876543210'));
    define('SITE_ADDRESS', Setting::get('site_address', 'Dr Tridosha Herbotech Pvt Ltd, Sco no 27, Second Floor, Phase 3, Model Town, Bathinda 151001'));
    define('CURRENCY_SYMBOL', Setting::get('currency_symbol', '₹'));
    define('RAZORPAY_KEY_ID', Setting::get('razorpay_key_id', ''));
    define('RAZORPAY_KEY_SECRET', Setting::get('razorpay_key_secret', ''));
    define('SHIPPING_FEE', (float)Setting::get('shipping_fee', 0));
    define('FREE_SHIPPING_ABOVE', (float)Setting::get('free_shipping_above', 499));
    define('MAP_LAT', '30.2110');
    define('MAP_LNG', '74.9455');
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

function getCartCount() {
    $count = 0;
    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            $count += $item['quantity'];
        }
    }
    return $count;
}

function getCartTotal() {
    $total = 0;
    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            $total += $item['price'] * $item['quantity'];
        }
    }
    return $total;
}

function getShippingFee($subtotal) {
    if ($subtotal >= FREE_SHIPPING_ABOVE) {
        return 0;
    }
    return SHIPPING_FEE;
}

$categories = Category::getAll(true);

$products = [];
$dbProducts = Product::getAll(true);
foreach ($dbProducts as $p) {
    $products[$p['id']] = $p;
}
