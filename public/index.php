<?php
// public/index.php

// 1. Cấu hình ban đầu
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// =================================================================
// BƯỚC 1: NHÚNG AUTOLOADER VÀ CÁC THIẾT LẬP CƠ SỞ (BOOTSTRAP)
// =================================================================

// Nhúng Autoloader của Composer (Tải tất cả classes có namespace App\)
require_once __DIR__ . '/../vendor/autoload.php';

// Nhúng file cấu hình (nơi định nghĩa DB_CONFIG, BASE_URL)
require_once __DIR__ . '/../config.php';

// Khắc phục lỗi: Kiểm tra và khởi động session chỉ khi chưa active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('APPNAME', 'HỆ THỐNG QUẢN LÝ KÝ TÚC XÁ');

// =================================================================
// BƯỚC 2: KHỞI TẠO CÁC PHỤ THUỘC (DEPENDENCIES)
// =================================================================

// Khởi tạo PDO Instance (Sử dụng Autoloading và Namespace App\Models)
try {
    // SỬ DỤNG LỚP PDOFactory VỚI NAMESPACE
    $factory = new App\Models\PDOFactory(); 
    $pdoInstance = $factory->create(DB_CONFIG);
} catch (Exception $e) {
    die("Không thể kết nối CSDL: " . $e->getMessage());
}

// Khởi tạo Router
$router = new Bramus\Router\Router();

// =================================================================
// BƯỚC 3: ĐỊNH NGHĨA CÁC ROUTES
// =================================================================

// Route mặc định (Trang chủ)
$router->get('/', function() {
    echo "<h1>Trang Chủ QLKTX</h1><p>Vui lòng đăng nhập hoặc xem các module CRUD.</p>";
});

// ********** TẤT CẢ CÁC ROUTES ĐỀU SỬ DỤNG NAMESPACE **********

// Route cho TẠO MỚI PHÒNG
$router->match('GET|POST', '/phong/create', function() use ($pdoInstance) {
    
    // TẠO CONTROLLER SỬ DỤNG NAMESPACE ĐẦY ĐỦ (Autoloading)
    $controller = new App\Controllers\PhongController($pdoInstance); 
    $controller->create();
});

// Route cho TẠO MỚI LOẠI PHÒNG
$router->match('GET|POST', '/loaiphong/create', function() use ($pdoInstance) {
    $controller = new App\Controllers\LoaiPhongController($pdoInstance); 
    $controller->create();
});

// Route cho TẠO MỚI DỊCH VỤ
$router->match('GET|POST', '/dichvu/create', function() use ($pdoInstance) {
    $controller = new App\Controllers\DichVuController($pdoInstance); 
    $controller->create();
});

// Route cho THÊM SINH VIÊN VÀO PHÒNG (Tạo Hợp đồng)
$router->match('GET|POST', '/hopdong/create', function() use ($pdoInstance) {
    $controller = new App\Controllers\HopDongController($pdoInstance); 
    $controller->create();
});

// 4. Xử lý 404
$router->set404(function() {
    header('HTTP/1.1 404 Not Found');
    echo "<h1>404 - Không tìm thấy trang!</h1><p>Vui lòng kiểm tra lại URL.</p>";
});

// =================================================================
// BƯỚC 5: CHẠY ROUTER
// =================================================================
$router->run();
?>