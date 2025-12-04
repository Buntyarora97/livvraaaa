<?php
require_once __DIR__ . '/../database.php';

class Order {
    public static function getAll($status = null, $limit = null) {
        $sql = "SELECT * FROM orders";
        $params = [];
        
        if ($status) {
            $sql .= " WHERE order_status = ?";
            $params[] = $status;
        }
        
        $sql .= " ORDER BY placed_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT " . (int)$limit;
        }
        
        $stmt = db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function getById($id) {
        $stmt = db()->prepare("SELECT * FROM orders WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function getByOrderNumber($orderNumber) {
        $stmt = db()->prepare("SELECT * FROM orders WHERE order_number = ?");
        $stmt->execute([$orderNumber]);
        return $stmt->fetch();
    }

    public static function getItems($orderId) {
        $stmt = db()->prepare("SELECT * FROM order_items WHERE order_id = ?");
        $stmt->execute([$orderId]);
        return $stmt->fetchAll();
    }

    public static function create($data, $items) {
        $pdo = db();
        $pdo->beginTransaction();
        
        try {
            $orderNumber = self::generateOrderNumber();
            
            $stmt = $pdo->prepare("INSERT INTO orders (order_number, customer_id, customer_name, customer_email, customer_phone, shipping_address, city, state, pincode, payment_method, payment_status, order_status, subtotal, shipping_fee, total, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            
            $stmt->execute([
                $orderNumber,
                $data['customer_id'] ?? null,
                $data['customer_name'],
                $data['customer_email'] ?? '',
                $data['customer_phone'],
                $data['shipping_address'],
                $data['city'] ?? '',
                $data['state'] ?? '',
                $data['pincode'] ?? '',
                $data['payment_method'],
                $data['payment_method'] === 'cod' ? 'pending' : 'pending',
                'pending',
                $data['subtotal'],
                $data['shipping_fee'] ?? 0,
                $data['total'],
                $data['notes'] ?? ''
            ]);
            
            $orderId = $pdo->lastInsertId();
            
            $itemStmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, product_name, product_image, unit_price, quantity, line_total) VALUES (?, ?, ?, ?, ?, ?, ?)");
            
            foreach ($items as $item) {
                $itemStmt->execute([
                    $orderId,
                    $item['product_id'],
                    $item['product_name'],
                    $item['product_image'] ?? '',
                    $item['unit_price'],
                    $item['quantity'],
                    $item['unit_price'] * $item['quantity']
                ]);
            }
            
            $pdo->commit();
            return ['id' => $orderId, 'order_number' => $orderNumber];
            
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public static function updateStatus($id, $status) {
        $sql = "UPDATE orders SET order_status = ?";
        $params = [$status];
        
        if ($status === 'delivered') {
            $sql .= ", delivered_at = CURRENT_TIMESTAMP";
        }
        
        $sql .= " WHERE id = ?";
        $params[] = $id;
        
        $stmt = db()->prepare($sql);
        return $stmt->execute($params);
    }

    public static function updatePaymentStatus($id, $status, $razorpayData = []) {
        $sql = "UPDATE orders SET payment_status = ?";
        $params = [$status];
        
        if (!empty($razorpayData['payment_id'])) {
            $sql .= ", razorpay_payment_id = ?";
            $params[] = $razorpayData['payment_id'];
        }
        if (!empty($razorpayData['signature'])) {
            $sql .= ", razorpay_signature = ?";
            $params[] = $razorpayData['signature'];
        }
        
        $sql .= " WHERE id = ?";
        $params[] = $id;
        
        $stmt = db()->prepare($sql);
        return $stmt->execute($params);
    }

    public static function getTotalCount($status = null) {
        $sql = "SELECT COUNT(*) FROM orders";
        $params = [];
        
        if ($status) {
            $sql .= " WHERE order_status = ?";
            $params[] = $status;
        }
        
        $stmt = db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }

    public static function getTotalRevenue() {
        return db()->query("SELECT COALESCE(SUM(total), 0) FROM orders WHERE payment_status = 'paid'")->fetchColumn();
    }

    public static function getRecentOrders($limit = 10) {
        $stmt = db()->prepare("SELECT * FROM orders ORDER BY placed_at DESC LIMIT ?");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    private static function generateOrderNumber() {
        return 'LIV' . date('Ymd') . strtoupper(substr(uniqid(), -6));
    }
}
