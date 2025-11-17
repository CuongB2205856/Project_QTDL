<?php

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

    // Hàm thêm hợp đồng
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

    // Hàm lấy số lượng hiện tại
    public function countCurrentOccupants($maPhong)
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(MaHD) 
            FROM HopDong 
            WHERE MaPhong = :maphong AND NgayKetThuc >= CURDATE()
        ");
        $stmt->execute(['maphong' => $maPhong]);
        return $stmt->fetchColumn();
    }

    // Hàm lấy tất cả hợp đồng
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
                hd.MaHD 
        ");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // Hàm lấy chi tiết hợp đồng
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

    // Hàm cập nhật hợp đồng
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

    // Hàm xóa hợp đồng
    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM HopDong WHERE MaHD = :id");
        return $stmt->execute(['id' => $id]);
    }
    public function allActiveSimple()
    {
        $stmt = $this->db->query("
            SELECT 
                hd.MaHD, 
                sv.HoTen,
                p.SoPhong
            FROM 
                HopDong hd
            JOIN 
                SinhVien sv ON hd.MaSV = sv.MaSV
            JOIN 
                Phong p ON hd.MaPhong = p.MaPhong
            WHERE 
                hd.NgayKetThuc >= CURDATE()
            ORDER BY 
                p.SoPhong, sv.HoTen
        ");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    public function checkSinhVienQuaHan($maSV)
    {
        // Tên hàm 'fn_KiemTraHopDongQuaHan' phải khớp với tên bạn tạo trong CSDL
        $stmt = $this->db->prepare("SELECT fn_KiemTraHopDongQuaHan(:masv) AS TinhTrang");
        $stmt->execute(['masv' => $maSV]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        // Trả về kết quả (1 hoặc 0) từ CSDL
        return (int)$result['TinhTrang'];
    }
}
?>