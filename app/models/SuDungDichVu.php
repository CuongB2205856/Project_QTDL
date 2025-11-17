<?php

namespace App\Models; 

class SuDungDichVu
{
    public $MaSDDV;
    public $MaHD;
    public $MaDV;
    public $SoLuongSuDung;
    public $ThangSuDungDV;
    public $NamSuDungDV;
    
    protected $db; 

    public function __construct(\PDO $pdo)
    {
        $this->db = $pdo;
    }
    public function all()
    {
        $sql = "
            SELECT 
                sddv.MaSDDV, 
                sddv.SoLuongSuDung, 
                sddv.ThangSuDungDV, 
                sddv.NamSuDungDV,
                sv.HoTen,
                p.SoPhong,
                dv.TenDichVu
            FROM 
                sudungdichvu sddv
            JOIN 
                hopdong hd ON sddv.MaHD = hd.MaHD
            JOIN 
                sinhvien sv ON hd.MaSV = sv.MaSV
            JOIN 
                phong p ON hd.MaPhong = p.MaPhong
            JOIN 
                dichvu dv ON sddv.MaDV = dv.MaDV
            ORDER BY 
                sddv.MaSDDV;
        ";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * [MỚI] Lấy chi tiết 1 dòng SDDV
     */
    public function find($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM SuDungDichVu WHERE MaSDDV = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    public function create(array $data)
    {
        // Bỏ qua nếu dịch vụ đã tồn tại (dùng IGNORE)
        $sql = "INSERT IGNORE INTO SuDungDichVu (MaHD, MaDV, SoLuongSuDung, ThangSuDungDV, NamSuDungDV)
                VALUES (:mahd, :madv, :soluong, :thang, :nam)";
        
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([
            'mahd' => $data['MaHD'],
            'madv' => $data['MaDV'],
            'soluong' => $data['SoLuongSuDung'],
            'thang' => $data['ThangSuDungDV'],
            'nam' => $data['NamSuDungDV']
        ]);
    }
    /**
     * [MỚI] Cập nhật số lượng sử dụng
     */
    public function update($id, $soLuong)
    {
        $sql = "UPDATE SuDungDichVu SET SoLuongSuDung = :soluong WHERE MaSDDV = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'soluong' => $soLuong,
            'id' => $id
        ]);
    }

    /**
     * [MỚI] Xóa một dòng dịch vụ
     */
    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM SuDungDichVu WHERE MaSDDV = :id");
        return $stmt->execute(['id' => $id]);
    }
}
?>