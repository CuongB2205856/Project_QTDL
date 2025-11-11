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