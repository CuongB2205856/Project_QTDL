<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Dashboard KTX'; ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" 
          rel="stylesheet" 
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" 
          crossorigin="anonymous">
    
    <link rel="stylesheet" 
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="/assets/CSS/Style.css">
    <link rel="icon" href="/CTU_logo.ico" type="image/x-icon">
    <style>
        /* Đẩy nội dung xuống dưới header (chiều cao 56px) */
        body {
            padding-top: 56px;
        }

        /* === Cấu hình Sidebar (Quan trọng) === */
        
        .sidebar {
            position: fixed; /* Cố định */
            top: 56px;       /* Nằm dưới header */
            bottom: 0;
            left: 0;
            width: 250px;    /* Chiều rộng sidebar */
            z-index: 1000;
            padding: 20px 0;
            background-color: #212529; /* Màu tối (giống bg-dark) */
            border-right: 1px solid #495057;
            
            /* Ẩn sidebar trên mobile và tablet (dưới 992px) */
            display: none; 
        }

        /* === Hiển thị sidebar trên PC (lg) === */
        @media (min-width: 992px) {
            .sidebar {
                display: block; /* Hiển thị lại sidebar */
            }
            /* Đẩy nội dung chính sang bên phải, chừa chỗ cho sidebar */
            .main-content {
                padding-left: 250px; 
            }
        }
        
        /* === Nút hamburger cho mobile (hiện khi < 992px) === */
        .mobile-sidebar-toggler {
            display: block;
        }
        @media (min-width: 992px) {
            .mobile-sidebar-toggler {
                display: none; /* Ẩn nút này đi trên PC */
            }
        }
        
        /* CSS cho các link trong sidebar */
        .sidebar .nav-link {
            color: #adb5bd;
            font-size: 1rem;
            padding: .75rem 1.5rem;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: #fff;
            background-color: #0d6efd; /* Màu active */
        }
        .sidebar .nav-link .bi {
            margin-right: 10px;
        }

    </style>
</head>
<body>

<nav class="navbar navbar-dark bg-dark fixed-top shadow-sm p-2">
    <div class="container-fluid">
    
        <button class="navbar-toggler mobile-sidebar-toggler" type="button" 
                data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar" 
                aria-controls="mobileSidebar">
            <span class="navbar-toggler-icon"></span>
        </button>

        <a class="navbar-brand d-flex align-items-center" href="/dashboard" 
           style="margin-left: 10px;">
            <img src="/image/CTU_logo.png" alt="Logo" 
                 style="max-height: 40px; margin-right: 15px;">
            <span class="d-none d-sm-inline">Quản lý KTX</span>
        </a>

        <div class="dropdown ms-auto">
            <a class="nav-link dropdown-toggle text-white" href="#" id="userDropdown" 
               role="button" data-bs-toggle="dropdown" 
               aria-expanded="false">
                <i class="bi bi-person-circle"></i> Xin Chào, <?php echo htmlspecialchars($_SESSION['username']) ?>
            </a>
            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark" 
                aria-labelledby="userDropdown">
                
                <li>
                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#adminChangePasswordModal">
                        <i class="bi bi-key-fill" style="margin-right: 5px;"></i> Đổi mật khẩu
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item" href="/logout">
                        <i class="bi bi-box-arrow-right" style="margin-right: 5px;"></i> Đăng xuất
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<nav class="sidebar d-none d-lg-block">
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item">
            <a href="/dashboard" 
               class="nav-link <?php echo ($currentRoute == '/dashboard') ? 'active' : ''; ?>">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a href="/users" 
               class="nav-link <?php echo ($currentRoute == '/users') ? 'active' : ''; ?>">
                <i class="bi bi-people-fill"></i> Quản lý Users
            </a>
        </li>
        <li class="nav-item">
            <a href="/sinhvien" 
               class="nav-link <?php echo ($currentRoute == '/sinhvien') ? 'active' : ''; ?>">
                <i class="bi bi-person-badge"></i> Sinh Viên
            </a>
        </li>
        <li class="nav-item">
            <a href="/phong" 
               class="nav-link <?php echo ($currentRoute == '/phong') ? 'active' : ''; ?>">
                <i class="bi bi-door-open"></i> Phòng
            </a>
        </li>
        <li class="nav-item">
            <a href="/loaiphong" 
               class="nav-link <?php echo ($currentRoute == '/loaiphong') ? 'active' : ''; ?>">
                <i class="bi bi-stack"></i> Loại Phòng
            </a>
        </li>
        <li class="nav-item">
            <a href="/hopdong" 
               class="nav-link <?php echo ($currentRoute == '/hopdong') ? 'active' : ''; ?>">
                <i class="bi bi-file-earmark-text"></i> Hợp đồng
            </a>
        </li>
        <li class="nav-item">
            <a href="/dichvu" 
               class="nav-link <?php echo ($currentRoute == '/dichvu') ? 'active' : ''; ?>">
                <i class="bi bi-plug-fill"></i> Dịch Vụ
            </a>
        </li>
        <li class="nav-item">
            <a href="/sudungdichvu" 
               class="nav-link <?php echo ($currentRoute == '/sudungdichvu') ? 'active' : ''; ?>">
                <i class="bi bi-plug-fill"></i> Sử Dụng Dịch Vụ
            </a>
        </li>
    </ul>
</nav>

<div class="offcanvas offcanvas-start bg-dark text-white d-lg-none" 
     tabindex="-1" id="mobileSidebar" 
     aria-labelledby="mobileSidebarLabel">
     
    <div class="offcanvas-header border-bottom border-secondary">
        <h5 class="offcanvas-title" id="mobileSidebarLabel">Menu Điều Hướng</h5>
        <button type="button" class="btn-close btn-close-white" 
                data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item">
                <a href="/dashboard" 
                   class="nav-link <?php echo ($currentRoute == '/dashboard') ? 'active' : ''; ?>">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a href="/users" 
                   class="nav-link <?php echo ($currentRoute == '/users') ? 'active' : ''; ?>">
                    <i class="bi bi-people-fill"></i> Quản lý Users
                </a>
            </li>
            <li class="nav-item">
                <a href="/sinhvien" 
                   class="nav-link <?php echo ($currentRoute == '/sinhvien') ? 'active' : ''; ?>">
                    <i class="bi bi-person-badge"></i> Sinh Viên
                </a>
            </li>
            <li class="nav-item">
                <a href="/phong" 
                   class="nav-link <?php echo ($currentRoute == '/phong') ? 'active' : ''; ?>">
                    <i class="bi bi-door-open"></i> Phòng
                </a>
            </li>
             <li class="nav-item">
                <a href="/loaiphong" 
                   class="nav-link <?php echo ($currentRoute == '/loaiphong') ? 'active' : ''; ?>">
                    <i class="bi bi-stack"></i> Loại Phòng
                </a>
            </li>
            <li class="nav-item">
                <a href="/hopdong" 
                   class="nav-link <?php echo ($currentRoute == '/hopdong') ? 'active' : ''; ?>">
                    <i class="bi bi-file-earmark-text"></i> Hợp đồng
                </a>
            </li>
             <li class="nav-item">
                <a href="/dichvu" 
                   class="nav-link <?php echo ($currentRoute == '/dichvu') ? 'active' : ''; ?>">
                    <i class="bi bi-plug-fill"></i> Dịch Vụ
                </a>
            </li>
        </ul>
    </div>
</div>


<main class="main-content">
    <div class="container-fluid pt-3">