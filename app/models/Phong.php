<?php
// models/Phong.php

use PDO;

class Phong
{
    public $MaPhong;
    public $MaLoaiPhong;
    public $SoPhong;
    public $SoLuongToiDa;
    public $TinhTrangPhong;
    
    protected $db; 

    public function __construct(PDO $pdo)
    {
        $this->db = $pdo;
    }
}
?>