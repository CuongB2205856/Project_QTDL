<?php

namespace App\Controllers;

// Nhúng các Models cần thiết
use App\Models\LoaiPhong;

class LoaiPhongController extends Controller
{
    private $model;

    public function __construct(\PDO $pdo)
    {
        parent::__construct();
        $this->model = new LoaiPhong($pdo);

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

    // Hiển thị trang danh sách loại phòng
    public function index()
    {
        $data = [];
        $data['loai_phong_list'] = $this->model->all();
        $this->loadView('LoaiPhong\Index', $data);
    }

    // Lấy chi tiết loại phòng
    public function ajax_get_details($id)
    {
        $data = $this->model->find($id);
        if ($data) {
            $this->json_response(['success' => true, 'data' => $data]);
        } else {
            $this->json_response(['success' => false, 'message' => 'Không tìm thấy loại phòng.']);
        }
    }

    // Xử lý thêm loại phòng
    public function ajax_create()
    {
        try {
            // Lấy ID của loại phòng vừa thêm
            $newId = $this->model->create($_POST);
            if ($newId) {
                // Lấy lại thông tin đầy đủ của loại phòng vừa thêm
                $newRow = $this->model->find($newId);
                $this->json_response([
                    'success' => true,
                    'message' => 'Thêm loại phòng thành công!',
                    'newRow' => $newRow // Gửi dữ liệu mới về JS
                ]);
            } else {
                $this->json_response(['success' => false, 'message' => 'Lỗi khi thêm loại phòng.']);
            }
        } catch (\Exception $e) {
            $this->json_response(['success' => false, 'message' => 'Lỗi CSDL: ' . $e->getMessage()]);
        }
    }

    // Xử lý cập nhật loại phòng
    public function ajax_update($id)
    {
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

    // Xử lý xóa loại phòng
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