<?php
// app/controllers/Controller.php (Lớp cơ sở)
namespace App\Controllers;
class Controller
{
    // Cần phải biết đường dẫn gốc của dự án để tải View
    protected $viewRoot;

    public function __construct()
    {        // 2. Định nghĩa viewRoot (ROOT_PATH đã có sẵn sau khi config.php được nhúng)
        $this->viewRoot = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR;
    }
    protected function loadView(string $viewName, array $data = [])
    {
        // 1. Giải nén dữ liệu
        extract($data);

        // 2. Xây dựng đường dẫn View tuyệt đối
        $viewPath = $this->viewRoot . strtolower($viewName) . '.php';

        // Kiểm tra và tải View
        if (file_exists($viewPath)) {
            require_once $viewPath;
        } else {
            // Xử lý lỗi nếu file View không tồn tại
            die("Lỗi 500: Không tìm thấy View tại đường dẫn: " . htmlspecialchars($viewPath));
        }
    }

    // Hàm 404 (Controller mẫu của Bramus Router)
    public function sendNotFound()
    {
        header("HTTP/1.0 404 Not Found");
        echo "<h1>404 Not Found</h1>";
    }
}