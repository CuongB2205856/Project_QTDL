<?php

namespace App\Controllers;

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

        $this->svModel = new SinhVien($pdo);
        $this->phongModel = new Phong($pdo);
        $this->hdModel = new HoaDon($pdo);

        // Kiểm tra session và quyền truy cập
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Phải đăng nhập
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        // Phải là 'QuanLy'
        if ($_SESSION['role'] !== 'QuanLy') {
            header('HTTP/1.1 404 Not Found'); // Gửi header 404
            $this->loadView('errors/404'); // Hiển thị trang 404
            exit; // Dừng lại
        }
    }

    // Hiển thị trang dashboard chính
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