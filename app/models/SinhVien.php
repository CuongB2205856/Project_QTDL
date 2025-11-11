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

    // READ: Tìm SV theo Mã SV (cần thiết để kiểm tra)
    public function findById($maSV) {
        $stmt = $this->db->prepare("SELECT * FROM SinhVien WHERE MaSV = :masv");
        $stmt->execute(['masv' => $maSV]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }

    // CREATE: Thêm sinh viên mới
    public function create(array $data) {
        $stmt = $this->db->prepare("INSERT INTO SinhVien (MaSV, HoTen, GioiTinh, SoDienThoai) 
                                 VALUES (:masv, :hoten, :gioitinh, :sdt)");
        return $stmt->execute([
            'masv' => $data['masv'],
            'hoten' => $data['hoten'],
            'gioitinh' => $data['gioitinh'] ?? null,
            'sdt' => $data['sdt'] ?? null
        ]);
    }
    public function count() {
        $stmt = $this->db->query("SELECT COUNT(MaSV) as total FROM SinhVien");
        return $stmt->fetchColumn();
    }
}
?>