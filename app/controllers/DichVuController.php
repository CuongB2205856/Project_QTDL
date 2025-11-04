<?php
// app/controllers/dichvu.controller.php
namespace App\Controllers;
// Nhúng Controller cơ sở
require_once __DIR__ . '/Controller.php';
// Nhúng Model
use App\Models\DichVu;

class DichVuController extends Controller {
    private $model;

    public function __construct(\PDO $pdo) {
        parent::__construct();
        $this->model = new DichVu($pdo);
    }

    public function create() {
        $data = [];
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            try {
                $result = $this->model->create($_POST);
                if ($result) {
                    $data['success'] = "Thêm dịch vụ thành công!";
                } else {
                    $data['error'] = "Lỗi khi thêm dịch vụ.";
                }
            } catch (Exception $e) {
                $data['error'] = "Lỗi CSDL: " . $e->getMessage();
            }
        }
        $data['dv_list'] = $this->model->all(); 
        
        // SỬ DỤNG: $this->loadView()
        $this->loadView('dichvu/create', $data);
    }
}
?>