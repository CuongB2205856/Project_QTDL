<?php
namespace App\Controllers;

// Nhúng các Models cần thiết
use App\Models\Users;

class UsersController extends Controller
{

    private $model;

    public function __construct(\PDO $pdo)
    {
        parent::__construct();
        $this->model = new Users($pdo);

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

    // Hiển thị danh sách người dùng
    public function index()
    {
        $data = [
            'users_list' => $this->model->all()
        ];
        $this->loadView('Users/index', $data);
    }

    // Lấy chi tiết người dùng
    public function ajax_get_details($id)
    {
        $data = $this->model->find($id);
        if ($data) {
            $this->json_response(['success' => true, 'data' => $data]);
        } else {
            $this->json_response(['success' => false, 'message' => 'Không tìm thấy người dùng.']);
        }
    }

    // Xử lý tạo người dùng
    public function ajax_create()
    {
        try {
            if (empty($_POST['username']) || empty($_POST['password']) || empty($_POST['role'])) {
                throw new \Exception('Vui lòng nhập Tên đăng nhập, Mật khẩu và Quyền.');
            }
            // Xử lý MaLienKet
            $malienket = ($_POST['role'] === 'SinhVien') ? ($_POST['malienket'] ?? null) : null;

            // (Nếu role là SinhVien, MaLienKet là bắt buộc)
            if ($_POST['role'] === 'SinhVien' && empty($malienket)) {
                throw new \Exception('Vui lòng nhập Mã liên kết (Mã SV) cho tài khoản Sinh Viên.');
            }

            $data = [
                'username' => $_POST['username'],
                'password' => $_POST['password'],
                'role' => $_POST['role'],
                'malienket' => $malienket
            ];

            $newId = $this->model->create($data);

            if ($newId) {
                $newRow = $this->model->find($newId);
                $this->json_response([
                    'success' => true,
                    'message' => 'Tạo người dùng thành công!',
                    'newRow' => $newRow
                ]);
            } else {
                throw new \Exception('Lỗi CSDL khi tạo người dùng.');
            }

        } catch (\PDOException $e) {
            // Lỗi trùng lặp (UNIQUE)
            if ($e->getCode() == 23000) {
                if (strpos($e->getMessage(), 'Username')) {
                    $this->json_response(['success' => false, 'message' => 'Lỗi: Tên đăng nhập này đã tồn tại.']);
                } elseif (strpos($e->getMessage(), 'MaLienKet')) {
                    $this->json_response(['success' => false, 'message' => 'Lỗi: Mã liên kết (Mã SV) này đã được sử dụng.']);
                } else {
                    $this->json_response(['success' => false, 'message' => 'Lỗi: Dữ liệu bị trùng lặp.']);
                }
            } else {
                $this->json_response(['success' => false, 'message' => 'Lỗi CSDL: ' . $e->getMessage()]);
            }
        } catch (\Exception $e) {
            $this->json_response(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
        }
    }

    // Xử lý cập nhật người dùng
    public function ajax_update($id)
    {
        try {
            if (empty($_POST['username']) || empty($_POST['role'])) {
                throw new \Exception('Vui lòng nhập Tên đăng nhập và Quyền.');
            }

            // Xử lý MaLienKet
            $malienket = ($_POST['role'] === 'SinhVien') ? ($_POST['malienket'] ?? null) : null;

            if ($_POST['role'] === 'SinhVien' && empty($malienket)) {
                throw new \Exception('Vui lòng nhập Mã liên kết (Mã SV) cho tài khoản Sinh Viên.');
            }

            $data = [
                'username' => $_POST['username'],
                'role' => $_POST['role'],
                'malienket' => $malienket
            ];

            $result = $this->model->update($id, $data);

            if ($result) {
                $updatedRow = $this->model->find($id);
                $this->json_response([
                    'success' => true,
                    'message' => 'Cập nhật thành công!',
                    'updatedRow' => $updatedRow
                ]);
            } else {
                throw new \Exception('Lỗi CSDL khi cập nhật.');
            }

        } catch (\PDOException $e) {
            // Lỗi trùng lặp (UNIQUE)
            if ($e->getCode() == 23000) {
                // Xử lý tương tự create
                $this->json_response(['success' => false, 'message' => 'Lỗi: Tên đăng nhập hoặc Mã liên kết bị trùng.']);
            } else {
                $this->json_response(['success' => false, 'message' => 'Lỗi CSDL: ' . $e->getMessage()]);
            }
        } catch (\Exception $e) {
            $this->json_response(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
        }
    }


    // Xử lý xóa người dùng
    public function ajax_delete($id)
    {
        try {
            $result = $this->model->delete($id);

            if ($result) {
                $this->json_response(['success' => true, 'message' => 'Xóa người dùng thành công!']);
            } else {
                throw new \Exception('Lỗi CSDL khi xóa người dùng.');
            }

        } catch (\PDOException $e) {
            // (Bắt lỗi khóa ngoại nếu không xóa được)
            if ($e->getCode() == 23000) {
                $this->json_response(['success' => false, 'message' => 'Lỗi: Không thể xóa người dùng này (đã có dữ liệu liên quan).']);
            } else {
                $this->json_response(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
            }
        }
    }

    // Reset mật khẩu người dùng
    public function ajax_reset_password($id)
    {
        try {
            // 1. Lấy thông tin user
            $user = $this->model->find($id);
            if (!$user) {
                throw new \Exception('Không tìm thấy tài khoản để reset.');
            }

            // Gọi hàm resetPassword
            $defaultPassword = '123456';
            $result = $this->model->resetPassword($user['Username'], $defaultPassword);

            if ($result) {
                $this->json_response(['success' => true, 'message' => "Đặt lại mật khẩu cho '{$user['Username']}' thành công (MK mặc định là 123456)."]);
            } else {
                $this->json_response(['success' => false, 'message' => 'Lỗi khi cập nhật mật khẩu trong CSDL.']);
            }
        } catch (\Exception $e) {
            $this->json_response(['success' => false, 'message' => 'Lỗi CSDL: ' . $e->getMessage()]);
        }
    }
    
}
?>