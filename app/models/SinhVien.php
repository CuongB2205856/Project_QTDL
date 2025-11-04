<?php
// models/SinhVien.php

use PDO;

class SinhVien
{
    public $MaSV;
    public $HoTen;
    public $GioiTinh;
    public $SoDienThoai; 
    
    protected $db; 

    public function __construct(PDO $pdo)
    {
        $this->db = $pdo;
    }
}
?>