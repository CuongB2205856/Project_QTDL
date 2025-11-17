<?php
// public/index.php

// 1. Cấu hình ban đầu
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// Nhúng các file cần thiết
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/functions.php';
// Nhúng file cấu hình (nơi định nghĩa DB_CONFIG, BASE_URL)
require_once __DIR__ . '/../config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('APPNAME', 'HỆ THỐNG QUẢN LÝ KÝ TÚC XÁ');

// Khởi tạo PDO Instance 
try {
    $factory = new App\Models\PDOFactory();
    $pdoInstance = $factory->create(DB_CONFIG);
} catch (Exception $e) {
    die("Không thể kết nối CSDL: " . $e->getMessage());
}

// Khởi tạo Router
$router = new Bramus\Router\Router();


use App\Controllers\AuthController;

// Định nghĩa các routes

// Tuyến đường Trang chủ
$router->get('/', function () {
    $baseURL = defined('BASE_URL') ? BASE_URL : '';

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

// 1. Hiển thị form đăng nhập
$router->get('/login', function () use ($pdoInstance) {
    (new AuthController($pdoInstance))->showLogin();
});

// 2. Xử lý đăng nhập
$router->post('/login', function () use ($pdoInstance) {
    (new AuthController($pdoInstance))->handleLogin();
});

// Tuyến đường Đăng ký

// 1. Hiển thị form đăng ký
$router->get('/register', function () use ($pdoInstance) {
    (new AuthController($pdoInstance))->showRegister();
});

// 2. Xử lý đăng ký
$router->post('/register', function () use ($pdoInstance) {
    (new AuthController($pdoInstance))->handleRegister();
});

// Tuyến đường Đăng xuất

// Xử lý đăng xuất
$router->get('/logout', function () use ($pdoInstance) {
    (new AuthController($pdoInstance))->logout();
});

// Tuyến đường đổi mật khẩu admin
$router->post('/users/ajax_admin_change_password', function () use ($pdoInstance) {
    $controller = new App\Controllers\UsersController($pdoInstance);
    $controller->ajax_admin_change_password();
});

// Tuyến đường chuyển hướng sau đăng nhập:

// Role QuanLy
$router->get('/dashboard', function () use ($pdoInstance) {
    (new App\Controllers\DashboardController($pdoInstance))->index();
});
$router->post('/dashboard/export_report', function () use ($pdoInstance) {
    (new App\Controllers\DashboardController($pdoInstance))->export_report();
});

// Role SinhVien (chuyển hướng kèm Mã SV)
$router->get('/student/profile/(\w+)', function ($maLienKet) use ($pdoInstance) {
    (new App\Controllers\StudentPanelController($pdoInstance))->index($maLienKet);
});

// Tuyến đường quản lý phòng

// 1. Trang danh sách chính (GET)
$router->get('/phong', function () use ($pdoInstance) {
    $controller = new App\Controllers\PhongController($pdoInstance);
    $controller->index();
});

// 2. Lấy chi tiết để sửa (GET - AJAX)
$router->get('/phong/get/(\d+)', function ($id) use ($pdoInstance) {
    $controller = new App\Controllers\PhongController($pdoInstance);
    $controller->ajax_get_details($id);
});

// 3. Xử lý thêm mới (POST - AJAX)
$router->post('/phong/ajax_create', function () use ($pdoInstance) {
    $controller = new App\Controllers\PhongController($pdoInstance);
    $controller->ajax_create();
});

// 4. Xử lý cập nhật (POST - AJAX)
$router->post('/phong/ajax_update/(\d+)', function ($id) use ($pdoInstance) {
    $controller = new App\Controllers\PhongController($pdoInstance);
    $controller->ajax_update($id);
});

// 5. Xử lý xóa (POST - AJAX)
$router->post('/phong/ajax_delete/(\d+)', function ($id) use ($pdoInstance) {
    $controller = new App\Controllers\PhongController($pdoInstance);
    $controller->ajax_delete($id);
});

$router->get('/phong/ajax_get_sv/(\w+)', function ($soPhong) use ($pdoInstance) {
    (new App\Controllers\PhongController($pdoInstance))->ajax_GetSinhVienInPhong($soPhong);
});

// Tuyến đường quản lý loại phòng

// 1. Trang danh sách chính (GET)
$router->get('/loaiphong', function () use ($pdoInstance) {
    $controller = new App\Controllers\LoaiPhongController($pdoInstance);
    $controller->index();
});

// 2. Lây chi tiết để sửa (GET - AJAX)
$router->get('/loaiphong/get/(\d+)', function ($id) use ($pdoInstance) {
    $controller = new App\Controllers\LoaiPhongController($pdoInstance);
    $controller->ajax_get_details($id);
});

// 3. Xử lý thêm mới (POST - AJAX)
$router->post('/loaiphong/ajax_create', function () use ($pdoInstance) {
    $controller = new App\Controllers\LoaiPhongController($pdoInstance);
    $controller->ajax_create();
});

// 4. Xử lý cập nhật (POST - AJAX)
$router->post('/loaiphong/ajax_update/(\d+)', function ($id) use ($pdoInstance) {
    $controller = new App\Controllers\LoaiPhongController($pdoInstance);
    $controller->ajax_update($id);
});

// 5. Xử lý xóa (POST - AJAX)
$router->post('/loaiphong/ajax_delete/(\d+)', function ($id) use ($pdoInstance) {
    $controller = new App\Controllers\LoaiPhongController($pdoInstance);
    $controller->ajax_delete($id);
});

// Tuyến đường quản lý dịch vụ

// 1. Trang danh sách chính (GET)
$router->get('/dichvu', function () use ($pdoInstance) {
    $controller = new App\Controllers\DichVuController($pdoInstance);
    $controller->index();
});

// 2. Lấy chi tiết để sửa (GET - AJAX)
$router->get('/dichvu/get/(\d+)', function ($id) use ($pdoInstance) {
    $controller = new App\Controllers\DichVuController($pdoInstance);
    $controller->ajax_get_details($id);
});

// 3. Xử lý thêm mới (POST - AJAX)
$router->post('/dichvu/ajax_create', function () use ($pdoInstance) {
    $controller = new App\Controllers\DichVuController($pdoInstance);
    $controller->ajax_create();
});

// 4. Xử lý cập nhật (POST - AJAX)
$router->post('/dichvu/ajax_update/(\d+)', function ($id) use ($pdoInstance) {
    $controller = new App\Controllers\DichVuController($pdoInstance);
    $controller->ajax_update($id);
});

// 5. Xử lý xóa (POST - AJAX)
$router->post('/dichvu/ajax_delete/(\d+)', function ($id) use ($pdoInstance) {
    $controller = new App\Controllers\DichVuController($pdoInstance);
    $controller->ajax_delete($id);
});

// Tuyến đường quản lý Sử Dụng Dịch Vụ (SDDV)
// 1. Trang danh sách chính (GET)
$router->get('/sddv', function () use ($pdoInstance) {
    $controller = new App\Controllers\SuDungDichVuController($pdoInstance);
    $controller->index();
});

// 2. Lấy chi tiết để sửa (GET - AJAX)
$router->get('/sddv/get/(\d+)', function ($id) use ($pdoInstance) {
    $controller = new App\Controllers\SuDungDichVuController($pdoInstance);
    $controller->ajax_get_details($id);
});

// 3. Xử lý thêm mới (POST - AJAX)
// Đây là route để "chọn loại dịch vụ" và "nhập số lượng"
$router->post('/sddv/ajax_create', function () use ($pdoInstance) {
    $controller = new App\Controllers\SuDungDichVuController($pdoInstance);
    $controller->ajax_create();
});

// 4. Xử lý cập nhật (POST - AJAX)
$router->post('/sddv/ajax_update/(\d+)', function ($id) use ($pdoInstance) {
    $controller = new App\Controllers\SuDungDichVuController($pdoInstance);
    $controller->ajax_update($id);
});

// 5. Xử lý xóa (POST - AJAX)
$router->post('/sddv/ajax_delete/(\d+)', function ($id) use ($pdoInstance) {
    $controller = new App\Controllers\SuDungDichVuController($pdoInstance);
    $controller->ajax_delete($id);
});

// Tuyến đường quản lý hợp đồng

// 1. Trang danh sách chính (GET)
$router->get('/hopdong', function () use ($pdoInstance) {
    $controller = new \App\Controllers\HopDongController($pdoInstance);
    $controller->index();
});

// 2. Xử lý tạo hợp đồng (POST)
$router->post('/api/hopdong/create', function () use ($pdoInstance) {
    $controller = new \App\Controllers\HopDongController($pdoInstance);
    $controller->create();
});

// 3. Xử lý cập nhật hợp đồng (POST)
$router->post('/api/hopdong/update', function () use ($pdoInstance) {
    $controller = new \App\Controllers\HopDongController($pdoInstance);
    $controller->update();
});

// 4. Lấy chi tiết hợp đồng (GET)
$router->get('/api/hopdong/get/(\d+)', function ($id) use ($pdoInstance) {
    $controller = new \App\Controllers\HopDongController($pdoInstance);
    $controller->getHopDongDetails($id);
});

// 5. Xử lý xóa hợp đồng (POST)
$router->post('/api/hopdong/delete/(\d+)', function ($id) use ($pdoInstance) {
    $controller = new \App\Controllers\HopDongController($pdoInstance);
    $controller->delete($id);
});

// 6. Kiểm tra trạng thái hợp đồng của 1 sinh viên
$router->get('/hopdong/check/(\w+)', function ($maSV) use ($pdoInstance) {
    (new App\Controllers\HopDongController($pdoInstance))->checkTrangThaiHopDong($maSV);
});


// Tuyến đường quản lý hóa đơn
$router->get('/hoadon', function() use ($pdoInstance) {
    (new App\Controllers\HoaDonController($pdoInstance))->index();
});

$router->post('/hoadon/ajax_create', function() use ($pdoInstance) {
    (new App\Controllers\HoaDonController($pdoInstance))->ajax_create();
});

$router->post('/hoadon/ajax_delete/(\d+)', function($id) use ($pdoInstance) {
    (new App\Controllers\HoaDonController($pdoInstance))->ajax_delete($id);
});

$router->post('/hoadon/ajax_update_status/(\d+)', function($id) use ($pdoInstance) {
    (new App\Controllers\HoaDonController($pdoInstance))->ajax_update_status($id);
});

$router->get('/hoadon/get/(\d+)', function($id) use ($pdoInstance) {
    (new App\Controllers\HoaDonController($pdoInstance))->ajax_get_details($id);
});

// Tuyến đường quản lý sinh viên

// 1. Trang danh sách chính (GET)
$router->get('/sinhvien', function () use ($pdoInstance) {
    $controller = new App\Controllers\SinhVienController($pdoInstance);
    $controller->index();
});

// 2. Lấy chi tiết để sửa (GET - AJAX)
// Mã SV có thể chứa chữ (vd: 'SV001'), dùng regex [\w\-]+
$router->get('/sinhvien/get/([\w\-]+)', function ($maSV) use ($pdoInstance) {
    $controller = new App\Controllers\SinhVienController($pdoInstance);
    $controller->ajax_get_details($maSV);
});

// 3. Lấy chi tiết phòng của sinh viên (GET - AJAX)
$router->get('/sinhvien/ajax_get_room_details/([\w\-]+)', function ($maSV) use ($pdoInstance) {
    $controller = new App\Controllers\SinhVienController($pdoInstance);
    $controller->ajax_get_room_details($maSV);
});

// 4. Xử lý thêm cập nhật (POST - AJAX)
$router->post('/sinhvien/ajax_update/([\w\-]+)', function ($maSV) use ($pdoInstance) {
    $controller = new App\Controllers\SinhVienController($pdoInstance);
    $controller->ajax_update($maSV);
});

// 5. Xử lý xóa (POST - AJAX)
$router->post('/sinhvien/ajax_delete/([\w\-]+)', function ($maSV) use ($pdoInstance) {
    $controller = new App\Controllers\SinhVienController($pdoInstance);
    $controller->ajax_delete($maSV);
});

// 6. Xử lý reset mật khẩu (POST - AJAX)
$router->post('/sinhvien/ajax_reset_password/([\w\-]+)', function ($maSV) use ($pdoInstance) {
    $controller = new App\Controllers\SinhVienController($pdoInstance);
    $controller->ajax_reset_password($maSV);
});

// 7. Xử lý thay đổi mật khẩu sinh viên (POST - AJAX)
$router->post('/student/ajax_change_password', function () use ($pdoInstance) {
    $controller = new App\Controllers\StudentPanelController($pdoInstance);
    $controller->ajax_change_password();
});

// Tuyến đường quản lý Users

// 1. Trang danh sách chính (GET)
$router->get('/users', function () use ($pdoInstance) {
    $controller = new App\Controllers\UsersController($pdoInstance);
    $controller->index();
});

// 2. Lấy chi tiết để sửa (GET - AJAX)
$router->get('/users/get/(\d+)', function ($id) use ($pdoInstance) {
    $controller = new App\Controllers\UsersController($pdoInstance);
    $controller->ajax_get_details($id);
});

// 3. Xử lý thêm mới (POST - AJAX)
$router->post('/users/ajax_create', function () use ($pdoInstance) {
    $controller = new App\Controllers\UsersController($pdoInstance);
    $controller->ajax_create();
});

// 4. Xử lý cập nhật (POST - AJAX)
$router->post('/users/ajax_update/(\d+)', function ($id) use ($pdoInstance) {
    $controller = new App\Controllers\UsersController($pdoInstance);
    $controller->ajax_update($id);
});

// 5. Xử lý xóa (POST - AJAX)
$router->post('/users/ajax_delete/(\d+)', function ($id) use ($pdoInstance) {
    $controller = new App\Controllers\UsersController($pdoInstance);
    $controller->ajax_delete($id);
});

// 6. Xử lý reset mật khẩu (POST - AJAX)
$router->post('/users/ajax_reset_password/(\d+)', function ($id) use ($pdoInstance) {
    $controller = new App\Controllers\UsersController($pdoInstance);
    $controller->ajax_reset_password($id);
});

// Tuyến đường 404
$router->set404(function () {
    header('HTTP/1.1 404 Not Found');
    require_once __DIR__ . '/../app/views/errors/404.php';
});

// CHẠY ROUTER
$router->run();
?>