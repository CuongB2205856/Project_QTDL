<?php
// app/controllers/HopDongController.php
namespace App\Controllers;
// Nhúng Controller cơ sở
require_once __DIR__ . '/Controller.php';

// Sử dụng các Model cần thiết
use App\Models\HopDong;
use App\Models\SinhVien;
use App\Models\Phong; 
use \PDOException; // Thêm để bắt lỗi CSDL

class HopDongController extends Controller {
    
    private $model;
    private $svModel;
    private $phongModel;

    public function __construct(\PDO $pdo) {
        parent::__construct(); 
        $this->model = new HopDong($pdo);
        $this->svModel = new SinhVien($pdo);
        $this->phongModel = new Phong($pdo); 
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // Trả về header JSON (Giống các Controller khác)
    private function json_response($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * HIỂN THỊ TRANG INDEX (Danh sách hợp đồng)
     * Xử lý: GET /hopdong
     */
    public function index() {
        $data = [];
        // Lấy danh sách hợp đồng (cần hàm all() trong model)
        $data['hopdong_list'] = $this->model->all(); 
        
        // Lấy danh sách SV (chưa có HĐ) cho modal Thêm mới
        $data['sinhvien_list'] = $this->svModel->allWithoutContract();
        
        // Lấy danh sách phòng (còn chỗ) cho modal Thêm mới (cần hàm allWithVacancy() trong model Phong)
        $data['phong_list'] = $this->phongModel->allWithVacancy(); 

        $this->loadView('HopDong/index', $data);
    }

    /**
     * (AJAX) LẤY CHI TIẾT 1 HỢP ĐỒNG ĐỂ SỬA
     * Xử lý: GET /api/hopdong/get/{id}
     */
    public function getHopDongDetails($id) {
        $data = $this->model->findDetails($id);
        if ($data) {
            $this->json_response(['success' => true, 'hopdong' => $data]);
        } else {
            $this->json_response(['success' => false, 'message' => 'Không tìm thấy hợp đồng.']);
        }
    }

    /**
     * (AJAX) XỬ LÝ THÊM MỚI HỢP ĐỒNG
     * Xử lý: POST /api/hopdong/create
     * (Đây là phương thức thay thế cho hàm create() cũ của bạn)
     */
    public function create() {
        try {
            $maSV = $_POST['MaSV'];
            $maPhong = $_POST['MaPhong'];
            $ngayBD = $_POST['NgayBatDau'];
            $ngayKT = $_POST['NgayKetThuc']; // View index.php có gửi trường này

            // --- 1. Validate Ngày ---
            if (empty($maSV) || empty($maPhong) || empty($ngayBD) || empty($ngayKT)) {
                throw new \Exception('Vui lòng nhập đầy đủ thông tin.');
            }
            if ($ngayKT <= $ngayBD) {
                 throw new \Exception('Ngày kết thúc phải sau ngày bắt đầu.');
            }

            // --- 2. Validate Sinh viên (Đã có HĐ chưa) ---
            $svCheck = $this->svModel->checkActiveContract($maSV);
            if ($svCheck > 0) {
                throw new \Exception('Sinh viên này đã có hợp đồng còn hạn.');
            }

            // --- 3. Validate Phòng (Còn chỗ không) ---
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
                'maphong' => $maPhong, // Thêm NgayLap
                'ngaybd' => $ngayBD,
                'ngaykt' => $ngayKT
            ]);

            if ($result) {
                // Tải lại trang (theo logic của view JS)
                $this->json_response([
                    'success' => true, 
                    'message' => 'Thêm hợp đồng thành công! Trang sẽ được tải lại.'
                ]);
            } else {
                throw new \Exception('Lỗi CSDL khi tạo hợp đồng.');
            }

        } catch (\Exception $e) {
            $this->json_response(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
        }
    }

    /**
     * (AJAX) XỬ LÝ CẬP NHẬT HỢP ĐỒNG
     * Xử lý: POST /api/hopdong/update
     */
    public function update() {
        try {
            $maHD = $_POST['MaHopDong'];
            $data = [
                'ngaybd' => $_POST['NgayBatDau'],
                'ngaykt' => $_POST['NgayKetThuc']
                // Không cho phép sửa MaSV hoặc MaPhong
            ];

            if (empty($maHD) || empty($data['ngaybd']) || empty($data['ngaykt'])) {
                 throw new \Exception('Vui lòng nhập đủ ngày bắt đầu và kết thúc.');
            }
             if ($data['ngaykt'] <= $data['ngaybd']) {
                 throw new \Exception('Ngày kết thúc phải sau ngày bắt đầu.');
            }

            $result = $this->model->update($maHD, $data);

             if ($result) {
                $this->json_response([
                    'success' => true, 
                    'message' => 'Cập nhật hợp đồng thành công! Trang sẽ được tải lại.'
                ]);
            } else {
                throw new \Exception('Lỗi CSDL khi cập nhật.');
            }

        } catch (\Exception $e) {
            $this->json_response(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
        }
    }

    /**
     * (AJAX) XỬ LÝ XÓA HỢP ĐỒNG
     * Xử lý: POST /api/hopdong/delete/{id}
     */
    public function delete($id)
    {
        try {
            $this->model->delete($id);
            $this->json_response(['success' => true, 'message' => 'Xóa hợp đồng thành công!']);
        
        } catch (PDOException $e) {
            // Xử lý lỗi khóa ngoại (nếu HĐ có hóa đơn)
            if ($e->getCode() == 23000) {
                $this->json_response([
                    'success' => false, 
                    'message' => 'Không thể xóa (Hợp đồng đã có Hóa đơn/Dịch vụ liên quan).'
                ]);
            } else {
                 $this->json_response([
                    'success' => false, 
                    'message' => 'Lỗi CSDL: ' . $e->getMessage()
                ]);
            }
        }
    }
}
?>