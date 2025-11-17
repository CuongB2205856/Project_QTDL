<?php

namespace App\Controllers;

// Nhúng các model cần thiết
use App\Models\HoaDon;
use App\Models\HopDong;
use App\Models\DichVu;
use App\Models\SuDungDichVu;

class HoaDonController extends Controller
{
    private $model;
    private $hopdong_model;
    private $dichvu_model;
    private $sddv_model;

    public function __construct(\PDO $pdo)
    {
        parent::__construct();
        $this->model = new HoaDon($pdo);
        $this->hopdong_model = new HopDong($pdo); // Dùng cho form
        $this->dichvu_model = new DichVu($pdo);   // Dùng cho form
        $this->sddv_model = new SuDungDichVu($pdo);

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

    // Hiển thị danh sách hóa đơn
    public function index()
    {
        $data = [];
        // Lấy danh sách hóa đơn
        $data['hoadon_list'] = $this->model->allDetails();

        // Lấy dữ liệu cho form modal
        $data['hopdong_list'] = $this->hopdong_model->allActiveSimple();
        $data['dichvu_list'] = $this->dichvu_model->all();

        $this->loadView('HoaDon/index', $data);
    }

    // Lấy chi tiết hóa đơn (cho chức năng sửa nếu cần)
    public function ajax_get_details($id)
    {
        $data = $this->model->findDetails($id);
        if ($data) {
            $this->json_response(['success' => true, 'data' => $data]);
        } else {
            $this->json_response(['success' => false, 'message' => 'Không tìm thấy hóa đơn.']);
        }
    }

    // Xử lý thêm hóa đơn
    public function ajax_create()
    {
        try {
            $maHD = $_POST['MaHD'];
            $thang = (int)$_POST['ThangSuDungDV'];
            $nam = (int)$_POST['NamSuDungDV'];
            // $ngayHetHan = $_POST['NgayHetHan']; // (Không cần nữa, Trigger tự xử lý)

            if (empty($maHD) || empty($thang) || empty($nam)) {
                throw new \Exception('Vui lòng chọn Hợp đồng, Tháng và Năm.');
            }

            // [LOGIC MỚI]
            // Chỉ cần thêm Dịch vụ Tiền phòng (MaDV=5)
            $this->sddv_model->create([
                'MaHD' => $maHD,
                'MaDV' => 5, // 5 là "Tiền phòng"
                'SoLuongSuDung' => 1,
                'ThangSuDungDV' => $thang,
                'NamSuDungDV' => $nam
            ]);

            $this->json_response([
                'success' => true,
                'message' => "Đã chốt tiền phòng (Tháng $thang/$nam). Hóa đơn được tự động tạo."
            ]);

        } catch (\PDOException $e) {
            if ($e->getCode() == '23000') {
                 $this->json_response(['success' => false, 'message' => "Lỗi: Tiền phòng tháng $thang/$nam đã được chốt rồi."]);
            }
            $this->json_response(['success' => false, 'message' => 'Lỗi CSDL: ' . $e->getMessage()]);
        
        } catch (\Exception $e) {
            $this->json_response(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
        }
    }

    // Xử lý cập nhật trạng thái hóa đơn
    public function ajax_update_status($id)
    {
        try {
            $status = $_POST['status'] ?? 'Đã thanh toán';
            $result = $this->model->updateStatus($id, $status);

            if ($result) {
                // Lấy lại thông tin đã cập nhật
                $updatedRow = $this->model->findDetails($id);
                $this->json_response([
                    'success' => true,
                    'message' => 'Cập nhật trạng thái thành công!',
                    'updatedRow' => $updatedRow,
                ]);
            } else {
                $this->json_response(['success' => false, 'message' => 'Lỗi khi cập nhật.']);
            }
        } catch (\Exception $e) {
            $this->json_response(['success' => false, 'message' => 'Lỗi CSDL: ' . $e->getMessage()]);
        }
    }

    // Xử lý xóa hóa đơn
    public function ajax_delete($id)
    {
        try {
            // Sử dụng hàm deleteInvoice đã định nghĩa
            $this->model->deleteInvoice($id);
            $this->json_response(['success' => true, 'message' => 'Xóa hóa đơn thành công!']);
        } catch (\Exception $e) {
            $this->json_response([
                'success' => false,
                'message' => 'Lỗi CSDL: ' . $e->getMessage()
            ]);
        }
    }

    // Hàm kiểm tra trạng thái hợp đồng của 1 sinh viên
    public function checkTrangThaiHopDong($maSV)
    {
        try {
            if (empty($maSV)) {
                throw new \Exception('Vui lòng nhập Mã Sinh viên.');
            }

            // Gọi phương thức model đã tạo
            $tinhTrang = $this->hopdong_model->checkSinhVienQuaHan($maSV);

            if ($tinhTrang == 0) {
                // 0 = Còn hạn
                $message = "Sinh viên [{$maSV}] CÒN HẠN hợp đồng.";
            } else {
                // 1 = Hết hạn (hoặc không có HĐ)
                $message = "Sinh viên [{$maSV}] ĐÃ HẾT HẠN hoặc KHÔNG CÓ hợp đồng.";
            }

            $this->json_response([
                'success' => true,
                'status' => $tinhTrang, // 0 hoặc 1
                'message' => $message
            ]);

        } catch (\Exception $e) {
            $this->json_response(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
        }
    }
}
?>