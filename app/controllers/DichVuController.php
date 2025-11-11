<?php
// app/controllers/dichvu.controller.php
namespace App\Controllers;
// Nhúng Controller cơ sở
require_once __DIR__ . '/Controller.php';
use App\Models\DichVu;

class DichVuController extends Controller {
    private $model;

    public function __construct(\PDO $pdo) {
        parent::__construct();
        $this->model = new DichVu($pdo);
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

    // *** 1. HIỂN THỊ TRANG INDEX VÀ MODAL ***
    public function index() {
        $data = [];
        // Lấy danh sách dịch vụ
        $data['dichvu_list'] = $this->model->all();
        
        $this->loadView('DichVu\Index', $data);
    }

    // *** 2. (AJAX) LẤY CHI TIẾT 1 DỊCH VỤ ĐỂ SỬA ***
    public function ajax_get_details($id) {
        $data = $this->model->find($id);
        if ($data) {
            $this->json_response(['success' => true, 'data' => $data]);
        } else {
            $this->json_response(['success' => false, 'message' => 'Không tìm thấy dịch vụ.']);
        }
    }

    // *** 3. (AJAX) XỬ LÝ THÊM MỚI ***
    public function ajax_create() {
        try {
            $newId = $this->model->create($_POST); // ['tendv' => '...', 'dongia' => '...']
            if ($newId) {
                // Lấy lại thông tin đầy đủ
                $newRow = $this->model->find($newId);
                $this->json_response([
                    'success' => true, 
                    'message' => 'Thêm dịch vụ thành công!',
                    'newRow' => $newRow // Gửi dữ liệu hàng mới về JS
                ]);
            } else {
                $this->json_response(['success' => false, 'message' => 'Lỗi khi thêm dịch vụ.']);
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