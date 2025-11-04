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
    
    // Các hàm CRUD và Xác thực (login(), register(), findByUsername(),...) sẽ được thêm sau
}
?>