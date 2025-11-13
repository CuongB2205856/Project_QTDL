<?php
// app/controllers/AuthController.php
namespace App\Controllers;

// Sử dụng Models
use App\Models\Users;
use App\Models\SinhVien;
use \PDOException;

class AuthController extends Controller
{

    private $userModel;
    private $sinhVienModel;
    private $pdo; // Cần giữ PDO instance để quản lý transaction
    
    /**
     * SỬA LỖI: Thêm thuộc tính $baseURL để sử dụng an toàn
     */
    private $baseURL;

    public function __construct(\PDO $pdo)
    {
        parent::__construct();
        $this->pdo = $pdo;
        $this->userModel = new Users($pdo);
        $this->sinhVienModel = new SinhVien($pdo);

        /**
         * SỬA LỖI: Lấy BASE_URL một cách an toàn ngay khi khởi tạo
         * Nó sẽ kiểm tra 'BASE_URL' từ config.php. Nếu không có, dùng chuỗi rỗng.
         */
        $this->baseURL = defined('BASE_URL') ? BASE_URL : '';

        // Bắt đầu session cho mọi hoạt động liên quan đến Auth
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Helper: Chuyển hướng dựa trên Role
     */
    private function redirectBasedOnRole($role, $maLienKet)
    {
        // SỬA LỖI: Sử dụng $this->baseURL đã được gán an toàn
        if ($role === 'QuanLy') {
            // ADMIN
            header('Location: ' . $this->baseURL . '/dashboard');
            exit;
        } elseif ($role === 'SinhVien' && !empty($maLienKet)) {
            // SINH VIÊN (Kèm Mã SV)
            header('Location: ' . $this->baseURL . '/student/profile/' . $maLienKet);
            exit;
        } else {
            // Trường hợp không rõ role hoặc MaSV
            header('Location: ' . $this->baseURL . '/');
            exit;
        }
    }

    // ====================================================================
    // Đăng nhập
    // ====================================================================

    /**
     * Hiển thị form đăng nhập (GET /login)
     */
    public function showLogin()
    {
        // Nếu đã đăng nhập, chuyển hướng ngay lập tức
        if (isset($_SESSION['user_id'])) {
            $this->redirectBasedOnRole($_SESSION['role'], $_SESSION['ma_lien_ket']);
        }
        // Giả định view nằm ở app/views/auth/login.php
        $this->loadView('auth/login');
    }

    /**
     * Xử lý đăng nhập (POST /login)
     */
    public function handleLogin()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
             // SỬA LỖI: Sử dụng $this->baseURL
            header('Location: ' . $this->baseURL . '/login');
            exit;
        }

        $username = strtoupper(trim($_POST['username'] ?? ''));
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            $this->loadView('auth/login', ['error' => 'Vui lòng nhập đầy đủ Mã Sinh Viên và Mật khẩu.']);
            return;
        }

        $result = $this->userModel->attemptLogin($username, $password);

        if ($result['success']) {
            $user = $result['user'];

            // Lưu thông tin user vào session
            $_SESSION['user_id'] = $user['UserID'];
            $_SESSION['username'] = $user['Username'];
            $_SESSION['role'] = $user['Role'];
            $_SESSION['ma_lien_ket'] = $user['MaLienKet']; // MaSV cho sinh viên

            // Chuyển hướng theo role
            $this->redirectBasedOnRole($user['Role'], $user['MaLienKet']);

        } else {
            $this->loadView('auth/login', ['error' => $result['message']]);
        }
    }

    // ====================================================================
    // Đăng ký Sinh Viên
    // ====================================================================

    /**
     * Hiển thị form đăng ký (GET /register)
     */
    public function showRegister()
    {
        $this->loadView('auth/register');
    }

    /**
     * Xử lý đăng ký cho Sinh Viên (POST /register)
     * Ghi vào 2 bảng: SinhVien và Users (sử dụng transaction)
     */
    public function handleRegister()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
             // SỬA LỖI: Sử dụng $this->baseURL
            header('Location: ' . $this->baseURL . '/register');
            exit;
        }

        $data = [
            'masv' => strtoupper(trim($_POST['masv'] ?? '')), // Chuyển MaSV thành chữ hoa
            'hoten' => trim($_POST['hoten'] ?? ''),
            'gioitinh' => trim($_POST['gioitinh'] ?? null),
            'sdt' => trim($_POST['sdt'] ?? null),
            'password' => $_POST['password'] ?? '',
            'password_confirm' => $_POST['password_confirm'] ?? ''
        ];
        $error = null;

        // 1. Validate cơ bản
        if (empty($data['masv']) || empty($data['hoten']) || empty($data['password']) || empty($data['password_confirm'])) {
            $error = 'Vui lòng nhập đầy đủ thông tin bắt buộc (Mã SV, Họ Tên, Mật khẩu).';
        } elseif ($data['password'] !== $data['password_confirm']) {
            $error = 'Mật khẩu xác nhận không khớp.';
        }

        if ($error) {
            $this->loadView('auth/register', ['error' => $error, 'old_data' => $data]);
            return;
        }

        try {
            // Bắt đầu Transaction
            $this->pdo->beginTransaction();

            // 2. Thêm vào bảng SinhVien
            $sinhVienData = [
                'masv' => $data['masv'],
                'hoten' => $data['hoten'],
                'gioitinh' => $data['gioitinh'],
                'sdt' => $data['sdt']
            ];
            $this->sinhVienModel->create($sinhVienData);

            // 3. Thêm vào bảng Users
            $userData = [
                'username' => $data['masv'], // GÁN MaSV làm Username
                'password' => $data['password'],
                'role' => 'SinhVien', // LUÔN LUÔN LÀ SINHVIEN
                'malienket' => $data['masv'] // Liên kết với MaSV
            ];
            $userId = $this->userModel->create($userData);

            if (!$userId) {
                throw new \Exception('Lỗi hệ thống khi tạo tài khoản User.');
            }

            // Hoàn tất Transaction
            $this->pdo->commit();

            // Đăng ký thành công, tự động đăng nhập và chuyển hướng
            $_SESSION['user_id'] = $userId;
            $_SESSION['username'] = $data['masv']; 
            $_SESSION['role'] = 'SinhVien';
            $_SESSION['ma_lien_ket'] = $data['masv'];

            $this->redirectBasedOnRole('SinhVien', $data['masv']);

        } catch (PDOException $e) {
            // Rollback nếu có lỗi CSDL
            $this->pdo->rollBack();

            if ($e->getCode() == 23000) {
                 $message = $e->getMessage();
                if (strpos($message, 'PRIMARY') || strpos($message, 'MaLienKet') || strpos($message, 'Username')) {
                    $error = 'Lỗi: Mã Sinh Viên này đã tồn tại hoặc đã được đăng ký.';
                } else {
                    $error = 'Lỗi: Dữ liệu bị trùng lặp. Vui lòng kiểm tra lại Mã SV.';
                }
            } else {
                $error = 'Lỗi CSDL: ' . $e->getMessage();
            }

            $this->loadView('auth/register', ['error' => $error, 'old_data' => $data]);

        } catch (\Exception $e) {
            $this->pdo->rollBack();
            $this->loadView('auth/register', ['error' => 'Lỗi: ' . $e->getMessage(), 'old_data' => $data]);
        }
    }

    // ====================================================================
    // Đăng xuất
    // ====================================================================

    /**
     * Xử lý đăng xuất (GET /logout)
     */
    public function logout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        session_destroy();

        // SỬA LỖI: Sử dụng $this->baseURL
        header('Location: ' . $this->baseURL . '/login');
        exit;
    }
}
?>