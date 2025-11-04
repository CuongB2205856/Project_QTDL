<?php
// app/controllers/phong.controller.php
namespace App\Controllers;
// Nhúng Controller cơ sở
require_once __DIR__ . '/Controller.php';

use App\Models\Phong;
use App\Models\LoaiPhong;
class PhongController extends Controller {
    private $model;
    private $loaiPhongModel;

    public function __construct(\PDO $pdo) {
        parent::__construct(); // -> Dòng này tải config.php và định nghĩa ROOT_PATH

        $this->model = new Phong($pdo);
        $this->loaiPhongModel = new LoaiPhong($pdo);
    }

    public function create() {
        $data = [];
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!empty($_POST['sophong']) && !empty($_POST['slmax'])) {
                try {
                    $result = $this->model->create($_POST);
                    if ($result) {
                        $data['success'] = "Thêm phòng " . $_POST['sophong'] . " thành công!";
                    } else {
                        $data['error'] = "Lỗi khi thêm phòng mới.";
                    }
                } catch (Exception $e) {
                    $data['error'] = "Lỗi CSDL: " . $e->getMessage();
                }
            } else {
                $data['error'] = "Vui lòng điền đầy đủ thông tin.";
            }
        }
        $data['loai_phong_list'] = $this->loaiPhongModel->all(); 
        
        // SỬ DỤNG: $this->loadView()
        $this->loadView('Phong\Create', $data);
    }
}
?>