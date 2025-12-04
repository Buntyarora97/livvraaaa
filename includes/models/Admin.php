<?php
require_once __DIR__ . '/../database.php';

class Admin {
    public static function getByUsername($username) {
        $stmt = db()->prepare("SELECT * FROM admins WHERE username = ? AND is_active = 1");
        $stmt->execute([$username]);
        return $stmt->fetch();
    }

    public static function getById($id) {
        $stmt = db()->prepare("SELECT * FROM admins WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function verifyPassword($username, $password) {
        $admin = self::getByUsername($username);
        if ($admin && password_verify($password, $admin['password_hash'])) {
            self::updateLastLogin($admin['id']);
            return $admin;
        }
        return false;
    }

    public static function updateLastLogin($id) {
        $stmt = db()->prepare("UPDATE admins SET last_login_at = CURRENT_TIMESTAMP WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public static function create($data) {
        $stmt = db()->prepare("INSERT INTO admins (username, password_hash, email, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([
            $data['username'],
            password_hash($data['password'], PASSWORD_DEFAULT),
            $data['email'] ?? '',
            $data['role'] ?? 'admin'
        ]);
        return db()->lastInsertId();
    }

    public static function updatePassword($id, $newPassword) {
        $stmt = db()->prepare("UPDATE admins SET password_hash = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
        return $stmt->execute([password_hash($newPassword, PASSWORD_DEFAULT), $id]);
    }
}
