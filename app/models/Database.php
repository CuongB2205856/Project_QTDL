<?php
// app/models/Database.php

// Lớp này vừa đóng vai trò Factory (tạo kết nối PDO)
// vừa là Wrapper (bao bọc các phương thức truy vấn)
namespace App\Models; 
// Xóa 'use PDO;' vì nó là lớp toàn cục (global class)

class Database
{
    private $dbh; // Database Handler (Đối tượng PDO)
    private $stmt; // Statement
    
    // Nhận thông tin cấu hình qua constructor
    public function __construct(array $config)
    {
        // 1. Tích hợp logic Factory: Tạo đối tượng PDO
        [
            'dbhost' => $dbhost,
            'dbname' => $dbname,
            'dbuser' => $dbuser,
            'dbpass' => $dbpass
        ] = $config;

        $dsn = "mysql:host={$dbhost};dbname={$dbname};charset=utf8mb4";

        try {
            // SỬA LỖI: Thêm \ trước PDO và các hằng số
            $this->dbh = new \PDO($dsn, $dbuser, $dbpass, [
                \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC, // Trả về mảng kết hợp
                \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8" // Hỗ trợ Tiếng Việt
            ]);
        // SỬA LỖI: Thêm \ trước PDOException
        } catch (\PDOException $e) {
            // Hiển thị lỗi kết nối để dễ debug
            die("Lỗi kết nối CSDL: " . $e->getMessage());
        }
    }

    // 2. Các phương thức truy vấn
    
    // Chuẩn bị câu lệnh SQL
    public function query($sql)
    {
        $this->stmt = $this->dbh->prepare($sql);
    }
    
    // Gán giá trị vào Prepared Statement
    public function bind($param, $value, $type = null)
    {
        if (is_null($type)) {
            switch (true) {
                case is_int($value): $type = \PDO::PARAM_INT; break; // Thêm \ trước PDO
                case is_bool($value): $type = \PDO::PARAM_BOOL; break; // Thêm \ trước PDO
                case is_null($value): $type = \PDO::PARAM_NULL; break; // Thêm \ trước PDO
                default: $type = \PDO::PARAM_STR;
            }
        }
        $this->stmt->bindValue($param, $value, $type);
    }

    // Thực thi câu lệnh đã chuẩn bị
    public function execute()
    {
        return $this->stmt->execute();
    }

    // Lấy tất cả các dòng kết quả
    public function resultSet()
    {
        $this->execute();
        return $this->stmt->fetchAll();
    }

    // Lấy một dòng kết quả duy nhất
    public function single()
    {
        $this->execute();
        return $this->stmt->fetch();
    }
    
    // Trả về ID của bản ghi vừa được chèn
    public function lastInsertId()
    {
        return $this->dbh->lastInsertId();
    }
}
?>