<?php
// models/Phong.php

namespace App\Models; 
class Phong
{
    public $MaPhong;
    public $MaLoaiPhong;
    public $SoPhong;
    public $SoLuongToiDa;
    public $TinhTrangPhong;

    protected $db;

    public function __construct(\PDO $pdo)
    {
        $this->db = $pdo;
    }

    // CREATE: Thêm phòng mới
    public function create(array $data)
    {
        $stmt = $this->db->prepare("INSERT INTO Phong (MaLoaiPhong, SoPhong, SoLuongToiDa, TinhTrangPhong) 
                                 VALUES (:maloai, :sophong, :slmax, 'Trống')");
        return $stmt->execute([
            'maloai' => $data['maloai'],
            'sophong' => $data['sophong'],
            'slmax' => $data['slmax']
        ]);
    }
}
?>