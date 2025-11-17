<?php

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

    // Hàm thêm mới phòng
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

    // Hàm lấy danh sách phòng

    public function all()
    {
        return $this->db->query("
            SELECT p.*, lp.TenLoaiPhong, lp.GiaThue
            FROM Phong p
            JOIN LoaiPhong lp ON p.MaLoaiPhong = lp.MaLoaiPhong
            ORDER BY p.SoPhong ASC
        ")->fetchAll(\PDO::FETCH_ASSOC); // Đổi sang FETCH_ASSOC
    }

    // Hàm lấy chi tiết phòng
    public function find($id)
    {
        $stmt = $this->db->prepare("
            SELECT p.*, lp.TenLoaiPhong, lp.GiaThue
            FROM Phong p
            JOIN LoaiPhong lp ON p.MaLoaiPhong = lp.MaLoaiPhong
            WHERE p.MaPhong = :id
        ");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    // Hàm cập nhật thông tin phòng
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

    // Hàm xóa phòng
    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM Phong WHERE MaPhong = :id");
        return $stmt->execute(['id' => $id]);
    }

    // Hàm đếm số lượng phòng
    public function count()
    {
        $stmt = $this->db->query("SELECT COUNT(MaPhong) as total FROM Phong");
        return $stmt->fetchColumn();
    }

    /**
     * [THÊM HÀM NÀY] Đếm số phòng còn trống
     */
    public function countAvailable()
    {
        $stmt = $this->db->query("SELECT COUNT(MaPhong) as total FROM Phong WHERE TinhTrangPhong = 'Trống'");
        return $stmt->fetchColumn();
    }
    // Hàm lấy danh sách phòng có chỗ trống
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
    public function getSinhVienInPhong($soPhong)
    {
        // Tên SP 'sp_DanhSachSinhVienTheoPhong' phải khớp
        $stmt = $this->db->prepare("CALL sp_DanhSachSinhVienTheoPhong(:sophong)");
        $stmt->execute(['sophong' => $soPhong]);
        
        // Trả về tất cả các dòng
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
?>