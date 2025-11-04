<?php
// models/DichVu.php

use PDO;

class DichVu
{
    public $MaDV;
    public $TenDichVu;
    public $DonGiaDichVu;
    
    protected $db; 

    public function __construct(PDO $pdo)
    {
        $this->db = $pdo;
    }
}
?>