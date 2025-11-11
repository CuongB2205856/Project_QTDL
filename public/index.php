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
require_once __DIR__ . '/../app/functions.php';
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
$router->get('/', function () {
    echo "<h1>Trang Chủ QLKTX</h1><p>Vui lòng đăng nhập hoặc xem các module CRUD.</p>";
});
$router->get('/dashboard', function () use ($pdoInstance) {
    $controller = new App\Controllers\DashboardController($pdoInstance);
    $controller->index();
});
// ********** TẤT CẢ CÁC ROUTES ĐỀU SỬ DỤNG NAMESPACE **********

// === QUẢN LÝ PHÒNG (CRUD - AJAX) ===

// 1. TRANG DANH SÁCH CHÍNH (GET)
$router->get('/phong', function () use ($pdoInstance) {
    $controller = new App\Controllers\PhongController($pdoInstance);
    $controller->index();
});

// 2. LẤY CHI TIẾT ĐỂ SỬA (GET - AJAX)
$router->get('/phong/get/(\d+)', function ($id) use ($pdoInstance) {
    $controller = new App\Controllers\PhongController($pdoInstance);
    $controller->ajax_get_details($id);
});

// 3. XỬ LÝ THÊM MỚI (POST - AJAX)
$router->post('/phong/ajax_create', function () use ($pdoInstance) {
    $controller = new App\Controllers\PhongController($pdoInstance);
    $controller->ajax_create();
});

// 4. XỬ LÝ CẬP NHẬT (POST - AJAX)
$router->post('/phong/ajax_update/(\d+)', function ($id) use ($pdoInstance) {
    $controller = new App\Controllers\PhongController($pdoInstance);
    $controller->ajax_update($id);
});

// 5. XỬ LÝ XÓA (POST - AJAX)
$router->post('/phong/ajax_delete/(\d+)', function ($id) use ($pdoInstance) {
    $controller = new App\Controllers\PhongController($pdoInstance);
    $controller->ajax_delete($id);
});
// === KẾT THÚC QUẢN LÝ PHÒNG ===

// === QUẢN LÝ LOẠI PHÒNG (CRUD) ===
// 1. TRANG DANH SÁCH CHÍNH (GET)
$router->get('/loaiphong', function () use ($pdoInstance) {
    $controller = new App\Controllers\LoaiPhongController($pdoInstance);
    $controller->index();
});

// 2. LẤY CHI TIẾT ĐỂ SỬA (GET - AJAX)
$router->get('/loaiphong/get/(\d+)', function ($id) use ($pdoInstance) {
    $controller = new App\Controllers\LoaiPhongController($pdoInstance);
    $controller->ajax_get_details($id);
});

// 3. XỬ LÝ THÊM MỚI (POST - AJAX)
$router->post('/loaiphong/ajax_create', function () use ($pdoInstance) {
    $controller = new App\Controllers\LoaiPhongController($pdoInstance);
    $controller->ajax_create();
});

// 4. XỬ LÝ CẬP NHẬT (POST - AJAX)
$router->post('/loaiphong/ajax_update/(\d+)', function ($id) use ($pdoInstance) {
    $controller = new App\Controllers\LoaiPhongController($pdoInstance);
    $controller->ajax_update($id);
});

// 5. XỬ LÝ XÓA (GET/POST - AJAX) - Dùng POST sẽ an toàn hơn
$router->post('/loaiphong/ajax_delete/(\d+)', function ($id) use ($pdoInstance) {
    $controller = new App\Controllers\LoaiPhongController($pdoInstance);
    $controller->ajax_delete($id);
});

// === KẾT THÚC QUẢN LÝ LOẠI PHÒNG ===



// === QUẢN LÝ DỊCH VỤ (CRUD - AJAX) ===

// 1. TRANG DANH SÁCH CHÍNH (GET)
$router->get('/dichvu', function () use ($pdoInstance) {
    $controller = new App\Controllers\DichVuController($pdoInstance);
    $controller->index();
});

// 2. LẤY CHI TIẾT ĐỂ SỬA (GET - AJAX)
$router->get('/dichvu/get/(\d+)', function ($id) use ($pdoInstance) {
    $controller = new App\Controllers\DichVuController($pdoInstance);
    $controller->ajax_get_details($id);
});

// 3. XỬ LÝ THÊM MỚI (POST - AJAX)
$router->post('/dichvu/ajax_create', function () use ($pdoInstance) {
    $controller = new App\Controllers\DichVuController($pdoInstance);
    $controller->ajax_create();
});

