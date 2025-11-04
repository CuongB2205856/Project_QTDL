<?php
// app/controllers/hopdong.controller.php
namespace App\Controllers;
// Nhúng Controller cơ sở
require_once __DIR__ . '/Controller.php';

use App\Models\SinhVien;
use App\Models\Phong;
use App\Models\HopDong;

class HopDongController extends Controller {
    private $svModel;
    private $phongModel;
    private $hdModel;

    public function __construct(\PDO $pdo) {
        parent::__construct(); // Gọi constructor của Controller cơ sở
        $this->svModel = new SinhVien($pdo);
        $this->phongModel = new Phong($pdo); 
        $this->hdModel = new HopDong($pdo);
    }

    public function create() {
        $data = [];
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $postData = $_POST;
            
            // 1. Kiểm tra/Tạo Sinh viên mới
            try {
                if (!$this->svModel->findById($postData['masv'])) {
                    // Sinh viên chưa tồn tại, tạo mới
                    $this->svModel->create($postData);
                } else {
                    // Nếu đã tồn tại, có thể cập nhật thông tin (ví dụ: số điện thoại)
                    $this->svModel->update($postData['masv'], $postData);
                }
            } catch (Exception $e) {
                $data['error'] = "Lỗi khi xử lý thông tin Sinh viên: " . $e->getMessage();
                // Nếu lỗi, dừng xử lý Hợp đồng
                $data['phong_list'] = $this->phongModel->all(); 
                return $this->loadView('hopdong/create', $data);
            }
            
            // 2. Tạo Hợp đồng
            try {
                // **Lưu ý: Trigger (nếu được viết) sẽ tự động cập nhật trạng thái phòng khi thêm HĐ thành công**
                $result = $this->hdModel->create($postData);
                
                if ($result) {
                    $data['success'] = "Thêm SV " . $postData['hoten'] . " vào phòng thành công! (Mã SV: " . $postData['masv'] . ")";
                } else {
                    $data['error'] = "Lỗi khi tạo hợp đồng. Kiểm tra xem phòng có còn chỗ không.";
                }
            } catch (Exception $e) {
                // Lỗi SQL có thể xảy ra nếu phòng đầy, hoặc SV đã có HĐ còn hạn
                $data['error'] = "Lỗi CSDL: " . $e->getMessage() . " (Có thể phòng đã đầy hoặc Mã SV đã có hợp đồng thuê phòng)";
            }
        }
        
        // Lấy danh sách phòng cho dropdown (Cần hàm all() trong Phong.php)
        $data['phong_list'] = $this->phongModel->all(); 
        
        // SỬ DỤNG: $this->loadView()
        $this->loadView('HopDong\Create', $data);
    }
}
?>