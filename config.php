<?php
// config.php

// Cấu hình CSDL
const DB_CONFIG = [
    'dbhost' => 'localhost',
    'dbname' => 'QuanLyKTX',
    'dbuser' => 'root', // Kiểm tra lại username MySQL của bạn
    'dbpass' => 'Cuong2004@**#' // THAY THẾ MẬT KHẨU MySQL CHÍNH XÁC CỦA BẠN
];

// Cấu hình Base URL (quan trọng cho routing)
define('BASE_URL', 'http://quanlyktx_dev.com');
?>