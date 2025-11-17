-- Trigger trên bảng `hopdong`
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` TRIGGER `trg_HopDong_PreventOverfill` BEFORE INSERT ON `hopdong` FOR EACH ROW BEGIN
    DECLARE v_SoLuongToiDa INT;
    DECLARE v_SoLuongHienTai INT;
    DECLARE v_ErrorMessage VARCHAR(255);
    SELECT SoLuongToiDa INTO v_SoLuongToiDa FROM Phong WHERE MaPhong = NEW.MaPhong;
    -- Đếm số hợp đồng CÒN HẠN trong phòng đó
    SELECT COUNT(MaHD) INTO v_SoLuongHienTai FROM HopDong WHERE MaPhong = NEW.MaPhong AND NgayKetThuc >= CURDATE();

    IF v_SoLuongHienTai >= v_SoLuongToiDa THEN
        SET v_ErrorMessage = CONCAT('Phòng đã đầy (', v_SoLuongHienTai, '/', v_SoLuongToiDa, '). Không thể thêm sinh viên mới.');
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = v_ErrorMessage;
    END IF;
END ;;
DELIMITER ;

DELIMITER ;;
CREATE DEFINER=`root`@`localhost` TRIGGER `trg_HopDong_UpdateStatus_AfterInsert` AFTER INSERT ON `hopdong` FOR EACH ROW BEGIN
    DECLARE v_SoLuongToiDa INT;
    DECLARE v_SoLuongHienTai INT;
    SELECT SoLuongToiDa INTO v_SoLuongToiDa FROM Phong WHERE MaPhong = NEW.MaPhong;
    SELECT COUNT(MaHD) INTO v_SoLuongHienTai FROM HopDong WHERE MaPhong = NEW.MaPhong AND NgayKetThuc >= CURDATE();

    IF v_SoLuongHienTai >= v_SoLuongToiDa THEN
        UPDATE Phong SET TinhTrangPhong = 'Đầy' WHERE MaPhong = NEW.MaPhong;
    ELSE
        UPDATE Phong SET TinhTrangPhong = 'Trống' WHERE MaPhong = NEW.MaPhong;
    END IF;
END ;;
DELIMITER ;

DELIMITER ;;
CREATE DEFINER=`root`@`localhost` TRIGGER `trg_HopDong_UpdateStatus_AfterDelete` AFTER DELETE ON `hopdong` FOR EACH ROW BEGIN
    UPDATE Phong SET TinhTrangPhong = 'Trống' WHERE MaPhong = OLD.MaPhong;
END ;;
DELIMITER ;

-- Trigger trên bảng `phong`
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` TRIGGER `trg_Phong_PreventDeleteIfRented` BEFORE DELETE ON `phong` FOR EACH ROW BEGIN
    DECLARE v_SoLuongHopDong INT DEFAULT 0;
    DECLARE v_ErrorMessage VARCHAR(255);
    SELECT COUNT(MaHD) INTO v_SoLuongHopDong FROM HopDong WHERE MaPhong = OLD.MaPhong;

    IF v_SoLuongHopDong > 0 THEN
        SET v_ErrorMessage = CONCAT('Không thể xóa Phòng (', OLD.SoPhong, '). Phòng này đã có Hợp đồng liên quan.');
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = v_ErrorMessage;
    END IF;
END ;;
DELIMITER ;

