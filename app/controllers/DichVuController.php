<?php

namespace App\Controllers;

// Nhúng Model các model cần thiết
use App\Models\DichVu;

class DichVuController extends Controller
{
    private $model;

    public function __construct(\PDO $pdo)
    {
        parent::__construct();
        $this->model = new DichVu($pdo);

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

    // Hiên thị danh sách dịch vụ
    public function index()
    {
        $data = [];
        // Lấy danh sách dịch vụ
        $data['dichvu_list'] = $this->model->all();
        $this->loadView('DichVu\Index', $data);
    }

    // Lấy chi tiết dịch vụ
    public function ajax_get_details($id)
    {
        $data = $this->model->find($id);
        if ($data) {
            $this->json_response(['success' => true, 'data' => $data]);
        } else {
            $this->json_response(['success' => false, 'message' => 'Không tìm thấy dịch vụ.']);
        }
    }

    // Xử lý thêm dịch vụ
    public function ajax_create()
    {
        try {
            $newId = $this->model->create($_POST);
            if ($newId) {
                // Lấy lại thông tin đầy đủ
                $newRow = $this->model->find($newId);
                $this->json_response([
                    'success' => true,
                    'message' => 'Thêm dịch vụ thành công!',
                    'newRow' => $newRow,
                ]);
            } else {
                $this->json_response(['success' => false, 'message' => 'Lỗi khi thêm dịch vụ.']);
            }
        } catch (\Exception $e) {
            $this->json_response(['success' => false, 'message' => 'Lỗi CSDL: ' . $e->getMessage()]);
        }
    }

    // Xử lý cập nhật dịch vụ
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
                    'updatedRow' => $updatedRow,
                ]);
            } else {
                $this->json_response(['success' => false, 'message' => 'Lỗi khi cập nhật.']);
            }
        } catch (\Exception $e) {
            $this->json_response(['success' => false, 'message' => 'Lỗi CSDL: ' . $e->getMessage()]);
        }
    }

    // Xử lý xóa dịch vụ
    public function ajax_delete($id)
    {
        try {
            $this->model->delete($id);
            $this->json_response(['success' => true, 'message' => 'Xóa dịch vụ thành công!']);
        } catch (\PDOException $e) {

            // Xử lý lỗi khóa ngoại (nếu có SuDungDichVu)
            if ($e->getCode() == 23000) {
                $this->json_response([
                    'success' => false,
                    'message' => 'Không thể xóa (dịch vụ này đã được sử dụng).'
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