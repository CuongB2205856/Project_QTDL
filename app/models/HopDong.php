<?php
// models/HopDong.php

namespace App\Models; 
class HopDong
{
    public $MaHD;
    public $MaSV;
    public $MaPhong;
    public $NgayBatDau;
    public $NgayKetThuc;
    
    protected $db; 

    public function __construct(\PDO $pdo)
    {
        $this->db = $pdo;
    }

    // CREATE: Tạo hợp đồng mới
    public function create(array $data)
    {
        $stmt = $this->db->prepare("INSERT INTO HopDong (MaSV, MaPhong, NgayBatDau, NgayKetThuc) 
                                 VALUES (:masv, :maphong, :ngaybd, :ngaykt)");
        return $stmt->execute([
            'masv' => $data['masv'],
            'maphong' => $data['maphong'],
            'ngaybd' => $data['ngaybd'],
            'ngaykt' => $data['ngaykt']
        ]);
    }
}
?>