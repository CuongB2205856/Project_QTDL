<?php

// 1. Nhúng Autoloader của Composer
require_once __DIR__ . '/vendor/autoload.php';

// 2. Nhúng Cấu hình (config.php)
require_once __DIR__ . '/config.php';

// 3. Khởi tạo PDO Factory
use APP\Models\PDOFactory;

// 4. Khởi tạo PDO Instance
try {
    $factory = new PDOFactory();
    $GLOBALS['pdoInstance'] = $factory->create(DB_CONFIG);
} catch (Exception $e) {
    die("Lỗi kết nối CSDL: " . $e->getMessage());
}
?>