<h2>Thêm Sinh Viên Mới vào Phòng</h2>

<?php if (isset($success)): ?>
    <p style="color: green;"><?php echo $success; ?></p>
<?php endif; ?>
<?php if (isset($error)): ?>
    <p style="color: red;"><?php echo $error; ?></p>
<?php endif; ?>

<form method="POST" action="">
    <h3>Thông tin Sinh viên (SV đã có sẽ tự động cập nhật HĐ)</h3>
    <label for="masv">Mã Sinh Viên:</label><br>
    <input type="text" id="masv" name="masv" required><br><br>

    <label for="hoten">Họ Tên:</label><br>
    <input type="text" id="hoten" name="hoten" required><br><br>

    <label for="sdt">Số Điện Thoại:</label><br>
    <input type="text" id="sdt" name="sdt"><br><br>
    
    <label for="gioitinh">Giới Tính:</label><br>
    <select id="gioitinh" name="gioitinh">
        <option value="Nam">Nam</option>
        <option value="Nữ">Nữ</option>
    </select><br><br>

    <h3>Thông tin Hợp đồng</h3>
    <label for="maphong">Chọn Phòng:</label><br>
    <select id="maphong" name="maphong" required>
        <?php foreach ($phong_list as $phong): ?>
            <option value="<?php echo $phong->MaPhong; ?>">
                <?php echo $phong->SoPhong; ?> (<?php echo $phong->TinhTrangPhong; ?>)
            </option>
        <?php endforeach; ?>
    </select><br><br>
    
    <label for="ngaybd">Ngày Bắt Đầu:</label><br>
    <input type="date" id="ngaybd" name="ngaybd" value="<?php echo date('Y-m-d'); ?>" required><br><br>

    <label for="ngaykt">Ngày Kết Thúc:</label><br>
    <input type="date" id="ngaykt" name="ngaykt" required><br><br>

    <button type="submit">Tạo Hợp Đồng</button>
</form>