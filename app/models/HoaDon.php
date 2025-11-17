<?php

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

    // Hàm đếm số lượng hóa đơn
    public function getTotalRevenue(string $status = 'Đã thanh toán')
    {
        $stmt = $this->db->prepare("
            SELECT SUM(TongTienThanhToan) as total 
            FROM HoaDon 
            WHERE TrangThaiThanhToan = :status
        ");
        $stmt->execute(['status' => $status]);
        $result = $stmt->fetchColumn();
        return $result ?: 0; // Trả về 0 nếu không có
    }

    // Hàm đếm hóa đơn chưa thanh toán
    public function countByStatus(string $status = 'Chưa thanh toán')
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(MaHoaDon) as total 
            FROM HoaDon 
            WHERE TrangThaiThanhToan = :status
        ");
        $stmt->execute(['status' => $status]);
        return $stmt->fetchColumn();
    }

    public function allDetails()
    {
        $query = "
            SELECT 
                h.MaHoaDon, h.NgayLapHoaDon, h.NgayHetHan, h.TongTienThanhToan, h.TrangThaiThanhToan,
                sddv.ThangSuDungDV, sddv.NamSuDungDV,
                dv.TenDichVu,
                sv.HoTen AS TenSinhVien, 
                p.SoPhong
            FROM HoaDon h
            JOIN SuDungDichVu sddv ON h.MaSDDV = sddv.MaSDDV
            JOIN DichVu dv ON sddv.MaDV = dv.MaDV
            JOIN HopDong hd ON sddv.MaHD = hd.MaHD
            JOIN SinhVien sv ON hd.MaSV = sv.MaSV
            JOIN Phong p ON hd.MaPhong = p.MaPhong
            ORDER BY h.MaHoaDon 
        ";
        return $this->db->query($query)->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Lấy chi tiết 1 hóa đơn
     */
    public function findDetails($id)
    {
        $stmt = $this->db->prepare("
            SELECT 
                h.*, 
                sddv.*,
                dv.TenDichVu,
                sv.HoTen AS TenSinhVien, 
                p.SoPhong
            FROM HoaDon h
            JOIN SuDungDichVu sddv ON h.MaSDDV = sddv.MaSDDV
            JOIN DichVu dv ON sddv.MaDV = dv.MaDV
            JOIN HopDong hd ON sddv.MaHD = hd.MaHD
            JOIN SinhVien sv ON hd.MaSV = sv.MaSV
            JOIN Phong p ON hd.MaPhong = p.MaPhong
            WHERE h.MaHoaDon = :id
        ");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Hàm tạo Hóa Đơn (bao gồm cả Sử Dụng Dịch Vụ)
     * Đây là nơi xử lý logic nghiệp vụ MaDV = 6 (tiền phòng)
     */
    public function createInvoice(array $data)
    {
        $this->db->beginTransaction();
        try {
            $mahd = $data['mahd'];
            $madv = $data['madv'];
            // Nếu là tiền phòng (MaDV=6) thì số lượng luôn là 1
            $soluong = ($madv == 6) ? 1 : ($data['soluong'] ?? 1); 
            
            $dongia = 0;

            // === LOGIC NGHIỆP VỤ: Lấy đơn giá ===
            if ($madv == 6) {
                // Lấy giá thuê phòng từ hợp đồng
                $stmt_gia = $this->db->prepare("
                    SELECT lp.GiaThue 
                    FROM HopDong hd
                    JOIN Phong p ON hd.MaPhong = p.MaPhong
                    JOIN LoaiPhong lp ON p.MaLoaiPhong = lp.MaLoaiPhong
                    WHERE hd.MaHD = :mahd
                ");
                $stmt_gia->execute(['mahd' => $mahd]);
                $dongia = $stmt_gia->fetchColumn();
            } else {
                // Lấy đơn giá từ bảng DichVu
                $stmt_gia = $this->db->prepare("SELECT DonGiaDichVu FROM DichVu WHERE MaDV = :madv");
                $stmt_gia->execute(['madv' => $madv]);
                $dongia = $stmt_gia->fetchColumn();
            }

            if ($dongia === false || $dongia === null) {
                throw new \Exception("Không tìm thấy đơn giá cho dịch vụ hoặc hợp đồng.");
            }

            $tongtien = $dongia * $soluong;

            // 1. Thêm vào SuDungDichVu
            $stmt_sddv = $this->db->prepare(
                "INSERT INTO SuDungDichVu (MaHD, MaDV, SoLuongSuDung, ThangSuDungDV, NamSuDungDV) 
                 VALUES (:mahd, :madv, :soluong, :thang, :nam)"
            );
            $stmt_sddv->execute([
                'mahd' => $mahd,
                'madv' => $madv,
                'soluong' => $soluong,
                'thang' => $data['thang'],
                'nam' => $data['nam']
            ]);
            $maSDDV = $this->db->lastInsertId();

            // 2. Thêm vào HoaDon
            $stmt_hd = $this->db->prepare(
                "INSERT INTO HoaDon (MaSDDV, NgayLapHoaDon, NgayHetHan, TongTienThanhToan, TrangThaiThanhToan)
                 VALUES (:masddv, :ngaylap, :ngayhethan, :tongtien, 'Chưa thanh toán')"
            );
            $stmt_hd->execute([
                'masddv' => $maSDDV,
                'ngaylap' => $data['ngaylap'],
                'ngayhethan' => $data['ngayhethan'],
                'tongtien' => $tongtien
            ]);
            $maHoaDon = $this->db->lastInsertId();

            $this->db->commit();
            return $maHoaDon; // Trả về MaHoaDon mới tạo

        } catch (\Exception $e) {
            $this->db->rollBack();
            // Ném lỗi ra để Controller bắt
            throw $e;
        }
    }

    /**
     * Cập nhật trạng thái hóa đơn
     */
    public function updateStatus($id, $status)
    {
        $stmt = $this->db->prepare("UPDATE HoaDon SET TrangThaiThanhToan = :status WHERE MaHoaDon = :id");
        return $stmt->execute(['status' => $status, 'id' => $id]);
    }

    /**
     * Xóa Hóa Đơn (và cả SuDungDichVu liên quan)
     */
    public function deleteInvoice($id)
    {
        $this->db->beginTransaction();
        try {
            // 1. Tìm MaSDDV
            $stmt_find = $this->db->prepare("SELECT MaSDDV FROM HoaDon WHERE MaHoaDon = :id");
            $stmt_find->execute(['id' => $id]);
            $maSDDV = $stmt_find->fetchColumn();

            if ($maSDDV) {
                // 2. Xóa Hóa Đơn
                $stmt_hd = $this->db->prepare("DELETE FROM HoaDon WHERE MaHoaDon = :id");
                $stmt_hd->execute(['id' => $id]);

                // 3. Xóa SuDungDichVu
                $stmt_sddv = $this->db->prepare("DELETE FROM SuDungDichVu WHERE MaSDDV = :masddv");
                $stmt_sddv->execute(['masddv' => $maSDDV]);
            }

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function calculateTotalMonthlyBill($maHD, $thang, $nam)
    {
        // Tên hàm 'fn_TinhTongTienHoaDonThang' phải khớp với tên bạn tạo trong CSDL
        $stmt = $this->db->prepare("
            SELECT fn_TinhTongTienHoaDonThang(:mahd, :thang, :nam) AS TongTien
        ");
        
        $stmt->execute([
            'mahd' => $maHD,
            'thang' => $thang,
            'nam' => $nam
        ]);
        
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return (float)($result['TongTien'] ?? 0.0);
    }
    public function getBaoCaoDoanhThuThang($thang, $nam)
    {
        // Tên SP 'sp_BaoCaoDoanhThuThang' phải khớp
        $stmt = $this->db->prepare("CALL sp_BaoCaoDoanhThuThang(:thang, :nam)");
        $stmt->execute([
            'thang' => $thang,
            'nam' => $nam
        ]);
        
        // SP này trả về 1 dòng duy nhất
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    public function create(array $data)
    {
        $sql = "INSERT INTO HoaDon (MaSDDV, NgayLapHoaDon, NgayHetHan, TongTienThanhToan, TrangThaiThanhToan) 
                VALUES (:masddv, :ngaylap, :ngayhethan, :tongtien, :trangthai)";
        
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([
            'masddv' => $data['MaSDDV'],
            'ngaylap' => $data['NgayLapHoaDon'],
            'ngayhethan' => $data['NgayHetHan'],
            'tongtien' => $data['TongTienThanhToan'],
            'trangthai' => 'ChuaThanhToan'
        ]);
    }

    /**
     * [MỚI] Logic nghiệp vụ chính để tạo tất cả hóa đơn cho 1 tháng
     */
    public function generateInvoicesForMonth($maHD, $thang, $nam, $ngayHetHan, $sddvModel, $hopDongModel)
    {
        // 1. Lấy GiaThue (Tiền phòng)
        $hopDongDetails = $hopDongModel->findDetails($maHD);
        if (!$hopDongDetails || !isset($hopDongDetails['GiaThue'])) {
            throw new \Exception('Không tìm thấy hợp đồng hoặc giá thuê phòng.');
        }
        $giaThue = $hopDongDetails['GiaThue'];

        // 2. Thêm "Tiền phòng" (MaDV=5) vào sudungdichvu
        $sddvModel->create([
            'MaHD' => $maHD,
            'MaDV' => 5, // 5 là "Tiền phòng"
            'SoLuongSuDung' => 1,
            'ThangSuDungDV' => $thang,
            'NamSuDungDV' => $nam
        ]);

        // 3. Lấy TẤT CẢ dịch vụ (Điện, Nước, Giữ xe, Tiền phòng...) của tháng đó
        $stmt_sddv = $this->db->prepare("
            SELECT sddv.MaSDDV, sddv.SoLuongSuDung, dv.DonGiaDichVu, dv.TenDichVu
            FROM sudungdichvu sddv
            JOIN dichvu dv ON sddv.MaDV = dv.MaDV
            WHERE sddv.MaHD = :mahd 
              AND sddv.ThangSuDungDV = :thang 
              AND sddv.NamSuDungDV = :nam
        ");
        $stmt_sddv->execute(['mahd' => $maHD, 'thang' => $thang, 'nam' => $nam]);
        $services = $stmt_sddv->fetchAll(\PDO::FETCH_ASSOC);

        if (empty($services)) {
            throw new \Exception('Không tìm thấy dịch vụ nào (kể cả tiền phòng) cho tháng này.');
        }

        // 4. Lặp qua từng dịch vụ và TẠO HÓA ĐƠN RIÊNG LẺ
        $totalCreated = 0;
        $ngayLap = date('Y-m-d');
        
        foreach ($services as $service) {
            // Ghi đè DonGiaDichVu cho Tiền phòng
            $donGia = ($service['TenDichVu'] == 'Tiền phòng') ? $giaThue : $service['DonGiaDichVu'];
            
            $tongTien = $service['SoLuongSuDung'] * $donGia;

            $this->create([
                'MaSDDV' => $service['MaSDDV'],
                'NgayLapHoaDon' => $ngayLap,
                'NgayHetHan' => $ngayHetHan,
                'TongTienThanhToan' => $tongTien
            ]);
            $totalCreated++;
        }

        return $totalCreated; // Trả về số hóa đơn đã tạo
    }
}
?>