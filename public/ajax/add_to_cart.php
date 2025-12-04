<?php
require_once '../../includes/config.php';

header('Content-Type: application/json');

$response = ['success' => false];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
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
                'category_name' => $product['category_name'] ?? '',
                'quantity' => $quantity
            ];
        }
        
        $response['success'] = true;
        $response['cart_count'] = getCartCount();
        $response['message'] = 'Product added to cart!';
    } else {
        $response['message'] = 'Product not found';
    }
}

echo json_encode($response);
