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
        return $stmt->execute([
            'tenloai' => $data['tenloai'],
            'giathue' => $data['giathue']
        ]);
    }
    // READ: Cần thêm hàm all() để hiển thị danh sách
    public function all()
    {
        return $this->db->query("SELECT * FROM LoaiPhong ORDER BY MaLoaiPhong ASC")->fetchAll(\PDO::FETCH_ASSOC);
    }
}
?>