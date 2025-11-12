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
    // --- SỬA LẠI HÀM CREATE (Thêm NgayLap) ---
    public function create(array $data)
    {
        // Giả sử DB của bạn có cột NgayLap
        $stmt = $this->db->prepare("INSERT INTO HopDong (MaSV, MaPhong, NgayBatDau, NgayKetThuc) 
                                 VALUES (:masv, :maphong, :ngaybd, :ngaykt)");
        return $stmt->execute([
            'masv' => $data['masv'],
            'maphong' => $data['maphong'],
            'ngaybd' => $data['ngaybd'],
            'ngaykt' => $data['ngaykt']
        ]);
    }
    public function countCurrentOccupants($maPhong) {
        $stmt = $this->db->prepare("
            SELECT COUNT(MaHD) 
            FROM HopDong 
            WHERE MaPhong = :maphong AND NgayKetThuc >= CURDATE()
        ");
        $stmt->execute(['maphong' => $maPhong]);
        return $stmt->fetchColumn();
    }
    // --- THÊM CÁC HÀM MỚI BÊN DƯỚI ---

    /**
     * MỚI: Lấy tất cả hợp đồng (cho trang index)
     */
    public function all()
    {
        $stmt = $this->db->query("
            SELECT 
                hd.MaHD AS MaHopDong, 
                hd.NgayBatDau, 
                hd.NgayKetThuc,
                sv.HoTen AS TenSinhVien,
                p.SoPhong,
                CASE 
                    WHEN hd.NgayKetThuc >= CURDATE() THEN 'Active'
                    ELSE 'Expired' 
                END AS TrangThai
            FROM 
                HopDong hd
            JOIN 
                SinhVien sv ON hd.MaSV = sv.MaSV
            JOIN 
                Phong p ON hd.MaPhong = p.MaPhong
            ORDER BY 
                hd.MaHD DESC
        ");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * MỚI: Lấy chi tiết 1 HĐ (cho modal Sửa)
     */
    public function findDetails($id)
    {
        $stmt = $this->db->prepare("
            SELECT 
                MaHD AS MaHopDong, 
                MaSV, 
                MaPhong, 
                NgayBatDau, 
                NgayKetThuc,
                CASE 
                    WHEN NgayKetThuc >= CURDATE() THEN 'Active'
                    ELSE 'Expired' 
                END AS TrangThai
            FROM 
                HopDong
            WHERE 
                MaHD = :id
        ");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * MỚI: Cập nhật HĐ (chỉ ngày tháng)
     */
    public function update($id, array $data)
    {
        $stmt = $this->db->prepare("
            UPDATE HopDong 
            SET NgayBatDau = :ngaybd, 
                NgayKetThuc = :ngaykt
            WHERE MaHD = :id
        ");
        return $stmt->execute([
            'ngaybd' => $data['ngaybd'],
            'ngaykt' => $data['ngaykt'],
            'id' => $id
        ]);
    }

    /**
     * MỚI: Xóa HĐ
     */
    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM HopDong WHERE MaHD = :id");
        return $stmt->execute(['id' => $id]);
    }
}
?>