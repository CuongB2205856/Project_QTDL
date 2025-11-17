<?php

namespace App\Controllers;

// Nhúng các model cần thiết cho thống kê
use App\Models\Phong;
use App\Models\SinhVien;
use App\Models\HoaDon;
use App\Models\HopDong; // Model này có thể cần bởi các model khác

class DashboardController extends Controller
{
    // Khai báo các model sẽ dùng
    private $phongModel;
    private $sinhVienModel;
    private $hoaDonModel;
    private $hopDongModel;

    public function __construct(\PDO $pdo)
    {
        parent::__construct();
        
        // Khởi tạo các model
        $this->phongModel = new Phong($pdo);
        $this->sinhVienModel = new SinhVien($pdo);
        $this->hoaDonModel = new HoaDon($pdo);
        $this->hopDongModel = new HopDong($pdo); 

        // Kiểm tra session (Giữ nguyên)
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }
        if ($_SESSION['role'] !== 'QuanLy') {
            header('HTTP/1.1 404 Not Found');
            $this->loadView('errors/404');
            exit;
        }
    }

    /**
     * [SỬA LẠI] Hiển thị trang Dashboard với dữ liệu thống kê
     */
    public function index()
    {
        $data = [];
        $stats = [];
        
        // 1. Lấy tháng/năm hiện tại
        $currentMonth = (int)date('m');
        $currentYear = (int)date('Y');
        
        // 2. Thống kê Phòng (Từ model Phong.php)
        $stats['rooms'] = [
            'total' => $this->phongModel->count(),
            'available' => $this->phongModel->countAvailable()
        ];

        // 3. Thống kê Sinh Viên (Từ model SinhVien.php)
        $stats['students'] = $this->sinhVienModel->getStudentStatistics();
        
        // 4. Thống kê Doanh thu (Tháng này) (Từ model HoaDon.php, gọi SP2)
        // [SP2 đã được sửa để xử lý đúng CSDL]
        $revenueData = $this->hoaDonModel->getBaoCaoDoanhThuThang($currentMonth, $currentYear);
        $stats['revenue'] = $revenueData['DoanhThuDaThanhToan'] ?? 0;
        
        // Gửi dữ liệu qua View
        $data['stats'] = $stats;
        $data['currentMonth'] = $currentMonth; // Gửi tháng hiện tại cho view

        $this->loadView('dashboard/index', $data);
    }

    /**
     * [KHÔNG ĐỔI] Xử lý POST từ Popup và xuất file CSV báo cáo (từ SP2)
     */
    public function export_report()
    {
        try {
            $thang = (int)($_POST['thang'] ?? date('m'));
            $nam = (int)($_POST['nam'] ?? date('Y'));

            // 1. Gọi Stored Procedure (SP2) qua Model
            $data = $this->hoaDonModel->getBaoCaoDoanhThuThang($thang, $nam);

            if (!$data) {
                throw new \Exception('Không có dữ liệu báo cáo cho tháng này.');
            }

            $filename = "BaoCao_DoanhThu_Thang_{$thang}_{$nam}.csv";

            // 2. Thiết lập HTTP Headers để trình duyệt tải file
            header('Content-Type: text/csv; charset=utf-8');
            header("Content-Disposition: attachment; filename=\"$filename\"");

            // 3. Tạo file CSV
            echo "\xEF\xBB\xBF"; // BOM cho UTF-8
            $output = fopen('php://output', 'w');
            
            // 4. Ghi dòng tiêu đề
            fputcsv($output, [
                'Thang', 
                'Nam', 
                'DoanhThuDaThanhToan (VND)', 
                'TienChuaThanhToan (VND)', 
                'TongSoHoaDon'
            ]);
            
            // 5. Ghi dòng dữ liệu
            fputcsv($output, [
                $thang,
                $nam,
                $data['DoanhThuDaThanhToan'],
                $data['TienChuaThanhToan'],
                $data['TongSoHoaDon']
            ]);
            
            fclose($output);
            exit;

        } catch (\Exception $e) {
            // (Bạn cần có hàm session_get_once trong functions.php để hiển thị lỗi)
            $_SESSION['flash_error'] = 'Lỗi xuất báo cáo: ' . $e->getMessage();
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }
    }
}
?>