<?php
$baseURL = defined('BASE_URL') ? BASE_URL : ''; 

// ==========================================================
// LOGIC ĐỂ XÁC ĐỊNH ĐÚNG TRANG CHỦ
// =ia
$homeURL = $baseURL . '/login'; // Mặc định là trang login nếu chưa đăng nhập
// app/views/studentpanel/student_header.php
$title = $title ?? 'Bảng Điều Khiển Sinh Viên'; 
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
    <title><?php echo htmlspecialchars($title); ?></title>

    <link rel="icon" href="/CTU_logo.ico" type="image/x-icon">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" 
          rel="stylesheet" 
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" 
          crossorigin="anonymous">
    
    <link rel="stylesheet" 
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --card-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            --card-shadow-hover: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        body {
            background-color: #f8f9fa;
            padding-top: 70px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* Navbar Styles */
        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            box-shadow: 0 2px 4px rgba(0,0,0,.1);
        }

        .navbar-brand {
            font-weight: 600;
            font-size: 1.1rem;
        }

        .navbar-brand img {
            transition: transform 0.3s ease;
        }

        .navbar-brand:hover img {
            transform: scale(1.1);
        }

        /* Main Content */
        .main-content {
            min-height: calc(100vh - 140px);
            padding-bottom: 2rem;
        }

        /* Card Styles */
        .card {
            border-radius: 12px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .hover-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--card-shadow-hover);
        }

        .card-header {
            border-radius: 12px 12px 0 0 !important;
            font-weight: 600;
        }

        /* Button Styles */
        .btn {
            border-radius: 8px;
            padding: 0.5rem 1.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }

        /* Badge Styles */
        .badge {
            border-radius: 6px;
            padding: 0.5em 0.75em;
            font-weight: 500;
        }

        /* List Group */
        .list-group-item {
            border: none;
            border-bottom: 1px solid #f0f0f0;
            padding: 1rem 0;
        }

        .list-group-item:last-child {
            border-bottom: none;
        }

        /* Modal Styles */
        .modal-content {
            border-radius: 12px;
        }

        .modal-header {
            border-radius: 12px 12px 0 0;
        }

        /* Form Styles */
        .form-control, .form-select {
            border-radius: 8px;
            padding: 0.625rem 0.875rem;
            border: 1px solid #dee2e6;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .form-label {
            font-weight: 500;
            color: #495057;
            margin-bottom: 0.5rem;
        }

        /* Alert Styles */
        .alert {
            border-radius: 8px;
            border: none;
        }

        /* Icon Styles */
        .bi, .fas, .far, .fab {
            vertical-align: middle;
        }

        /* Gradient Background */
        .bg-gradient {
            background: var(--primary-gradient) !important;
        }

        /* Responsive */
        @media (max-width: 768px) {
            body {
                padding-top: 60px;
            }

            .navbar-brand span {
                font-size: 0.9rem;
            }

            .card {
                margin-bottom: 1rem;
            }
        }

        /* Loading Animation */
        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .spinner {
            animation: spin 1s linear infinite;
        }

        /* Smooth Scrolling */
        html {
            scroll-behavior: smooth;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 10px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 5px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-dark fixed-top shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center" href="<?= $homeURL ?>">
            <img src="/image/CTU_logo.png" alt="Logo" 
                 style="max-height: 35px; margin-right: 10px;">
            <span>Bảng Điều Khiển Sinh Viên</span>
        </a>
        <div class="ms-auto">
            <a class="btn btn-outline-light" href="/logout">
                <i class="bi bi-box-arrow-right me-1"></i>
                <span class="d-none d-sm-inline">Đăng xuất</span>
            </a>
        </div>
    </div>
</nav>

<main class="main-content container mt-4">