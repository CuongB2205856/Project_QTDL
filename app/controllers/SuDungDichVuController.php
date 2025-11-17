<?php
namespace App\Controllers;

use App\Models\SuDungDichVu;
use App\Models\HopDong;
use App\Models\DichVu;

class SuDungDichVuController extends Controller
{
    private $model;
    private $hopdong_model;
    private $dichvu_model;

    public function __construct(\PDO $pdo)
    {
        parent::__construct();
        $this->model = new SuDungDichVu($pdo);
        $this->hopdong_model = new HopDong($pdo);
        $this->dichvu_model = new DichVu($pdo);

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

    /**
     * [KHÔNG ĐỔI] Hiển thị trang chính
     */
    public function index()
    {
        $data = [];
        // Giờ hàm $this->model->all() đã tồn tại (ở Bước 1)
        $data['sddv_list'] = $this->model->all(); 
        
        // Lấy danh sách cho form
        $data['hopdong_list_active'] = $this->hopdong_model->allActiveSimple();
        $data['dichvu_list_all'] = $this->dichvu_model->all();

        $this->loadView('SuDungDichVu/index', $data);
    }

    /**
     * [KHÔNG ĐỔI] Xử lý thêm mới dịch vụ (Điện, Nước, ...)
     */
    public function ajax_create()
    {
        try {
            $data = [
                'MaHD' => $_POST['MaHD'],
                'MaDV' => $_POST['MaDV'],
                'SoLuongSuDung' => $_POST['SoLuongSuDung'],
                'ThangSuDungDV' => $_POST['ThangSuDungDV'],
                'NamSuDungDV' => $_POST['NamSuDungDV']
            ];

            if (empty($data['MaHD']) || empty($data['MaDV']) || empty($data['SoLuongSuDung']) || empty($data['ThangSuDungDV']) || empty($data['NamSuDungDV'])) {
                throw new \Exception('Vui lòng nhập đầy đủ thông tin.');
            }

            // Không thêm Tiền phòng (MaDV=5) tại trang này
            if ($data['MaDV'] == 5) {
                throw new \Exception('Vui lòng dùng chức năng "Lập Hóa Đơn" để chốt tiền phòng.');
            }

            $this->model->create($data);

            $this->json_response([
                'success' => true,
                'message' => 'Thêm dịch vụ thành công! Hóa đơn đã được tự động tạo.'
            ]);

        } catch (\PDOException $e) {
            // Bắt lỗi Unique Constraint (UQ_SDDV)
            if ($e->getCode() == '23000') {
                $this->json_response(['success' => false, 'message' => 'Lỗi: Dịch vụ này đã được nhập cho hợp đồng này trong tháng này rồi.']);
            }
            $this->json_response(['success' => false, 'message' => 'Lỗi CSDL: ' . $e->getMessage()]);
        
        } catch (\Exception $e) {
            $this->json_response(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
        }
    }

    /**
     * [MỚI] Lấy chi tiết (dùng cho modal sửa)
     */
    public function ajax_get_details($id)
    {
        $data = $this->model->find($id);
        if ($data) {
            $this->json_response(['success' => true, 'data' => $data]);
        } else {
            $this->json_response(['success' => false, 'message' => 'Không tìm thấy.']);
        }
    }

    /**
     * [MỚI] Cập nhật số lượng
     */
    public function ajax_update($id)
    {
        try {
            $soLuong = $_POST['SoLuongSuDung'];
            if (empty($soLuong) || !is_numeric($soLuong) || $soLuong < 0) {
                 throw new \Exception('Số lượng không hợp lệ.');
            }
            
            $this->model->update($id, $soLuong);
            $this->json_response(['success' => true, 'message' => 'Cập nhật thành công!']);

        } catch (\Exception $e) {
             $this->json_response(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
        }
    }

    /**
     * [MỚI] Xóa một dòng dịch vụ
     */
    public function ajax_delete($id)
    {
        try {
            $this->model->delete($id);
            $this->json_response(['success' => true, 'message' => 'Xóa thành công!']);
        } catch (\PDOException $e) {
             // Bắt lỗi khóa ngoại (nếu hóa đơn đã tồn tại)
            if ($e->getCode() == '23000') {
                 $this->json_response(['success' => false, 'message' => 'Lỗi: Không thể xóa vì đã có Hóa đơn liên quan.']);
            }
            $this->json_response(['success' => false, 'message' => 'Lỗi CSDL: ' . $e->getMessage()]);
        }
    }
}
?>