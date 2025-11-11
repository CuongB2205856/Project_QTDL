<?php

/**
 * Tạo URL tuyệt đối dựa trên BASE_URL đã định nghĩa.
 * Yêu cầu hằng số BASE_URL phải được định nghĩa (thường trong config.php).
 */
function url($path = '')
{
    // Đảm bảo BASE_URL đã được định nghĩa
    if (!defined('BASE_URL')) {
        die('Lỗi: Hằng số BASE_URL chưa được định nghĩa.');
    }

    // Xóa dấu '/' ở cuối BASE_URL (nếu có)
    $base_url = rtrim(BASE_URL, '/');
    
    // Xóa dấu '/' ở đầu $path (nếu có) để tránh bị lặp '//'
    $path = ltrim($path, '/');

    // Trả về URL hoàn chỉnh
    return $base_url . '/' . $path;
}

/**
 * Hàm gỡ lỗi (Debug and Die).
 * In ra biến và dừng thực thi chương trình.
 */
if (!function_exists('dd')) {
  function dd($var)
  {
    echo '<pre>'; // Định dạng cho dễ đọc
    var_dump($var);
    echo '</pre>';
    exit();
  }
}

/**
 * Chuyển hướng đến một URL khác.
 * Hỗ trợ truyền "flash message" qua session.
 * * @param string $location URL đầy đủ (nên được tạo bằng hàm url()).
 * @param array $data Dữ liệu để lưu vào session (flash message).
 */
if (!function_exists('redirect')) {
  function redirect($location, array $data = [])
  {
    foreach ($data as $key => $value) {
      $_SESSION[$key] = $value;
    }

    // Thực hiện chuyển hướng bằng URL tuyệt đối
    header('Location: ' . $location, true, 302);
    exit();
  }
}

/**
 * Đọc và xóa một biến trong $_SESSION (flash message).
 *
 * @param string $name Tên biến session.
 * @param mixed $default Giá trị trả về nếu biến không tồn tại.
 */
if (!function_exists('session_get_once')) {
  function session_get_once($name, $default = null)
  {
    $value = $default;
    if (isset($_SESSION[$name])) {
      $value = $_SESSION[$name];
      unset($_SESSION[$name]);
    }
    return $value;
  }
}

/**
 * Mã hóa ký tự đặc biệt HTML để chống XSS.
 */
function e($value)
{
  return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}