<?php
// app/controllers/StudentPanelController.php
namespace App\Controllers;
// Nhúng Controller cơ sở
require_once __DIR__ . '/Controller.php';

use App\Models\SinhVien;
use App\Models\Users;

class StudentPanelController extends Controller {
    
    private $svModel;
    private $userModel;
    // private $maSV_test; // BIẾN TẠM THỜI ĐỂ TEST - ĐÃ XÓA

    public function __construct(\PDO $pdo) {
        parent::__construct(); 
        $this->svModel = new SinhVien($pdo);
        $this->userModel = new Users($pdo);

        // 1. Phải đăng nhập
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        // 2. SỬA LẠI: Phải là 'SinhVien'
        // Nếu không phải, GỬI LỖI 404
        if ($_SESSION['role'] !== 'SinhVien') {
            header('HTTP/1.1 404 Not Found'); // Gửi header 404
            $this->loadView('errors/404'); // Hiển thị trang 404
            exit; // Dừng lại
        }
        // --- KẾT THÚC BỘ LỌC ---
    }
    // Trả về header JSON
    private function json_response($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    // *** 1. HIỂN THỊ TRANG PROFILE ***
    // SỬA: Thêm tham số $maSV
    public function index($maSV) { 
        
        // 3. SỬA LẠI: Kiểm tra quyền sở hữu
        // MaSV trên URL phải khớp với người đang đăng nhập
        if ($maSV !== $_SESSION['ma_lien_ket']) {
             header('HTTP/1.1 404 Not Found'); // Gửi header 404
             $this->loadView('errors/404'); // Hiển thị trang 404
             exit;
        }
        
        $data = [];
        $data['details'] = $this->svModel->getStudentDashboardDetails($maSV);
        // ... (phần còn lại của hàm giữ nguyên) ...
        $this->loadView('StudentPanel/index', $data);
    }

    // SỬA LẠI hàm ajax_change_password()
    public function ajax_change_password() {
        
        // Phải lấy MaSV từ SESSION, không dùng biến test
        $maSV = $_SESSION['ma_lien_ket'] ?? null; 

        if (!$maSV) {
            $this->json_response(['success' => false, 'message' => 'Phiên làm việc hết hạn.']);
            return;
        }

        $old_pass = $_POST['old_pass'] ?? '';
        $new_pass = $_POST['new_pass'] ?? '';

        if (empty($old_pass) || empty($new_pass)) {
             $this->json_response(['success' => false, 'message' => 'Vui lòng nhập đủ mật khẩu cũ và mới.']);
             return;
        }

        try {
            // Dùng MaSV từ session để đổi mật khẩu
            $result = $this->userModel->changePassword($maSV, $old_pass, $new_pass);
            $this->json_response($result);

        } catch (\Exception $e) {
            $this->json_response(['success' => false, 'message' => 'Lỗi CSDL: ' . $e->getMessage()]);
        }
    }
}
?>