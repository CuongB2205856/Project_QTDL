<?php
// app/controllers/UsersController.php
namespace App\Controllers;

// Sử dụng Model
use App\Models\Users;

class UsersController extends Controller {
    
    private $model;

    public function __construct(\PDO $pdo) {
        parent::__construct(); 
        $this->model = new Users($pdo);
        
        // Đảm bảo session đã được khởi tạo
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Hiển thị trang quản lý (GET) và xử lý tạo người dùng (POST)
     */
    public function index() {
        // Xử lý khi người dùng SUBMIT FORM (POST)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $username = $_POST['username'];
                $password = $_POST['password'];
                $role = 'Quản lý'; // Gán cứng vai trò theo yêu cầu

                if (empty($username) || empty($password)) {
                    throw new \Exception('Tên đăng nhập và mật khẩu không được để trống.');
                }

                // (Bạn nên thêm logic kiểm tra tên đăng nhập đã tồn tại chưa)
                
                $result = $this->model->create([
                    'username' => $username,
                    'password' => $password,
                    'role' => $role
                ]);

                if ($result) {
                    $_SESSION['message'] = "Tạo người dùng '$username' thành công!";
                    $_SESSION['message_type'] = 'success';
                } else {
                    throw new \Exception('Lỗi CSDL khi tạo người dùng.');
                }

            } catch (\Exception $e) {
                $_SESSION['message'] = 'Lỗi: ' . $e->getMessage();
                $_SESSION['message_type'] = 'danger';
            }
            
            // Redirect về chính trang index (để tránh F5 submit lại)
            header('Location: ' . url('users'));
            exit;
        }

        // Xử lý khi vào trang (GET)
        $data = [
            'users_list' => $this->model->all(),
            'message' => $_SESSION['message'] ?? null,
            'message_type' => $_SESSION['message_type'] ?? 'info'
        ];
        
        // Xóa session message sau khi đọc
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
        
        $this->loadView('Users/index', $data);
    }

    /**
     * Xử lý xóa người dùng (POST)
     */
    public function delete($id) {
        try {
            // (Bạn có thể muốn thêm kiểm tra, ví dụ: không cho xóa chính mình)
            
            $result = $this->model->delete($id);

            if ($result) {
                $_SESSION['message'] = "Xóa người dùng thành công!";
                $_SESSION['message_type'] = 'success';
            } else {
                throw new \Exception('Lỗi CSDL khi xóa người dùng.');
            }

        } catch (\Exception $e) {
            // (Bắt lỗi khóa ngoại nếu không xóa được)
            if (strpos($e->getMessage(), 'foreign key')) {
                 $_SESSION['message'] = 'Lỗi: Không thể xóa người dùng này vì có dữ liệu liên quan (hợp đồng, hóa đơn...).';
            } else {
                 $_SESSION['message'] = 'Lỗi: ' . $e->getMessage();
            }
            $_SESSION['message_type'] = 'danger';
        }
        
        // Redirect về trang danh sách
        header('Location: ' . url('users'));
        exit;
    }
}
?>