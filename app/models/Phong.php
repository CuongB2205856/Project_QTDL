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
    public function all()
    {
        // Truy vấn này lấy cả thông tin loại phòng
        return $this->db->query("
            SELECT p.*, lp.TenLoaiPhong 
            FROM Phong p
            JOIN LoaiPhong lp ON p.MaLoaiPhong = lp.MaLoaiPhong
            ORDER BY p.SoPhong ASC
        ")->fetchAll(\PDO::FETCH_OBJ); // Dùng FETCH_OBJ như HopDong/Create.php
    }

    // *** THÊM HÀM MỚI CHO DASHBOARD ***
    public function count() {
        $stmt = $this->db->query("SELECT COUNT(MaPhong) as total FROM Phong");
        return $stmt->fetchColumn();
    }
}
?>