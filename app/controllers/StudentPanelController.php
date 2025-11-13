<?php

namespace App\Controllers;

// Nhúng các Models cần thiết
use App\Models\SinhVien;
use App\Models\Users;

class StudentPanelController extends Controller
{

    private $svModel;
    private $userModel;

    public function __construct(\PDO $pdo)
    {
        parent::__construct();
        $this->svModel = new SinhVien($pdo);
        $this->userModel = new Users($pdo);

        // Phải đăng nhập
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        // Phải là 'SinhVien'
        if ($_SESSION['role'] !== 'SinhVien') {
            header('HTTP/1.1 404 Not Found');
            $this->loadView('errors/404');
            exit;
        }
    }

    // Trả về header JSON
    private function json_response($data)
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    // Hiển thị bảng điều khiển sinh viên
    public function index($maSV)
    {
        // Kiểm tra MaSV từ URL có khớp với MaSV trong SESSION không
        if ($maSV !== $_SESSION['ma_lien_ket']) {
            header('HTTP/1.1 404 Not Found');
            $this->loadView('errors/404');
            exit;
        }
        // Lấy dữ liệu bảng điều khiển cho sinh viên        
        $data = [];
        $data['details'] = $this->svModel->getStudentDashboardDetails($maSV);
        $this->loadView('StudentPanel/index', $data);
    }

    // Đổi mật khẩu cho sinh viên
    public function ajax_change_password()
    {
        // Lấy MaSV từ SESSION
        $maSV = $_SESSION['ma_lien_ket'] ?? null;
        // Kiểm tra MaSV
        if (!$maSV) {
            $this->json_response(['success' => false, 'message' => 'Phiên làm việc hết hạn.']);
            return;
        }

        // Lấy dữ liệu từ POST
        $old_pass = $_POST['old_pass'] ?? '';
        $new_pass = $_POST['new_pass'] ?? '';

        // Kiểm tra dữ liệu đầu vào
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