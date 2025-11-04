<?php
// bootstrap.php

// 1. Nhúng Autoloader của Composer (Cần thiết cho Bramus/Router và các thư viện khác)
require_once __DIR__ . '/vendor/autoload.php';

// 2. Nhúng Cấu hình (config.php)
require_once __DIR__ . '/config.php';

// 3. Khởi tạo PDO Factory
// Dựa trên composer.json, bạn có thể đã dùng thư viện vlucas/phpdotenv,
// nhưng ta sẽ dùng DB_CONFIG trong config.php
require_once __DIR__ . '/app/models/PDOFactory.php';

// 4. Khởi tạo PDO Instance và lưu vào biến toàn cục (hoặc Registry/Container)
try {
    $factory = new PDOFactory();
    $GLOBALS['pdoInstance'] = $factory->create(DB_CONFIG); 
    // Dùng $GLOBALS['pdoInstance'] là cách làm nhanh nhất để truy cập từ Controller
} catch (Exception $e) {
    die("Lỗi kết nối CSDL: " . $e->getMessage());
}
?>