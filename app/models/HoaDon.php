<?php
// models/HoaDon.php
namespace App\Models; 

class HoaDon
{
    public $MaHoaDon;
    public $MaSDDV;
    public $NgayLapHoaDon;
    public $NgayHetHan;
    public $TongTienThanhToan;
    
    protected $db; 

    public function __construct(\PDO $pdo)
    {
        $this->db = $pdo;
    }
    public function getTotalRevenue(string $status = 'Đã thanh toán') {
        $stmt = $this->db->prepare("
            SELECT SUM(TongTienThanhToan) as total 
            FROM HoaDon 
            WHERE TrangThaiThanhToan = :status
        ");
        $stmt->execute(['status' => $status]);
        $result = $stmt->fetchColumn();
        return $result ?: 0; // Trả về 0 nếu không có
    }

    // (Bạn cũng có thể thêm hàm đếm hóa đơn chưa thanh toán)
    public function countByStatus(string $status = 'Chưa thanh toán') {
        $stmt = $this->db->prepare("
            SELECT COUNT(MaHoaDon) as total 
            FROM HoaDon 
            WHERE TrangThaiThanhToan = :status
        ");
        $stmt->execute(['status' => $status]);
        return $stmt->fetchColumn();
    }
}
?>