<?php
// models/SuDungDichVu.php

use PDO;

class SuDungDichVu
{
    public $MaSDDV;
    public $MaHD;
    public $MaDV;
    public $SoLuongSuDung;
    public $ThangSuDungDV;
    public $NamSuDungDV;
    
    protected $db; 

    public function __construct(PDO $pdo)
    {
        $this->db = $pdo;
    }
}
?>