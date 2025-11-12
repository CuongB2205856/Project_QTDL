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
     * SỬA LẠI: Tạo người dùng mới và trả về ID
     */
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
    /**
     * MỚI: Cập nhật thông tin User (không bao gồm mật khẩu)
     */
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
    public function find($id)
    {
        // Không lấy mật khẩu
        $stmt = $this->db->prepare("SELECT UserID, Username, Role, MaLienKet FROM Users WHERE UserID = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    /**
     * Xóa người dùng bằng ID (SỬA LẠI TÊN BẢNG VÀ CỘT)
     */
    public function delete($id)
    {
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