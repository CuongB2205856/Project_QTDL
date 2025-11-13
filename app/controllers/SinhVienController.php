<?php
// app/controllers/SinhVienController.php
namespace App\Controllers;
// Nhúng Controller cơ sở
require_once __DIR__ . '/Controller.php';

use App\Models\SinhVien;
use App\Models\Users; // Thêm model Users

class SinhVienController extends Controller {
    private $model;
    private $userModel; // Thêm biến cho model Users

    public function __construct(\PDO $pdo) {
        parent::__construct(); 
        $this->model = new SinhVien($pdo);
        $this->userModel = new Users($pdo); // Khởi tạo
        
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
    }

    // Trả về header JSON (Giống PhongController)
    private function json_response($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    // *** 1. HIỂN THỊ TRANG INDEX ***
    public function index() {
        $data = [];
        $data['sinhvien_list'] = $this->model->all(); 
        $this->loadView('SinhVien/index', $data);
    }

    // *** 2. (AJAX) LẤY CHI TIẾT 1 SV ĐỂ SỬA ***
    public function ajax_get_details($maSV) {
        $data = $this->model->findById($maSV);
        if ($data) {
            $this->json_response(['success' => true, 'data' => $data]);
        } else {
            $this->json_response(['success' => false, 'message' => 'Không tìm thấy sinh viên.']);
        }
    }

    // *** 3. (AJAX) LẤY CHI TIẾT PHÒNG Ở ***
    public function ajax_get_room_details($maSV) {
        $data = $this->model->findDetails($maSV);
        if ($data) {
            $this->json_response(['success' => true, 'data' => $data]);
        } else {
            $this->json_response(['success' => false, 'message' => 'Sinh viên này hiện không có hợp đồng (hoặc hợp đồng đã hết hạn).']);
        }
    }

    // *** 4. (AJAX) XỬ LÝ CẬP NHẬT ***
    public function ajax_update($maSV) {
         try {
            $result = $this->model->update($maSV, $_POST);
            if ($result) {
                // Lấy lại thông tin đã cập nhật (chỉ thông tin SV)
                $updatedRow = $this->model->findById($maSV);
                $this->json_response([
                    'success' => true, 
                    'message' => 'Cập nhật thành công!',
                    'updatedRowData' => $updatedRow // Gửi dữ liệu đã sửa về JS
                ]);
            } else {
                $this->json_response(['success' => false, 'message' => 'Lỗi khi cập nhật.']);
            }
        } catch (\Exception $e) {
            $this->json_response(['success' => false, 'message' => 'Lỗi CSDL: ' . $e->getMessage()]);
        }
    }

    // *** 5. (AJAX) XỬ LÝ XÓA ***
    public function ajax_delete($maSV)
    {
        try {
            // (Giống PhongController)
            $this->model->delete($maSV);
            $this->json_response(['success' => true, 'message' => 'Xóa sinh viên thành công!']);
        } catch (\PDOException $e) {
            // Xử lý lỗi khóa ngoại (nếu SV có Hợp đồng)
            if ($e->getCode() == 23000) {
                $this->json_response([
                    'success' => false, 
                    'message' => 'Không thể xóa sinh viên (đã có hợp đồng). Bạn cần xóa hợp đồng trước.'
                ]);
            } else {
                 $this->json_response([
                    'success' => false, 
                    'message' => 'Lỗi CSDL: ' . $e->getMessage()
                ]);
            }
        }
    }

    // *** 6. (AJAX) RESET MẬT KHẨU ***
    public function ajax_reset_password($maSV)
    {
        try {
            // Đặt lại MK mặc định, ví dụ '123456'
            $defaultPassword = '123456';
            $result = $this->userModel->resetPassword($maSV, $defaultPassword);
            
            if ($result) {
                $this->json_response(['success' => true, 'message' => 'Đặt lại mật khẩu thành công (MK mặc định là 123456).']);
            } else {
                $this->json_response(['success' => false, 'message' => 'Lỗi: Không tìm thấy tài khoản (user) ứng với Mã SV này.']);
            }
        } catch (\Exception $e) {
            $this->json_response(['success' => false, 'message' => 'Lỗi CSDL: ' . $e->getMessage()]);
        }
    }
}
?>