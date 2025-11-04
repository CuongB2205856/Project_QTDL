<?php
// models/DichVu.php
namespace App\Models;
class DichVu
{
    public $MaDV;
    public $TenDichVu;
    public $DonGiaDichVu;

    protected $db;

    public function __construct(\PDO $pdo)
    {
        $this->db = $pdo;
    }
    public function create(array $data)
    {
        $stmt = $this->db->prepare("INSERT INTO DichVu (TenDichVu, DonGiaDichVu) 
                                 VALUES (:tendv, :dongia)");
        return $stmt->execute([
            'tendv' => $data['tendv'],
            'dongia' => $data['dongia']
        ]);
    }
    // READ: Hàm all()
    public function all()
    {
        return $this->db->query("SELECT * FROM DichVu ORDER BY MaDV ASC")->fetchAll(\PDO::FETCH_ASSOC);
    }
}
?>