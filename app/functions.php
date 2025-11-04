<?php

if (!function_exists('PDO')) {
  function PDO(): PDO
  {
    global $PDO;
    return $PDO;
  }
}

if (!function_exists('AUTHGUARD')) {
  function AUTHGUARD(): App\SessionGuard
  {
    global $AUTHGUARD;
    return $AUTHGUARD;
  }
}

if (!function_exists('dd')) {
  function dd($var)
  {
    var_dump($var);
    exit();
  }
}

if (!function_exists('redirect')) {
  // Chuyển hướng đến một trang khác
  function redirect($location, array $data = [])
  {
    foreach ($data as $key => $value) {
      $_SESSION[$key] = $value;
    }

    header('Location: ' . $location, true, 302);
    exit();
  }
}

if (!function_exists('session_get_once')) {
  // Đọc và xóa một biến trong $_SESSION
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

function e($value)
{
  return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function generate_csrf_token(): string
{
  if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
  }
  return $_SESSION['csrf_token'];
}

function csrf_token_input(): string
{
  $token = generate_csrf_token();
  return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
}

function verify_csrf_token($token): bool
{
  return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
