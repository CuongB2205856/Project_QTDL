<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?php echo url('/') ?>">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-laugh-wink"></i>
        </div>
        <div class="sidebar-brand-text mx-3">Project QTDL</div>
    </a>

    <hr class="sidebar-divider my-0">

    <li class="nav-item active">
        <a class="nav-link" href="<?php echo url('/') ?>">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <hr class="sidebar-divider">

    <div class="sidebar-heading">
        Quản Lý Dữ Liệu
    </div>

    <li class="nav-item">
        <a class="nav-link" href="<?php echo url('loaiphong') ?>">
            <i class="fas fa-fw fa-couch"></i>
            <span>Quản Lý Loại Phòng</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="<?php echo url('phong') ?>">
            <i class="fas fa-fw fa-door-open"></i>
            <span>Quản Lý Phòng</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="<?php echo url('dichvu') ?>">
            <i class="fas fa-fw fa-wrench"></i>
            <span>Quản Lý Dịch Vụ</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="<?php echo url('hopdong') ?>">
            <i class="fas fa-fw fa-file-signature"></i>
            <span>Quản Lý Hợp Đồng</span>
        </a>
    </li>
    
    <li class="nav-item">
        <a class="nav-link" href="<?php echo url('hoadon') ?>">
            <i class="fas fa-fw fa-money-check-alt"></i>
            <span>Quản Lý Hóa Đơn</span>
        </a>
    </li>
    
    <li class="nav-item">
        <a class="nav-link" href="<?php echo url('sinhvien') ?>">
            <i class="fas fa-fw fa-user-friends"></i>
            <span>Quản Lý Sinh Viên</span>
        </a>
    </li>

    <hr class="sidebar-divider d-none d-md-block">

    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>