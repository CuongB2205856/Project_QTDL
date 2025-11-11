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

    // Các hàm CRUD và Xác thực (login(), register(), findByUsername(),...) sẽ được thêm sau
}
?>