<?php
// models/User.php
namespace App\Models;

class Users
{
    // Thuộc tính Entity Object
    public $UserID;
    public $Username;
    public $Password;
    public $Role;
    public $MaLienKet;

    protected $db; // Đối tượng PDO

    public function __construct(\PDO $pdo)
    {
        $this->db = $pdo;
    }
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
    public function all()
    {
        // Sử dụng tên cột UserID, Username, Role
        $stmt = $this->db->query('SELECT UserID, Username, Role FROM Users');
        return $stmt->fetchAll();
    }

    /**
     * Tạo người dùng mới (SỬA LẠI TÊN BẢNG VÀ CỘT)
     */
    public function create(array $data)
    {
        // Băm mật khẩu trước khi lưu
        $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);

        // Sử dụng tên bảng Users và các cột Username, Password, Role
        $sql = "INSERT INTO Users (Username, Password, Role) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            $data['username'],
            $hashedPassword,
            $data['role'] // 'Quản lý'
        ]);
    }

    /**
     * Xóa người dùng bằng ID (SỬA LẠI TÊN BẢNG VÀ CỘT)
     */
    public function delete($id)
    {
        // Sử dụng tên bảng Users và cột UserID
        $sql = "DELETE FROM Users WHERE UserID = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
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
    // Các hàm CRUD và Xác thực (login(), register(), findByUsername(),...) sẽ được thêm sau
}
?>