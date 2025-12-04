<?php
require_once __DIR__ . '/../database.php';

class Setting {
    private static $cache = [];

    public static function get($key, $default = null) {
        if (isset(self::$cache[$key])) {
            return self::$cache[$key];
        }
        
        $stmt = db()->prepare("SELECT setting_value FROM settings WHERE setting_key = ?");
        $stmt->execute([$key]);
        $result = $stmt->fetch();
        
        $value = $result ? $result['setting_value'] : $default;
        self::$cache[$key] = $value;
        
        return $value;
    }

    public static function set($key, $value) {
        $stmt = db()->prepare("INSERT OR REPLACE INTO settings (setting_key, setting_value, updated_at) VALUES (?, ?, CURRENT_TIMESTAMP)");
        $result = $stmt->execute([$key, $value]);
        self::$cache[$key] = $value;
        return $result;
    }

    public static function getAll() {
        $result = db()->query("SELECT * FROM settings ORDER BY setting_key")->fetchAll();
        $settings = [];
        foreach ($result as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
        return $settings;
    }

    public static function updateMultiple($data) {
        foreach ($data as $key => $value) {
            self::set($key, $value);
        }
        return true;
    }
}
