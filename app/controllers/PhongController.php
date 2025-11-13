<?php

namespace App\Controllers;

// Nhúng các Models cần thiết
use App\Models\Phong;
use App\Models\LoaiPhong;

class PhongController extends Controller
{
    private $model;
    private $loaiPhongModel;

    public function __construct(\PDO $pdo)
    {
        parent::__construct();
        $this->model = new Phong($pdo);
        $this->loaiPhongModel = new LoaiPhong($pdo);

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

    // Hiển thị trang danh sách phòng
    public function index()
    {
        $data = [];
        $data['phong_list'] = $this->model->all();
        $data['loai_phong_list'] = $this->loaiPhongModel->all();
        $this->loadView('Phong\Index', $data);
    }

    // Lấy chi tiết phòng
    public function ajax_get_details($id)
    {
        $data = $this->model->find($id);
        if ($data) {
            $this->json_response(['success' => true, 'data' => $data]);
        } else {
            $this->json_response(['success' => false, 'message' => 'Không tìm thấy phòng.']);
        }
    }

    // Xử lý thêm phòng
    public function ajax_create()
    {
        try {
            $newId = $this->model->create($_POST);
            if ($newId) {
                $newRow = $this->model->find($newId);
                $this->json_response([
                    'success' => true,
                    'message' => 'Thêm phòng thành công!',
                    'newRow' => $newRow // Gửi dữ liệu mới về JS
                ]);
            } else {
                $this->json_response(['success' => false, 'message' => 'Lỗi khi thêm phòng.']);
            }
        } catch (\Exception $e) {
            // Xử lý lỗi trùng `SoPhong` (UNIQUE)
            if ($e->getCode() == 23000) {
                $this->json_response(['success' => false, 'message' => 'Lỗi: Số phòng "' . $_POST['sophong'] . '" đã tồn tại.']);
            } else {
                $this->json_response(['success' => false, 'message' => 'Lỗi CSDL: ' . $e->getMessage()]);
            }
        }
    }

    // Xử lý cập nhật phòng
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
            // Xử lý lỗi trùng `SoPhong` (UNIQUE)
            if ($e->getCode() == 23000) {
                $this->json_response(['success' => false, 'message' => 'Lỗi: Số phòng "' . $_POST['sophong'] . '" đã tồn tại.']);
            } else {
                $this->json_response(['success' => false, 'message' => 'Lỗi CSDL: ' . $e->getMessage()]);
            }
        }
    }

    // Xử lý xóa phòng
    public function ajax_delete($id)
    {
        try {
            $this->model->delete($id);
            $this->json_response(['success' => true, 'message' => 'Xóa phòng thành công!']);
        } catch (\PDOException $e) {
            // Xử lý lỗi khóa ngoại (nếu có Hợp đồng)
            if ($e->getCode() == 23000) {
                $this->json_response([
                    'success' => false,
                    'message' => 'Không thể xóa phòng (đã có sinh viên ở).'
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