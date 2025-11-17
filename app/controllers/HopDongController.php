<?php

namespace App\Controllers;

// Nhúng các Models cần thiết
use App\Models\HopDong;
use App\Models\SinhVien;
use App\Models\Phong;

class HopDongController extends Controller
{

    private $model;
    private $svModel;
    private $phongModel;

    public function __construct(\PDO $pdo)
    {
        parent::__construct();
        $this->model = new HopDong($pdo);
        $this->svModel = new SinhVien($pdo);
        $this->phongModel = new Phong($pdo);

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

    // Hiển thị danh sách hợp đồng
    public function index()
    {
        $data = [];
        $data['hopdong_list'] = $this->model->all();
        $data['sinhvien_list'] = $this->svModel->allWithoutContract();
        $data['phong_list'] = $this->phongModel->allWithVacancy();
        $this->loadView('HopDong/index', $data);
    }

    // Lấy chi tiết 1 hợp đồng
    public function getHopDongDetails($id)
    {
        $data = $this->model->findDetails($id);
        if ($data) {
            $this->json_response(['success' => true, 'hopdong' => $data]);
        } else {
            $this->json_response(['success' => false, 'message' => 'Không tìm thấy hợp đồng.']);
        }
    }

    // Xử lý thêm mới hợp đồng
    public function create()
    {
        try {
            $maSV = $_POST['MaSV'];
            $maPhong = $_POST['MaPhong'];
            $ngayBD = $_POST['NgayBatDau'];
            $ngayKT = $_POST['NgayKetThuc'];

            if (empty($maSV) || empty($maPhong) || empty($ngayBD) || empty($ngayKT)) {
                throw new \Exception('Vui lòng nhập đầy đủ thông tin.');
            }
            if ($ngayKT <= $ngayBD) {
                throw new \Exception('Ngày kết thúc phải sau ngày bắt đầu.');
            }

            // Validate Sinh viên (Đã có HĐ chưa)
            $svCheck = $this->svModel->checkActiveContract($maSV);
            if ($svCheck > 0) {
                throw new \Exception('Sinh viên này đã có hợp đồng còn hạn.');
            }

            // Validate Phòng (Còn chỗ không)
            $phong = $this->phongModel->find($maPhong);
            if (!$phong) {
                throw new \Exception('Không tìm thấy phòng đã chọn.');
            }

            $slToiDa = $phong['SoLuongToiDa'];
            $slHienTai = $this->model->countCurrentOccupants($maPhong);

            if ($slHienTai >= $slToiDa) {
                throw new \Exception('Phòng đã đầy, không thể thêm sinh viên.');
            }

            // Tạo Hợp đồng
            $result = $this->model->create([
                'masv' => $maSV,
                'maphong' => $maPhong,
                'ngaybd' => $ngayBD,
                'ngaykt' => $ngayKT
            ]);

            if ($result) {
                // Tải lại trang (theo logic của view JS)
                $this->json_response([
                    'success' => true,
                    'message' => 'Thêm hợp đồng thành công!'
                ]);
            } else {
                throw new \Exception('Lỗi CSDL khi tạo hợp đồng.');
            }

        } catch (\Exception $e) {
            $this->json_response(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
        }
    }

    // Xử lý cập nhật hợp đồng
    public function update()
    {
        try {
            $maHD = $_POST['MaHopDong'];
            $data = [
                'ngaybd' => $_POST['NgayBatDau'],
                'ngaykt' => $_POST['NgayKetThuc']
            ];

            if (empty($maHD) || empty($data['ngaybd']) || empty($data['ngaykt'])) {
                throw new \Exception('Vui lòng nhập đủ ngày bắt đầu và kết thúc.');
            }
            if ($data['ngaykt'] <= $data['ngaybd']) {
                throw new \Exception('Ngày kết thúc phải sau ngày bắt đầu.');
            }

            $result = $this->model->update($maHD, $data);

            if ($result) {
                $this->json_response([
                    'success' => true,
                    'message' => 'Cập nhật hợp đồng thành công! Trang sẽ được tải lại.'
                ]);
            } else {
                throw new \Exception('Lỗi CSDL khi cập nhật.');
            }

        } catch (\Exception $e) {
            $this->json_response(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
        }
    }

    // Xử lý xóa hợp đồng
    public function delete($id)
    {
        try {
            $this->model->delete($id);
            $this->json_response(['success' => true, 'message' => 'Xóa hợp đồng thành công!']);

        } catch (\PDOException $e) {
            // Xử lý lỗi khóa ngoại (nếu HĐ có hóa đơn)
            if ($e->getCode() == 23000) {
                $this->json_response([
                    'success' => false,
                    'message' => 'Không thể xóa (Hợp đồng đã có Hóa đơn/Dịch vụ liên quan).'
                ]);
            } else {
                $this->json_response([
                    'success' => false,
                    'message' => 'Lỗi CSDL: ' . $e->getMessage()
                ]);
            }
        }
    }
    public function checkTrangThaiHopDong($maSV)
    {
        try {
            if (empty($maSV)) {
                throw new \Exception('Vui lòng nhập Mã Sinh viên.');
            }

            // Gọi phương thức model 'checkSinhVienQuaHan'
            // (Phương thức này đã có trong HopDong.php)
            $tinhTrang = $this->model->checkSinhVienQuaHan($maSV);

            if ($tinhTrang == 0) {
                // 0 = Còn hạn
                $message = "Sinh viên [{$maSV}] CÒN HẠN hợp đồng.";
            } else {
                // 1 = Hết hạn (hoặc không có HĐ)
                $message = "Sinh viên [{$maSV}] ĐÃ HẾT HẠN hoặc KHÔNG CÓ hợp đồng.";
            }

            // Gọi hàm json_response đã có sẵn trong file này
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