<?php
// server.php

// File này cho phép chúng ta mô phỏng chức năng "mod_rewrite" của Apache
// từ built-in PHP web server, cung cấp cách tiện lợi để kiểm tra ứng dụng 
// mà không cần cài đặt phần mềm web server "thực tế".
// Cách sử dụng:
// php -S localhost:8080 -t public/ server.php

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
);

// Nếu URI không phải là thư mục gốc và file được yêu cầu 
// (ví dụ: CSS, JS, hình ảnh) tồn tại trong thư mục public,
// thì trả về file đó trực tiếp (không thông qua PHP).
if ($uri !== '/' && file_exists(__DIR__ . '/public' . $uri)) {
    return false;
}

// Nếu không phải là một file tĩnh, chuyển hướng yêu cầu đến 
// public/index.php để Router xử lý.
require_once __DIR__ . '/public/index.php';