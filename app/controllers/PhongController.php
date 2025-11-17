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
            // Gọi model để xóa
            $this->model->delete($id);
            $this->json_response(['success' => true, 'message' => 'Xóa phòng thành công!']);

        } catch (\PDOException $e) {

            // 1. Bắt lỗi 45000 từ Trigger (Phòng đang được thuê)
            if ($e->getCode() == '45000') {
                $errorMessage = $e->getMessage();
                // Tách thông báo lỗi từ Trigger
                if (strpos($errorMessage, 'MESSAGE_TEXT') !== false) {
                    $parts = explode("MESSAGE_TEXT = ", $errorMessage);
                    $errorMessage = trim($parts[1] ?? 'Lỗi từ Trigger CSDL');
                }
                $this->json_response(['success' => false, 'message' => $errorMessage]);
            }

            // 2. Bắt lỗi 23000 (Lỗi khóa ngoại CSDL mặc định)
            else if ($e->getCode() == 23000) {
                $this->json_response([
                    'success' => false,
                    'message' => 'Lỗi CSDL: Không thể xóa (đã có Hợp đồng, Dịch vụ... liên quan).'
                ]);
            }

            // 3. Các lỗi CSDL khác
            else {
                $this->json_response([
                    'success' => false,
                    'message' => 'Lỗi CSDL: ' . $e->getMessage()
                ]);
            }
        }
    }
    public function ajax_GetSinhVienInPhong($soPhong)
    {
        try {
            if (empty($soPhong)) {
                throw new \Exception('Vui lòng nhập số phòng.');
            }

            // Gọi hàm Model để thực thi Stored Procedure
            $danhsach = $this->model->getSinhVienInPhong($soPhong);

            if (empty($danhsach)) {
                $this->json_response([
                    'success' => true,
                    'data' => [], // Trả về mảng rỗng
                    'message' => 'Phòng này (' . $soPhong . ') không có sinh viên nào hoặc không tồn tại.'
                ]);
            } else {
                $this->json_response([
                    'success' => true,
                    'data' => $danhsach
                ]);
            }

        } catch (\Exception $e) {
            $this->json_response(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
        }
    }
}
?>