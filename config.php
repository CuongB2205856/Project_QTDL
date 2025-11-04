<?php
// config.php

// Cấu hình CSDL
const DB_CONFIG = [
    'dbhost' => 'localhost',
    'dbname' => 'QuanLyKyTucXa',
    'dbuser' => 'root', // Kiểm tra lại username MySQL của bạn
    'dbpass' => 'your_password' // THAY THẾ MẬT KHẨU MySQL CHÍNH XÁC CỦA BẠN
];

// Cấu hình Base URL (quan trọng cho routing)
define('BASE_URL', 'http://localhost/QLKTX/'); 

// Tải file Database ngay khi ứng dụng khởi động (cần thiết cho Models)
require_once 'models/Database.php'; 
?>