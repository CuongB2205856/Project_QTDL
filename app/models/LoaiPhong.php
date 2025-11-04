<?php
// models/LoaiPhong.php

use PDO;

class LoaiPhong
{
    public $MaLoaiPhong;
    public $TenLoaiPhong;
    public $GiaThue;
    
    protected $db; // Đối tượng PDO

    public function __construct(PDO $pdo)
    {
        $this->db = $pdo;
    }
}
?>