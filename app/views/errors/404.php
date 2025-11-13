<?php 
// Lấy BASE_URL
$baseURL = defined('BASE_URL') ? BASE_URL : ''; 

// ==========================================================
// LOGIC ĐỂ XÁC ĐỊNH ĐÚNG TRANG CHỦ
// =ia
$homeURL = $baseURL . '/login'; // Mặc định là trang login nếu chưa đăng nhập

// Khởi động session nếu nó chưa chạy
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra xem đã đăng nhập chưa
if (isset($_SESSION['user_id'])) {
    $role = $_SESSION['role'] ?? null;
    $maLienKet = $_SESSION['ma_lien_ket'] ?? null;

    if ($role === 'QuanLy') {
        // Nếu là Quản lý, trang chủ là /dashboard
        $homeURL = $baseURL . '/dashboard';
    } elseif ($role === 'SinhVien' && !empty($maLienKet)) {
        // Nếu là Sinh viên, trang chủ là profile
        $homeURL = $baseURL . '/student/profile/' . $maLienKet;
    }
    // Nếu có role khác mà không xác định, nó sẽ giữ nguyên là /login
}
// ==========================================================
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Không tìm thấy</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .error-container { max-width: 500px; margin: 100px auto; padding: 30px; background: #fff; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); text-align: center; }
        .error-code { font-size: 72px; font-weight: bold; color: #dc3545; }
    </style>
</head>
<body>
    <div class="container">
        <div class="error-container">
            <div class="error-code">404</div>
            <h2 class="mt-3">Không tìm thấy trang</h2>
            <p>Rất tiếc, trang bạn đang tìm kiếm không tồn tại hoặc đã bị di chuyển.</p>
            
            <a href="<?= $homeURL ?>" class="btn btn-primary mt-3">Quay về Trang chủ</a>
            
        </div>
    </div>
</body>
</html>