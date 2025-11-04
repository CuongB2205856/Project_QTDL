<?php
// app/controllers/loaiphong.controller.php
namespace App\Controllers;
// Nhúng Controller cơ sở
require_once __DIR__ . '/Controller.php';
use App\Models\LoaiPhong; 

class LoaiPhongController extends Controller {
    private $model;

    public function __construct(\PDO $pdo) {
        parent::__construct(); // Gọi constructor của Controller cơ sở
        $this->model = new LoaiPhong($pdo);
    }

    public function create() {
        $data = [];
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            try {
                $result = $this->model->create($_POST);
                if ($result) {
                    $data['success'] = "Thêm loại phòng thành công!";
                } else {
                    $data['error'] = "Lỗi khi thêm loại phòng.";
                }
            } catch (Exception $e) {
                $data['error'] = "Lỗi CSDL: " . $e->getMessage();
            }
        }
        $data['loai_phong_list'] = $this->model->all(); 
        
        // SỬ DỤNG: $this->loadView()
        $this->loadView('LoaiPhong\Create', $data); 
    }
}
?>