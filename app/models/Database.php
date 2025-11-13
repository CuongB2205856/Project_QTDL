<?php

namespace App\Models; 

class Database
{
    private $dbh;
    private $stmt;
    
    public function __construct(array $config)
    {
        [
            'dbhost' => $dbhost,
            'dbname' => $dbname,
            'dbuser' => $dbuser,
            'dbpass' => $dbpass
        ] = $config;

        $dsn = "mysql:host={$dbhost};dbname={$dbname};charset=utf8mb4";

        try {
            $this->dbh = new \PDO($dsn, $dbuser, $dbpass, [
                \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC, 
                \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
            ]);
        } catch (\PDOException $e) {
            die("Lỗi kết nối CSDL: " . $e->getMessage());
        }
    }
    
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
                case is_int($value): $type = \PDO::PARAM_INT; break;
                case is_bool($value): $type = \PDO::PARAM_BOOL; break;
                case is_null($value): $type = \PDO::PARAM_NULL; break;
                default: $type = \PDO::PARAM_STR;
            }
        }
        $this->stmt->bindValue($param, $value, $type);
    }

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