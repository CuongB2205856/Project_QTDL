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
    // CREATE: Sửa lại để trả về ID
    public function create(array $data)
    {
        $stmt = $this->db->prepare("INSERT INTO Phong (MaLoaiPhong, SoPhong, SoLuongToiDa, TinhTrangPhong) 
                                 VALUES (:maloai, :sophong, :slmax, 'Trống')");
        $stmt->execute([
            'maloai' => $data['maloai'],
            'sophong' => $data['sophong'],
            'slmax' => $data['slmax']
        ]);
        // Trả về ID của phòng vừa thêm
        return $this->db->lastInsertId();
    }

    // READ (All): Giữ nguyên, nhưng nên trả về FETCH_ASSOC cho nhất quán
    public function all()
    {
        return $this->db->query("
            SELECT p.*, lp.TenLoaiPhong, lp.GiaThue
            FROM Phong p
            JOIN LoaiPhong lp ON p.MaLoaiPhong = lp.MaLoaiPhong
            ORDER BY p.SoPhong ASC
        ")->fetchAll(\PDO::FETCH_ASSOC); // Đổi sang FETCH_ASSOC
    }

    // *** THÊM MỚI: READ (Find): TÌM 1 PHÒNG THEO ID ***
    public function find($id)
    {
        // Cũng join để lấy TenLoaiPhong
        $stmt = $this->db->prepare("
            SELECT p.*, lp.TenLoaiPhong, lp.GiaThue
            FROM Phong p
            JOIN LoaiPhong lp ON p.MaLoaiPhong = lp.MaLoaiPhong
            WHERE p.MaPhong = :id
        ");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    // *** THÊM MỚI: UPDATE: CẬP NHẬT PHÒNG ***
    public function update($id, array $data)
    {
        // Giả sử không cho sửa TinhTrangPhong ở đây
        $stmt = $this->db->prepare("UPDATE Phong 
                                 SET MaLoaiPhong = :maloai, 
                                     SoPhong = :sophong, 
                                     SoLuongToiDa = :slmax
                                 WHERE MaPhong = :id");
        return $stmt->execute([
            'maloai' => $data['maloai'],
            'sophong' => $data['sophong'],
            'slmax' => $data['slmax'],
            'id' => $id
        ]);
    }

    // *** THÊM MỚI: DELETE: XÓA PHÒNG ***
    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM Phong WHERE MaPhong = :id");
        return $stmt->execute(['id' => $id]);
    }

    // *** THÊM HÀM MỚI CHO DASHBOARD ***
    public function count()
    {
        $stmt = $this->db->query("SELECT COUNT(MaPhong) as total FROM Phong");
        return $stmt->fetchColumn();
    }
    // --- THÊM HÀM MỚI NÀY VÀO CUỐI FILE (TRƯỚC DẤU }) ---

    /**
     * MỚI: Lấy danh sách phòng CÒN CHỖ TRỐNG
     * (Cho modal Thêm Hợp Đồng)
     */
    public function allWithVacancy()
    {
        // Đếm số HĐ còn hạn của mỗi phòng
        $subQuery = "
            (SELECT COUNT(hd.MaHD) 
             FROM HopDong hd 
             WHERE hd.MaPhong = p.MaPhong AND hd.NgayKetThuc >= CURDATE())
        ";

        $stmt = $this->db->query("
            SELECT 
                p.*, 
                lp.TenLoaiPhong, 
                (p.SoLuongToiDa - {$subQuery}) AS SoChoTrong
            FROM 
                Phong p
            JOIN 
                LoaiPhong lp ON p.MaLoaiPhong = lp.MaLoaiPhong
            HAVING 
                SoChoTrong > 0
            ORDER BY 
                p.SoPhong ASC
        ");
        
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
?>