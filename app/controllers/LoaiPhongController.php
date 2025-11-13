<?php
// app/controllers/loaiphong.controller.php
namespace App\Controllers;
// Nhúng Controller cơ sở
require_once __DIR__ . '/Controller.php';
use App\Models\LoaiPhong; 

class LoaiPhongController extends Controller {
    private $model;

    public function __construct(\PDO $pdo) {
        parent::__construct(); 
        $this->model = new LoaiPhong($pdo);
        // Đảm bảo session đã khởi động để dùng cho thông báo (nếu cần)
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

    // Trả về header JSON
    private function json_response($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    // *** 1. HIỂN THỊ TRANG INDEX VÀ MODAL ***
    public function index() {
        $data = [];
        $data['loai_phong_list'] = $this->model->all();
        // Tải view 'Index' (trang danh sách)
        $this->loadView('LoaiPhong\Index', $data); 
    }

    // *** 2. (AJAX) LẤY CHI TIẾT 1 LOẠI PHÒNG ĐỂ SỬA ***
    public function ajax_get_details($id) {
        $data = $this->model->find($id);
        if ($data) {
            $this->json_response(['success' => true, 'data' => $data]);
        } else {
            $this->json_response(['success' => false, 'message' => 'Không tìm thấy loại phòng.']);
        }
    }

    // *** 3. (AJAX) XỬ LÝ THÊM MỚI ***
    public function ajax_create() {
        try {
            // Lấy ID của loại phòng vừa thêm
            $newId = $this->model->create($_POST);
            if ($newId) {
                // Lấy lại thông tin đầy đủ của loại phòng vừa thêm
                $newRow = $this->model->find($newId);
                $this->json_response([
                    'success' => true, 
                    'message' => 'Thêm loại phòng thành công!',
                    'newRow' => $newRow // Gửi dữ liệu hàng mới về JS
                ]);
            } else {
                $this->json_response(['success' => false, 'message' => 'Lỗi khi thêm loại phòng.']);
            }
        } catch (\Exception $e) {
            $this->json_response(['success' => false, 'message' => 'Lỗi CSDL: ' . $e->getMessage()]);
        }
    }

    // *** 4. (AJAX) XỬ LÝ CẬP NHẬT ***
    public function ajax_update($id) {
         try {
            $result = $this->model->update($id, $_POST);
            if ($result) {
                // Lấy lại thông tin đã cập nhật
                $updatedRow = $this->model->find($id);
                $this->json_response([
                    'success' => true, 
                    'message' => 'Cập nhật thành công!',
                    'updatedRow' => $updatedRow // Gửi dữ liệu đã sửa về JS
                ]);
            } else {
                $this->json_response(['success' => false, 'message' => 'Lỗi khi cập nhật.']);
            }
        } catch (\Exception $e) {
            $this->json_response(['success' => false, 'message' => 'Lỗi CSDL: ' . $e->getMessage()]);
        }
    }

    // *** 5. (AJAX) XỬ LÝ XÓA ***
    public function ajax_delete($id)
    {
        try {
            $this->model->delete($id);
            $this->json_response(['success' => true, 'message' => 'Xóa thành công!']);
        } catch (\PDOException $e) {
            $this->json_response([
                'success' => false, 
                'message' => 'Không thể xóa (có thể do đang được sử dụng).'
            ]);
        }
    }
}
?>