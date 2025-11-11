<?php
// models/DichVu.php

namespace App\Models; 
class DichVu
{
    protected $db;

    public function __construct(\PDO $pdo)
    {
        $this->db = $pdo;
    }

    // CREATE: Sửa lại để trả về ID
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
    
    // READ (All): Giữ nguyên
    public function all()
    {
        return $this->db->query("SELECT * FROM DichVu ORDER BY TenDichVu ASC")->fetchAll(\PDO::FETCH_ASSOC);
    }

    // *** THÊM MỚI: READ (Find): TÌM 1 DỊCH VỤ THEO ID ***
    public function find($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM DichVu WHERE MaDV = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    
    // *** THÊM MỚI: UPDATE: CẬP NHẬT DỊCH VỤ ***
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
    
    // *** THÊM MỚI: DELETE: XÓA DỊCH VỤ ***
    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM DichVu WHERE MaDV = :id");
        return $stmt->execute(['id' => $id]);
    }

    // Giữ hàm count cho dashboard
    public function count() {
        $stmt = $this->db->query("SELECT COUNT(MaDV) as total FROM DichVu");
        return $stmt->fetchColumn();
    }
}
?>