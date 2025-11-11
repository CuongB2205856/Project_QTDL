<?php
// models/LoaiPhong.php

namespace App\Models;
class LoaiPhong
{
    public $MaLoaiPhong;
    public $TenLoaiPhong;
    public $GiaThue;

    protected $db; // Đối tượng PDO

    public function __construct(\PDO $pdo)
    {
        $this->db = $pdo;
    }

    // CREATE: Thêm loại phòng mới
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
    // READ: Cần thêm hàm all() để hiển thị danh sách
    public function all()
    {
        return $this->db->query("SELECT * FROM LoaiPhong ORDER BY MaLoaiPhong ASC")->fetchAll(\PDO::FETCH_ASSOC);
    }
    public function find($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM LoaiPhong WHERE MaLoaiPhong = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    // *** THÊM MỚI: UPDATE: CẬP NHẬT LOẠI PHÒNG ***
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

    // *** THÊM MỚI: DELETE: XÓA LOẠI PHÒNG ***
    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM LoaiPhong WHERE MaLoaiPhong = :id");
        return $stmt->execute(['id' => $id]);
    }
}
?>