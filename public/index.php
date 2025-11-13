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

use App\Controllers\AuthController;

$router->get('/', function() {
    $baseURL = defined('BASE_URL') ? BASE_URL : '';

    // Session đã được khởi động ở đầu file index.php
    
    if (isset($_SESSION['user_id'])) {
        // Đã đăng nhập -> Chuyển hướng theo Role
        $role = $_SESSION['role'];
        $maLienKet = $_SESSION['ma_lien_ket'];

        if ($role === 'QuanLy') {
            header('Location: ' . $baseURL . '/dashboard');
            exit;
        } elseif ($role === 'SinhVien' && !empty($maLienKet)) {
            header('Location: ' . $baseURL . '/student/profile/' . $maLienKet);
            exit;
        }
        
    }
    
    // Chưa đăng nhập (hoặc không rõ role) -> Chuyển về trang login
    header('Location: ' . $baseURL . '/login');
    exit;
});
// Tuyến đường Đăng nhập
$router->get('/login', function() use ($pdoInstance) {
    (new AuthController($pdoInstance))->showLogin();
});
$router->post('/login', function() use ($pdoInstance) {
    (new AuthController($pdoInstance))->handleLogin();
});

// Tuyến đường Đăng ký
$router->get('/register', function() use ($pdoInstance) {
    (new AuthController($pdoInstance))->showRegister();
});
$router->post('/register', function() use ($pdoInstance) {
    (new AuthController($pdoInstance))->handleRegister();
});

// Tuyến đường Đăng xuất
$router->get('/logout', function() use ($pdoInstance) {
    (new AuthController($pdoInstance))->logout();
});

// Tuyến đường chuyển hướng sau đăng nhập:
// Role QuanLy
$router->get('/dashboard', function() use ($pdoInstance) {
    (new App\Controllers\DashboardController($pdoInstance))->index();
});

// Role SinhVien (chuyển hướng kèm Mã SV)
// SỬA ĐÚNG:
// Role SinhVien (chuyển hướng kèm Mã SV)
$router->get('/student/profile/(\w+)', function($maLienKet) use ($pdoInstance) {
    (new App\Controllers\StudentPanelController($pdoInstance))->index($maLienKet);
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
// --- HopDong Routes ---
$router->get('/hopdong', function () use ($pdoInstance) {
    $controller = new \App\Controllers\HopDongController($pdoInstance);
    $controller->index();
});

$router->post('/api/hopdong/create', function () use ($pdoInstance) {
    $controller = new \App\Controllers\HopDongController($pdoInstance);
    $controller->create();
});

$router->post('/api/hopdong/update', function () use ($pdoInstance) {
    $controller = new \App\Controllers\HopDongController($pdoInstance);
    $controller->update();
});

// Chú ý: Thay đổi {id} thành (\d+) để khớp với regex của router
$router->get('/api/hopdong/get/(\d+)', function ($id) use ($pdoInstance) {
    $controller = new \App\Controllers\HopDongController($pdoInstance);
    $controller->getHopDongDetails($id);
});

$router->post('/api/hopdong/delete/(\d+)', function ($id) use ($pdoInstance) {
    $controller = new \App\Controllers\HopDongController($pdoInstance);
    $controller->delete($id);
});
$router->get('/api/hopdong/get/{id}', '\App\Controllers\HopDongController@getHopDongDetails');
$router->post('/api/hopdong/delete/{id}', '\App\Controllers\HopDongController@delete');

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
    // Đường dẫn này phải trỏ đúng vào file notfound.php
    require_once __DIR__ . '/../app/views/errors/404.php';
});

// 2. XỬ LÝ ĐỔI MẬT KHẨU (POST - AJAX)
$router->post('/student/ajax_change_password', function () use ($pdoInstance) {
    $controller = new App\Controllers\StudentPanelController($pdoInstance);
    $controller->ajax_change_password();
});

// === KẾT THÚC STUDENT PANEL ===
// === QUẢN LÝ NGƯỜI DÙNG (ADMIN) - ĐÃ SỬA AJAX ===

// 1. TRANG DANH SÁCH (GET)
$router->get('/users', function () use ($pdoInstance) {
    $controller = new App\Controllers\UsersController($pdoInstance);
    $controller->index();
});

// 2. LẤY CHI TIẾT ĐỂ SỬA (GET - AJAX)
$router->get('/users/get/(\d+)', function ($id) use ($pdoInstance) {
    $controller = new App\Controllers\UsersController($pdoInstance);
    $controller->ajax_get_details($id);
});

// 3. XỬ LÝ THÊM MỚI (POST - AJAX)
$router->post('/users/ajax_create', function () use ($pdoInstance) {
    $controller = new App\Controllers\UsersController($pdoInstance);
    $controller->ajax_create();
});

// 4. XỬ LÝ CẬP NHẬT (POST - AJAX)
$router->post('/users/ajax_update/(\d+)', function ($id) use ($pdoInstance) {
    $controller = new App\Controllers\UsersController($pdoInstance);
    $controller->ajax_update($id);
});

// 5. XỬ LÝ XÓA (POST - AJAX)
$router->post('/users/ajax_delete/(\d+)', function ($id) use ($pdoInstance) {
    $controller = new App\Controllers\UsersController($pdoInstance);
    $controller->ajax_delete($id);
});

// 6. XỬ LÝ RESET MẬT KHẨU (POST - AJAX)
$router->post('/users/ajax_reset_password/(\d+)', function ($id) use ($pdoInstance) {
    $controller = new App\Controllers\UsersController($pdoInstance);
    $controller->ajax_reset_password($id);
});

// === KẾT THÚC QUẢN LÝ NGƯỜI DÙNG ===
// =================================================================
// BƯỚC 5: CHẠY ROUTER
// =================================================================
$router->run();
?>