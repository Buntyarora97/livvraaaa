<?php
require_once __DIR__ . '/../database.php';

class Category {
    public static function getAll($activeOnly = false) {
        $sql = "SELECT * FROM categories";
        if ($activeOnly) {
            $sql .= " WHERE is_active = 1";
        }
        $sql .= " ORDER BY sort_order ASC";
        return db()->query($sql)->fetchAll();
    }

    public static function getById($id) {
        $stmt = db()->prepare("SELECT * FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function getBySlug($slug) {
        $stmt = db()->prepare("SELECT * FROM categories WHERE slug = ?");
        $stmt->execute([$slug]);
        return $stmt->fetch();
    }

    public static function create($data) {
        $stmt = db()->prepare("INSERT INTO categories (name, slug, description, icon_class, is_active, sort_order) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['name'],
            self::generateSlug($data['name']),
            $data['description'] ?? '',
            $data['icon_class'] ?? 'fa-leaf',
            $data['is_active'] ?? 1,
            $data['sort_order'] ?? 0
        ]);
        return db()->lastInsertId();
    }

    public static function update($id, $data) {
        $stmt = db()->prepare("UPDATE categories SET name = ?, slug = ?, description = ?, icon_class = ?, is_active = ?, sort_order = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
        return $stmt->execute([
            $data['name'],
            self::generateSlug($data['name']),
            $data['description'] ?? '',
            $data['icon_class'] ?? 'fa-leaf',
            $data['is_active'] ?? 1,
            $data['sort_order'] ?? 0,
            $id
        ]);
    }

    public static function delete($id) {
        $stmt = db()->prepare("DELETE FROM categories WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public static function getProductCount($categoryId) {
        $stmt = db()->prepare("SELECT COUNT(*) FROM products WHERE category_id = ?");
        $stmt->execute([$categoryId]);
        return $stmt->fetchColumn();
    }

    private static function generateSlug($name) {
        $slug = strtolower(trim($name));
        $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        return trim($slug, '-');
    }
}
