<?php

namespace App\Models;

class Users
{
    public $UserID;
    public $Username;
    public $Password;
    public $Role;
    public $MaLienKet;

    protected $db;

    public function __construct(\PDO $pdo)
    {
        $this->db = $pdo;
    }

    // Hàm đổi mật khẩu
    public function resetPassword($username, $newPassword)
    {
        // Luôn hash mật khẩu trước khi lưu
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        $stmt = $this->db->prepare("
            UPDATE Users 
            SET Password = :password 
            WHERE Username = :username
        ");

        return $stmt->execute([
            'password' => $hashedPassword,
            'username' => $username
        ]);
    }

    // Hàm lấy danh sách người dùng
    public function all()
    {
        // Sử dụng tên cột UserID, Username, Role
        $stmt = $this->db->query('SELECT UserID, Username, Role, MaLienKet FROM Users ORDER BY UserID');
        return $stmt->fetchAll();
    }


    // Hàm thêm mới người dùng
    public function create(array $data)
    {
        // Băm mật khẩu trước khi lưu
        $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);

        $sql = "INSERT INTO Users (Username, Password, Role, MaLienKet) VALUES (:username, :password, :role, :malienket)";
        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            'username' => $data['username'],
            'password' => $hashedPassword,
            'role' => $data['role'],
            'malienket' => $data['malienket'] // Có thể là null
        ]);

        // Trả về ID của user vừa tạo
        return $this->db->lastInsertId();
    }

    // Hàm cập nhật thông tin người dùng
    public function update($id, array $data)
    {
        $sql = "UPDATE Users SET Username = :username, Role = :role, MaLienKet = :malienket WHERE UserID = :id";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'username' => $data['username'],
            'role' => $data['role'],
            'malienket' => $data['malienket'],
            'id' => $id
        ]);
    }

    // Hàm lấy chi tiết người dùng
    public function find($id)
    {
        // Không lấy mật khẩu
        $stmt = $this->db->prepare("SELECT UserID, Username, Role, MaLienKet FROM Users WHERE UserID = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    // Hàm xóa người dùng
    public function delete($id)
    {
        $sql = "DELETE FROM Users WHERE UserID = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    // Hàm đổi mật khẩu
    public function changePassword($username, $oldPassword, $newPassword)
    {
        // 1. Lấy mật khẩu hash hiện tại
        $stmt = $this->db->prepare("SELECT Password FROM Users WHERE Username = :username");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();

        if (!$user) {
            return ['success' => false, 'message' => 'Không tìm thấy tài khoản.'];
        }

        // 2. Xác thực mật khẩu cũ
        if (!password_verify($oldPassword, $user['Password'])) {
            return ['success' => false, 'message' => 'Mật khẩu cũ không chính xác.'];
        }

        // 3. Cập nhật mật khẩu mới
        $newHashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $updateStmt = $this->db->prepare("
            UPDATE Users 
            SET Password = :password 
            WHERE Username = :username
        ");

        $result = $updateStmt->execute([
            'password' => $newHashedPassword,
            'username' => $username
        ]);

        if ($result) {
            return ['success' => true, 'message' => 'Đổi mật khẩu thành công.'];
        } else {
            return ['success' => false, 'message' => 'Lỗi khi cập nhật mật khẩu.'];
        }
    }

    // Hàm tìm kiếm mã liên kết
    public function findByMaLienKet($maLienKet)
    {
        $stmt = $this->db->prepare("SELECT Username FROM Users WHERE MaLienKet = :ma");
        $stmt->execute(['ma' => $maLienKet]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    // Hàm tìm kiếm người dùng theo tên đăng nhập
    public function findByUsername(string $username)
    {
        $stmt = $this->db->prepare("SELECT * FROM Users WHERE Username = :username");
        $stmt->execute(['username' => $username]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    // Hàm đăng nhập
    public function attemptLogin(string $username, string $password)
    {
        $user = $this->findByUsername($username);

        if (!$user) {
            return ['success' => false, 'message' => 'Tên đăng nhập không tồn tại.'];
        }

        // Kiểm tra mật khẩu
        if (password_verify($password, $user['Password'])) {
            // Đăng nhập thành công, loại bỏ mật khẩu hash trước khi trả về
            unset($user['Password']);
            return ['success' => true, 'user' => $user];
        } else {
            return ['success' => false, 'message' => 'Mật khẩu không chính xác.'];
        }
    }
}
?>