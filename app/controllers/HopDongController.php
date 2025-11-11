<?php
// app/controllers/HopDongController.php
namespace App\Controllers;
// Nhúng Controller cơ sở

// Sử dụng các Model cần thiết
use App\Models\HopDong;
use App\Models\SinhVien;
use App\Models\Phong; 

class HopDongController extends Controller {
    
    private $model;
    private $svModel;
    private $phongModel;

    public function __construct(\PDO $pdo) {
        parent::__construct(); 
        $this->model = new HopDong($pdo);
        $this->svModel = new SinhVien($pdo);
        // Khởi tạo PhongModel (giống như trong PhongController)
        // Chúng ta cần nó để lấy chi tiết phòng (Số lượng tối đa)
        $this->phongModel = new Phong($pdo); 
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Xử lý trang TẠO HỢP ĐỒNG (GET để hiển thị form, POST để xử lý)
     */
    public function create() {
        $data = [
            'sinhvien_list' => [],
            'phong_list' => [],
            'message' => $_SESSION['message'] ?? null,
            'message_type' => $_SESSION['message_type'] ?? 'info'
        ];
        
        // Xóa session message sau khi đọc
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);

        // Xử lý khi người dùng SUBMIT FORM (POST)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $maSV = $_POST['masv'];
                $maPhong = $_POST['maphong'];
                $ngayBD = $_POST['ngaybd'];
                $ngayKT = $_POST['ngaykt'];

                // --- 1. Validate Ngày ---
                if (empty($maSV) || empty($maPhong) || empty($ngayBD) || empty($ngayKT)) {
                    throw new \Exception('Vui lòng nhập đầy đủ thông tin.');
                }
                if ($ngayKT <= $ngayBD) {
                     throw new \Exception('Ngày kết thúc phải sau ngày bắt đầu.');
                }

                // --- 2. Validate Sinh viên ---
                $svCheck = $this->svModel->checkActiveContract($maSV);
                if ($svCheck > 0) {
                    throw new \Exception('Sinh viên này đã có hợp đồng còn hạn.');
                }

                // --- 3. Validate Phòng ---
                // (Giả sử PhongModel có hàm find() giống PhongController)
                $phong = $this->phongModel->find($maPhong); 
                if (!$phong) {
                     throw new \Exception('Không tìm thấy phòng đã chọn.');
                }
                
                $slToiDa = $phong['SoLuongToiDa'];
                $slHienTai = $this->model->countCurrentOccupants($maPhong);

                if ($slHienTai >= $slToiDa) {
                    throw new \Exception('Phòng đã đầy, không thể thêm sinh viên.');
                }

                // --- 4. Tạo Hợp đồng ---
                $result = $this->model->create([
                    'masv' => $maSV,
                    'maphong' => $maPhong,
                    'ngaybd' => $ngayBD,
                    'ngaykt' => $ngayKT
                ]);

                if ($result) {
                    $_SESSION['message'] = "Thêm hợp đồng thành công!";
                    $_SESSION['message_type'] = 'success';
                } else {
                    throw new \Exception('Lỗi CSDL khi tạo hợp đồng.');
                }

            } catch (\Exception $e) {
                $_SESSION['message'] = 'Lỗi: ' . $e->getMessage();
                $_SESSION['message_type'] = 'danger';
            }
            
            // Redirect về chính trang create (để tránh F5 submit lại)
            header('Location: ' . url('hopdong/create'));
            exit;
        }

        // Xử lý khi vào trang (GET)
        // Lấy danh sách SV chưa có phòng
        $data['sinhvien_list'] = $this->svModel->allWithoutContract();
        // Lấy tất cả các phòng (giả sử PhongModel có hàm all())
        $data['phong_list'] = $this->phongModel->all(); 
        
        $this->loadView('HopDong/Create', $data);
    }
}
?>