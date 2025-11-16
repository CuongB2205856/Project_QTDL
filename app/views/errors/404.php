<?php 
// Lấy BASE_URL
$baseURL = defined('BASE_URL') ? BASE_URL : ''; 
$homeURL = $baseURL . '/login';

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

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Không tìm thấy trang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/CSS/StyleError.css">
</head>
<body>
    <div class="decorative-elements">
        <div class="circle"></div>
        <div class="circle"></div>
        <div class="circle"></div>
    </div>
    
    <div class="error-container">
        <i class="bi bi-exclamation-triangle error-icon"></i>
        <div class="error-code">404</div>
        <h1 class="error-title">Không tìm thấy trang</h1>
        <p class="error-description">
            Rất tiếc, trang bạn đang tìm kiếm không tồn tại hoặc đã bị di chuyển. 
            Vui lòng kiểm tra lại đường dẫn hoặc quay về trang chủ.
        </p>
        <a href="<?= $homeURL ?>" class="btn-home">
            <i class="bi bi-house-door"></i>
            Quay về Trang chủ
        </a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>