// 4. XỬ LÝ CẬP NHẬT (POST - AJAX)
$router->post('/dichvu/ajax_update/(\d+)', function ($id) use ($pdoInstance) {
    $controller = new App\Controllers\DichVuController($pdoInstance);
    $controller->ajax_update($id);
});

// 5. XỬ LÝ XÓA (POST - AJAX)
$router->post('/dichvu/ajax_delete/(\d+)', function ($id) use ($pdoInstance) {
    $controller = new App\Controllers\DichVuController($pdoInstance);
    $controller->ajax_delete($id);
});
// === KẾT THÚC QUẢN LÝ DỊCH VỤ ===

// Route cho THÊM SINH VIÊN VÀO PHÒNG (Tạo Hợp đồng)
$router->match('GET|POST', '/hopdong/create', function () use ($pdoInstance) {
    $controller = new App\Controllers\HopDongController($pdoInstance);
    $controller->create();
});
// === QUẢN LÝ SINH VIÊN (CRUD - AJAX) ===

// 1. TRANG DANH SÁCH CHÍNH (GET)
$router->get('/sinhvien', function () use ($pdoInstance) {
    $controller = new App\Controllers\SinhVienController($pdoInstance);
    $controller->index();
});

// 2. LẤY CHI TIẾT ĐỂ SỬA (GET - AJAX)
// Mã SV có thể chứa chữ (vd: 'SV001'), dùng regex [\w\-]+
$router->get('/sinhvien/get/([\w\-]+)', function ($maSV) use ($pdoInstance) {
    $controller = new App\Controllers\SinhVienController($pdoInstance);
    $controller->ajax_get_details($maSV);
});

// 3. LẤY CHI TIẾT PHÒNG Ở (GET - AJAX)
$router->get('/sinhvien/ajax_get_room_details/([\w\-]+)', function ($maSV) use ($pdoInstance) {
    $controller = new App\Controllers\SinhVienController($pdoInstance);
    $controller->ajax_get_room_details($maSV);
});

// 4. XỬ LÝ CẬP NHẬT (POST - AJAX)
$router->post('/sinhvien/ajax_update/([\w\-]+)', function ($maSV) use ($pdoInstance) {
    $controller = new App\Controllers\SinhVienController($pdoInstance);
    $controller->ajax_update($maSV);
});

// 5. XỬ LÝ XÓA (POST - AJAX)
$router->post('/sinhvien/ajax_delete/([\w\-]+)', function ($maSV) use ($pdoInstance) {
    $controller = new App\Controllers\SinhVienController($pdoInstance);
    $controller->ajax_delete($maSV);
});

// 6. XỬ LÝ RESET MẬT KHẨU (POST - AJAX)
$router->post('/sinhvien/ajax_reset_password/([\w\-]+)', function ($maSV) use ($pdoInstance) {
    $controller = new App\Controllers\SinhVienController($pdoInstance);
    $controller->ajax_reset_password($maSV);
});
// === KẾT THÚC QUẢN LÝ SINH VIÊN ===
// 4. Xử lý 404
$router->set404(function () {
    header('HTTP/1.1 404 Not Found');
    echo "<h1>404 - Không tìm thấy trang!</h1><p>Vui lòng kiểm tra lại URL.</p>";
});
// === BẢNG ĐIỀU KHIỂN SINH VIÊN (STUDENT PANEL) ===

// 1. TRANG PROFILE CHÍNH (GET)
$router->get('/student/profile', function () use ($pdoInstance) {
    $controller = new App\Controllers\StudentPanelController($pdoInstance);
    $controller->index();
});

// 2. XỬ LÝ ĐỔI MẬT KHẨU (POST - AJAX)
$router->post('/student/ajax_change_password', function () use ($pdoInstance) {
    $controller = new App\Controllers\StudentPanelController($pdoInstance);
    $controller->ajax_change_password();
});

// === KẾT THÚC STUDENT PANEL ===
// === QUẢN LÝ NGƯỜI DÙNG (ADMIN) ===

// 1. TRANG DANH SÁCH (GET) VÀ TẠO MỚI (POST)
$router->match('GET|POST', '/users', function () use ($pdoInstance) {
    $controller = new App\Controllers\UsersController($pdoInstance);
    $controller->index();
});

// 2. XỬ LÝ XÓA (POST)
$router->post('/users/delete/(\d+)', function ($id) use ($pdoInstance) {
    $controller = new App\Controllers\UsersController($pdoInstance);
    $controller->delete($id);
});

// === KẾT THÚC QUẢN LÝ NGƯỜI DÙNG ===
// =================================================================
// BƯỚC 5: CHẠY ROUTER
// =================================================================
$router->run();
?>