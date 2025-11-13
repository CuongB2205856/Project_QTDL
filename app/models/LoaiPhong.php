<?php

namespace App\Models;
class LoaiPhong
{
    public $MaLoaiPhong;
    public $TenLoaiPhong;
    public $GiaThue;

    protected $db;

    public function __construct(\PDO $pdo)
    {
        $this->db = $pdo;
    }

    // Hàm thêm mới loại phòng
    public function create(array $data)
    {
        $stmt = $this->db->prepare("INSERT INTO LoaiPhong (TenLoaiPhong, GiaThue) 
                                 VALUES (:tenloai, :giathue)");
        $stmt->execute([
            'tenloai' => $data['tenloai'],
            'giathue' => $data['giathue']
        ]);
        // Trả về ID của dòng vừa thêm
        return $this->db->lastInsertId();
    }

    // Hàm lấy danh sách loại phòng
    public function all()
    {
        return $this->db->query("SELECT * FROM LoaiPhong ORDER BY MaLoaiPhong")->fetchAll(\PDO::FETCH_ASSOC);
    }

    // Hàm lấy chi tiết loại phòng
    public function find($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM LoaiPhong WHERE MaLoaiPhong = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    // Hàm cập nhật thông tin loại phòng
    public function update($id, array $data)
    {
        $stmt = $this->db->prepare("UPDATE LoaiPhong 
                                 SET TenLoaiPhong = :tenloai, GiaThue = :giathue 
                                 WHERE MaLoaiPhong = :id");
        return $stmt->execute([
            'tenloai' => $data['tenloai'],
            'giathue' => $data['giathue'],
            'id' => $id
        ]);
    }

    // Hàm xóa loại phòng
    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM LoaiPhong WHERE MaLoaiPhong = :id");
        return $stmt->execute(['id' => $id]);
    }
}
?>