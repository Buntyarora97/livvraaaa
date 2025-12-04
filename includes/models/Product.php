<?php
require_once __DIR__ . '/../database.php';

class Product {
    public static function getAll($activeOnly = false, $limit = null) {
        $sql = "SELECT p.*, c.name as category_name, c.slug as category_slug FROM products p LEFT JOIN categories c ON p.category_id = c.id";
        if ($activeOnly) {
            $sql .= " WHERE p.is_active = 1";
        }
        $sql .= " ORDER BY p.created_at DESC";
        if ($limit) {
            $sql .= " LIMIT " . (int)$limit;
        }
        return db()->query($sql)->fetchAll();
    }

    public static function getFeatured($limit = 8) {
        $stmt = db()->prepare("SELECT p.*, c.name as category_name, c.slug as category_slug FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.is_featured = 1 AND p.is_active = 1 ORDER BY p.created_at DESC LIMIT ?");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    public static function getByCategory($categorySlug, $activeOnly = true) {
        $sql = "SELECT p.*, c.name as category_name, c.slug as category_slug FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE c.slug = ?";
        if ($activeOnly) {
            $sql .= " AND p.is_active = 1";
        }
        $sql .= " ORDER BY p.created_at DESC";
        $stmt = db()->prepare($sql);
        $stmt->execute([$categorySlug]);
        return $stmt->fetchAll();
    }

    public static function getById($id) {
        $stmt = db()->prepare("SELECT p.*, c.name as category_name, c.slug as category_slug FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function getBySlug($slug) {
        $stmt = db()->prepare("SELECT p.*, c.name as category_name, c.slug as category_slug FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.slug = ?");
        $stmt->execute([$slug]);
        return $stmt->fetch();
    }

    public static function create($data) {
        $stmt = db()->prepare("INSERT INTO products (category_id, name, slug, sku, price, mrp, short_description, long_description, benefits, image, stock_qty, is_featured, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['category_id'],
            $data['name'],
            self::generateSlug($data['name']),
            $data['sku'] ?? '',
            $data['price'],
            $data['mrp'] ?? $data['price'],
            $data['short_description'] ?? '',
            $data['long_description'] ?? '',
            $data['benefits'] ?? '',
            $data['image'] ?? '',
            $data['stock_qty'] ?? 100,
            $data['is_featured'] ?? 0,
            $data['is_active'] ?? 1
        ]);
        return db()->lastInsertId();
    }

    public static function update($id, $data) {
        $sql = "UPDATE products SET category_id = ?, name = ?, slug = ?, sku = ?, price = ?, mrp = ?, short_description = ?, long_description = ?, benefits = ?, stock_qty = ?, is_featured = ?, is_active = ?, updated_at = CURRENT_TIMESTAMP";
        $params = [
            $data['category_id'],
            $data['name'],
            self::generateSlug($data['name']),
            $data['sku'] ?? '',
            $data['price'],
            $data['mrp'] ?? $data['price'],
            $data['short_description'] ?? '',
            $data['long_description'] ?? '',
            $data['benefits'] ?? '',
            $data['stock_qty'] ?? 100,
            $data['is_featured'] ?? 0,
            $data['is_active'] ?? 1
        ];
        
        if (!empty($data['image'])) {
            $sql .= ", image = ?";
            $params[] = $data['image'];
        }
        
        $sql .= " WHERE id = ?";
        $params[] = $id;
        
        $stmt = db()->prepare($sql);
        return $stmt->execute($params);
    }

    public static function delete($id) {
        $stmt = db()->prepare("DELETE FROM products WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public static function search($query) {
        $stmt = db()->prepare("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.is_active = 1 AND (p.name LIKE ? OR p.short_description LIKE ?) ORDER BY p.name");
        $searchTerm = '%' . $query . '%';
        $stmt->execute([$searchTerm, $searchTerm]);
        return $stmt->fetchAll();
    }

    public static function getTotalCount() {
        return db()->query("SELECT COUNT(*) FROM products")->fetchColumn();
    }

    private static function generateSlug($name) {
        $slug = strtolower(trim($name));
        $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        return trim($slug, '-');
    }
}
