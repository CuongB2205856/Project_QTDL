<?php
// models/HoaDon.php

use PDO;

class HoaDon
{
    public $MaHoaDon;
    public $MaSDDV;
    public $NgayLapHoaDon;
    public $NgayHetHan;
    public $TongTienThanhToan;
    
    protected $db; 

    public function __construct(PDO $pdo)
    {
        $this->db = $pdo;
    }
}
?>