-- Trigger trên bảng `sudungdichvu`
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` TRIGGER `trg_SuDungDichVu_AutoCreateHoaDon` AFTER INSERT ON `sudungdichvu` FOR EACH ROW BEGIN
    DECLARE v_DonGia DECIMAL(15, 2) DEFAULT 0.0;
    DECLARE v_TongTien DECIMAL(15, 2) DEFAULT 0.0;

    IF NEW.MaDV = 5 THEN -- MaDV = 5 là Tiền phòng
        SELECT lp.GiaThue INTO v_DonGia
        FROM loaiphong lp
        JOIN phong p ON lp.MaLoaiPhong = p.MaLoaiPhong
        JOIN hopdong hd ON p.MaPhong = hd.MaPhong
        WHERE hd.MaHD = NEW.MaHD
        LIMIT 1;
    ELSE -- Các dịch vụ khác
        SELECT DonGiaDichVu INTO v_DonGia
        FROM dichvu WHERE MaDV = NEW.MaDV;
    END IF;

    SET v_TongTien = v_DonGia * NEW.SoLuongSuDung;

    INSERT INTO hoadon (MaSDDV, NgayLapHoaDon, NgayHetHan, TongTienThanhToan, TrangThaiThanhToan)
    VALUES (NEW.MaSDDV, CURDATE(), CURDATE() + INTERVAL 10 DAY, v_TongTien, 'Chưa thanh toán');
END ;;
DELIMITER ;

-- Function: fn_KiemTraHopDongQuaHan
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `fn_KiemTraHopDongQuaHan`(p_MaSV VARCHAR(20)) RETURNS int
    READS SQL DATA
    DETERMINISTIC
BEGIN
    DECLARE v_SoLuongHopDongConHan INT DEFAULT 0;

    SELECT COUNT(MaHD)
    INTO v_SoLuongHopDongConHan
    FROM HopDong
    WHERE MaSV = p_MaSV
      AND NgayKetThuc >= CURDATE();

    IF v_SoLuongHopDongConHan = 0 THEN
        RETURN 1; -- Quá hạn (hoặc không có hợp đồng còn hạn)
    ELSE
        RETURN 0; -- Còn hạn
    END IF;
END ;;
DELIMITER ;

-- Function: fn_TinhTongTienHoaDonThang
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `fn_TinhTongTienHoaDonThang`(p_MaHD INT, p_Thang INT, p_Nam INT) RETURNS decimal(15,2)
    READS SQL DATA
    DETERMINISTIC
BEGIN
    DECLARE v_TienPhong DECIMAL(15, 2) DEFAULT 0.0;
    DECLARE v_TienDichVu DECIMAL(15, 2) DEFAULT 0.0;
    DECLARE v_TongTien DECIMAL(15, 2) DEFAULT 0.0;

    -- 1. Lấy tiền thuê phòng
    SELECT lp.GiaThue INTO v_TienPhong
    FROM HopDong hd
    JOIN Phong p ON hd.MaPhong = p.MaPhong
    JOIN LoaiPhong lp ON p.MaLoaiPhong = lp.MaLoaiPhong
    WHERE hd.MaHD = p_MaHD LIMIT 1;

    -- 2. Tính tổng tiền các dịch vụ đã sử dụng trong tháng/năm
    SELECT COALESCE(SUM(dv.DonGiaDichVu * sddv.SoLuongSuDung), 0)
    INTO v_TienDichVu
    FROM SuDungDichVu sddv
    JOIN DichVu dv ON sddv.MaDV = dv.MaDV
    WHERE sddv.MaHD = p_MaHD
        AND sddv.ThangSuDungDV = p_Thang
        AND sddv.NamSuDungDV = p_Nam;

    -- 3. Cộng tổng
    SET v_TongTien = v_TienPhong + v_TienDichVu;
    RETURN v_TongTien;
END ;;
DELIMITER ;

-- Procedure: sp_BaoCaoDoanhThuThang
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_BaoCaoDoanhThuThang`(IN p_Thang int, IN p_Nam int)
BEGIN
    SELECT
        -- Tổng tiền của hóa đơn 'Đã thanh toán'
        COALESCE(SUM(CASE
            WHEN hd.TrangThaiThanhToan = 'Đã thanh toán' THEN hd.TongTienThanhToan
            ELSE 0
        END), 0) AS DoanhThuDaThanhToan,

        -- Tổng tiền của hóa đơn 'Chưa thanh toán' hoặc NULL
        COALESCE(SUM(CASE
            WHEN (hd.TrangThaiThanhToan != 'Đã thanh toán' OR hd.TrangThaiThanhToan IS NULL) THEN hd.TongTienThanhToan
            ELSE 0
        END), 0) AS TienChuaThanhToan,

        -- Tổng số hóa đơn
        COUNT(hd.MaHoaDon) AS TongSoHoaDon
    FROM
        hoadon hd
    JOIN
        sudungdichvu sddv ON hd.MaSDDV = sddv.MaSDDV
    WHERE
        sddv.ThangSuDungDV = p_Thang
        AND sddv.NamSuDungDV = p_Nam;
END ;;
DELIMITER ;

-- Procedure: sp_DanhSachSinhVienTheoPhong
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_DanhSachSinhVienTheoPhong`(IN p_SoPhong VARCHAR(10))
BEGIN
    SELECT
        sv.MaSV,
        sv.HoTen,
        sv.GioiTinh,
        sv.SoDienThoai,
        hd.MaHD,
        hd.NgayBatDau,
        hd.NgayKetThuc
    FROM
        SinhVien sv
    JOIN
        HopDong hd ON sv.MaSV = hd.MaSV
    JOIN
        Phong p ON hd.MaPhong = p.MaPhong
    WHERE
        p.SoPhong = p_SoPhong
        AND hd.NgayKetThuc >= CURDATE(); -- Chỉ lấy HĐ còn hạn
END ;;
DELIMITER ;