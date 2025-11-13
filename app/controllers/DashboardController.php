<?php
// app/controllers/DashboardController.php
namespace App\Controllers;
// Nhúng Controller cơ sở
require_once __DIR__ . '/Controller.php';

// Nhúng các Models cần thiết
use App\Models\SinhVien;
use App\Models\Phong;
use App\Models\HoaDon;

class DashboardController extends Controller
{

    private $svModel;
    private $phongModel;
    private $hdModel;

    public function __construct(\PDO $pdo)
    {
        parent::__construct();

        // 2. SỬA LỖI: Bỏ comment và khởi tạo 3 model
        $this->svModel = new SinhVien($pdo);
        $this->phongModel = new Phong($pdo);

        // Giả định $hdModel là HoaDon (dựa theo 'use' ở đầu file)
        $this->hdModel = new HoaDon($pdo);

        // ... (Phần session và kiểm tra quyền giữ nguyên) ...
        // --- BỘ LỌC BẢO VỆ ---
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // 1. Phải đăng nhập
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        // 2. Phải là 'QuanLy'
        if ($_SESSION['role'] !== 'QuanLy') {
            header('HTTP/1.1 404 Not Found'); // Gửi header 404
            $this->loadView('errors/404'); // Hiển thị trang 404
            exit; // Dừng lại
        }

        // --- KẾT THÚC BỘ LỌC ---
    }
    /**
     * Hiển thị trang dashboard chính
     */
    public function index()
    {
        $data = [];

        try {
            // Lấy dữ liệu thống kê
            $data['total_students'] = $this->svModel->count();
            $data['total_rooms'] = $this->phongModel->count();

            // Doanh thu đã thanh toán
            $data['total_revenue_paid'] = $this->hdModel->getTotalRevenue('Đã thanh toán');

            // Hóa đơn chưa thanh toán
            $data['total_unpaid_invoices'] = $this->hdModel->countByStatus('Chưa thanh toán');

        } catch (\Exception $e) {
            $data['error'] = "Lỗi khi tải thống kê: " . $e->getMessage();
        }

        // Tải view của dashboard
        $this->loadView('dashboard/index', $data);
    }
}
?>