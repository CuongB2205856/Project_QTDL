<?php

namespace App\Models;
class DichVu
{
    protected $db;

    public function __construct(\PDO $pdo)
    {
        $this->db = $pdo;
    }

    // Hàm thêm dịch vụ
    public function create(array $data)
    {
        $stmt = $this->db->prepare("INSERT INTO DichVu (TenDichVu, DonGiaDichVu) 
                                 VALUES (:tendv, :dongia)");
        $stmt->execute([
            'tendv' => $data['tendv'],
            'dongia' => $data['dongia']
        ]);
        // Trả về ID của dịch vụ vừa thêm
        return $this->db->lastInsertId();
    }

    // Hàm lấy danh sách dịch vụ
    public function all()
    {
        return $this->db->query("SELECT * FROM DichVu ORDER BY MaDV ASC")->fetchAll(\PDO::FETCH_ASSOC);
    }

    // Hàm lấy chi tiết dịch vụ
    public function find($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM DichVu WHERE MaDV = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    // Hàm cập nhật thông tin dịch vụ    
    public function update($id, array $data)
    {
        $stmt = $this->db->prepare("UPDATE DichVu 
                                 SET TenDichVu = :tendv, 
                                     DonGiaDichVu = :dongia
                                 WHERE MaDV = :id");
        return $stmt->execute([
            'tendv' => $data['tendv'],
            'dongia' => $data['dongia'],
            'id' => $id
        ]);
    }

    // Hàm xóa dịch vụ
    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM DichVu WHERE MaDV = :id");
        return $stmt->execute(['id' => $id]);
    }

    // Hàm đếm số lượng dịch vụ
    public function count()
    {
        $stmt = $this->db->query("SELECT COUNT(MaDV) as total FROM DichVu");
        return $stmt->fetchColumn();
    }
}
?>