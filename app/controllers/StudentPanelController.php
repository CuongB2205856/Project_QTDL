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
        
        // ==========================================================
        // GÁN CỨNG MÃ SV ĐỂ TEST (theo yêu cầu "không cần chứng thực")
        // Khi làm đăng nhập, bạn thay thế bằng $_SESSION['masv']
        // $this->maSV_test = 'SV001'; // <-- ĐÃ XÓA DÒNG NÀY
        // ==========================================================

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
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
        
        // $maSV = $this->maSV_test; // <-- ĐÃ XÓA DÒNG NÀY

        if (!$maSV) {
             // (Sau này) Chuyển hướng về trang đăng nhập
             die("Bạn chưa đăng nhập hoặc MaSV không hợp lệ!");
        }

        $data = [];
        // Lấy tất cả thông tin
        $data['details'] = $this->svModel->getStudentDashboardDetails($maSV);
        $data['maSV'] = $maSV;

        $this_view = 'StudentPanel/index';
        
        if (!$data['details']) {
            // Xử lý trường hợp không tìm thấy SV
            die("Không tìm thấy thông tin sinh viên: " . htmlspecialchars($maSV));
        }

        $this->loadView($this_view, $data);
    }

    // *** 2. (AJAX) XỬ LÝ ĐỔI MẬT KHẨU ***
    public function ajax_change_password() {
        
        // SỬA: Lấy MaSV từ SESSION, không dùng biến test
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