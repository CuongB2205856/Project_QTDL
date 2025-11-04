<?php
// models/HopDong.php

use PDO;

class HopDong
{
    public $MaHD;
    public $MaSV;
    public $MaPhong;
    public $NgayBatDau;
    public $NgayKetThuc;
    
    protected $db; 

    public function __construct(PDO $pdo)
    {
        $this->db = $pdo;
    }
}
?>