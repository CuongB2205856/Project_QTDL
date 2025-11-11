<?php
// models/SinhVien.php
namespace App\Models;

class SinhVien
{
    public $MaSV;
    public $HoTen;
    public $GioiTinh;
    public $SoDienThoai;

    protected $db;

    public function __construct(\PDO $pdo)
    {
        $this->db = $pdo;
    }
    // READ: Lấy tất cả SV (kèm thông tin phòng và placeholder Tình trạng)
    public function all()
    {
        $stmt = $this->db->query("
            SELECT 
                sv.*, 
                p.SoPhong,

                -- YÊU CẦU CỦA BẠN:
                -- Đây là cột placeholder. 
                -- Sau này, bạn sẽ thay thế 'Chưa rõ' 
                -- bằng Function MySQL của bạn, ví dụ:
                -- f_TinhTrangTreTien(sv.MaSV) AS TinhTrangDongTien
                'Chưa rõ' AS TinhTrangDongTien

            FROM 
                SinhVien sv
            LEFT JOIN HopDong hd ON sv.MaSV = hd.MaSV AND hd.NgayKetThuc >= CURDATE()
            LEFT JOIN Phong p ON hd.MaPhong = p.MaPhong
            ORDER BY 
                sv.HoTen
        ");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    // READ: Tìm SV theo Mã SV (cần thiết để kiểm tra)
    public function findById($maSV)
    {
        $stmt = $this->db->prepare("SELECT * FROM SinhVien WHERE MaSV = :masv");
        $stmt->execute(['masv' => $maSV]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }
    // READ: Lấy chi tiết phòng SV đang ở (cho modal chi tiết)
    public function findDetails($maSV)
    {
        $stmt = $this->db->prepare("
            SELECT 
                sv.MaSV, sv.HoTen,
                p.SoPhong, lp.TenLoaiPhong, lp.GiaThue,
                hd.NgayBatDau, hd.NgayKetThuc
            FROM 
                SinhVien sv
            JOIN HopDong hd ON sv.MaSV = hd.MaSV
            JOIN Phong p ON hd.MaPhong = p.MaPhong
            JOIN LoaiPhong lp ON p.MaLoaiPhong = lp.MaLoaiPhong
            WHERE 
                sv.MaSV = :masv AND hd.NgayKetThuc >= CURDATE()
            LIMIT 1
        ");
        $stmt->execute(['masv' => $maSV]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    // CREATE: Thêm sinh viên mới
    public function create(array $data)
    {
        $stmt = $this->db->prepare("INSERT INTO SinhVien (MaSV, HoTen, GioiTinh, SoDienThoai) 
                                 VALUES (:masv, :hoten, :gioitinh, :sdt)");
        return $stmt->execute([
            'masv' => $data['masv'],
            'hoten' => $data['hoten'],
            'gioitinh' => $data['gioitinh'] ?? null,
            'sdt' => $data['sdt'] ?? null
        ]);
    }
    public function update($maSV, array $data)
    {
        $stmt = $this->db->prepare("
            UPDATE SinhVien 
            SET HoTen = :hoten, GioiTinh = :gioitinh, SoDienThoai = :sdt
            WHERE MaSV = :masv
        ");
        return $stmt->execute([
            'hoten' => $data['hoten'],
            'gioitinh' => $data['gioitinh'] ?? null,
            'sdt' => $data['sdt'] ?? null,
            'masv' => $maSV
        ]);
    }

    /**
     * DELETE: Xóa SV
     */
    public function delete($maSV)
    {
        $stmt = $this->db->prepare("DELETE FROM SinhVien WHERE MaSV = :masv");
        return $stmt->execute(['masv' => $maSV]);
    }
    public function count()
    {
        $stmt = $this->db->query("SELECT COUNT(MaSV) as total FROM SinhVien");
        return $stmt->fetchColumn();
    }
    public function getStudentDashboardDetails($maSV)
    {
        $stmt = $this->db->prepare("
            SELECT 
                sv.HoTen, sv.MaSV,
                p.SoPhong, 
                lp.TenLoaiPhong,
                lp.GiaThue AS GiaTienThuePhong, 

                h.TongTienThanhToan AS GiaTienPhaiDong,
                h.NgayHetHan AS NgayDenHanDongTien

            FROM 
                SinhVien sv
            
            LEFT JOIN HopDong hd ON sv.MaSV = hd.MaSV AND hd.NgayKetThuc >= CURDATE()
            LEFT JOIN Phong p ON hd.MaPhong = p.MaPhong
            LEFT JOIN LoaiPhong lp ON p.MaLoaiPhong = lp.MaLoaiPhong
            
            -- SỬA LỖI JOIN TẠI ĐÂY --
            LEFT JOIN SuDungDichVu sddv ON hd.MaHD = sddv.MaHD
            LEFT JOIN HoaDon h ON sddv.MaSDDV = h.MaSDDV AND h.TrangThaiThanhToan = 'Chưa thanh toán'
            -- KẾT THÚC SỬA LỖI --
            
            WHERE 
                sv.MaSV = :masv
                
            ORDER BY 
                h.NgayHetHan ASC 
            LIMIT 1
        ");

        $stmt->execute(['masv' => $maSV]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    public function allWithoutContract()
    {
        $stmt = $this->db->query("
            SELECT * FROM SinhVien 
            WHERE MaSV NOT IN (
                SELECT MaSV FROM HopDong WHERE NgayKetThuc >= CURDATE()
            )
            ORDER BY HoTen
        ");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * MỚI: Kiểm tra 1 SV cụ thể có hợp đồng CÒN HẠN hay không
     * (Để validate khi POST form)
     */
    public function checkActiveContract($maSV)
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(MaHD) 
            FROM HopDong 
            WHERE MaSV = :masv AND NgayKetThuc >= CURDATE()
        ");
        $stmt->execute(['masv' => $maSV]);
        return $stmt->fetchColumn();
    }
}
?>