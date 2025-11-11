<?php
// app/controllers/DashboardController.php
namespace App\Controllers;
// Nhúng Controller cơ sở
require_once __DIR__ . '/Controller.php';

// Nhúng các Models cần thiết
use App\Models\SinhVien;
use App\Models\Phong;
use App\Models\HoaDon;

class DashboardController extends Controller {
    
    private $svModel;
    private $phongModel;
    private $hdModel;

    public function __construct(\PDO $pdo) {
        parent::__construct(); 
        $this->svModel = new SinhVien($pdo);
        $this->phongModel = new Phong($pdo);
        $this->hdModel = new HoaDon($pdo);
    }

    /**
     * Hiển thị trang dashboard chính
     */
    public function index() {
        $data = [];

        try {
            // Lấy dữ liệu thống kê
            $data['total_students'] = $this->svModel->count();
            $data['total_rooms'] = $this->phongModel->count();
            
            // Doanh thu đã thanh toán
            $data['total_revenue_paid'] = $this->hdModel->getTotalRevenue('Đã thanh toán');
            
            // Hóa đơn chưa thanh toán
            $data['total_unpaid_invoices'] = $this->hdModel->countByStatus('Chưa thanh toán');

        } catch (Exception $e) {
            $data['error'] = "Lỗi khi tải thống kê: " . $e->getMessage();
        }

        // Tải view của dashboard
        $this->loadView('dashboard/index', $data);
    }
}
?>