<?php
// 1. Set các biến cho header
$title = 'Dashboard Trang Chủ'; 
$currentRoute = '/hopdong'; // Quan trọng: để active link sidebar

// 2. Gọi Header (Mở <html>, <head>, <body>, nav, sidebar, và <main>)
require_once __DIR__ . '/../components/header.php'; 
?>

<style>
    .form-group { margin-bottom: 15px; }
    .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
    .form-control { width: 100%; max-width: 500px; padding: 8px; font-size: 1em; }
    .btn-submit { padding: 10px 20px; background-color: #007bff; color: white; border: none; cursor: pointer; }
    
    .message-box {
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 5px;
    }
    .message-success { background-color: #d4edda; color: #155724; }
    .message-danger { background-color: #f8d7da; color: #721c24; }
    .message-info { background-color: #cce5ff; color: #004085; }
</style>

<h2>Tạo Hợp Đồng Mới (Thêm Sinh Viên Vào Phòng)</h2>
<hr>

<?php if ($data['message']): ?>
    <div class="message-box message-<?php echo $data['message_type']; ?>">
        <?php echo htmlspecialchars($data['message']); ?>
    </div>
<?php endif; ?>


<form action="<?php echo url('hopdong/create'); ?>" method="POST">
    
    <div class="form-group">
        <label for="masv">Chọn Sinh Viên (Chưa có phòng):</label>
        <select id="masv" name="masv" class="form-control" required>
            <option value="">-- Vui lòng chọn --</option>
            <?php if (empty($data['sinhvien_list'])): ?>
                 <option value="" disabled>Không có sinh viên nào chưa có phòng.</option>
            <?php else: ?>
                <?php foreach ($data['sinhvien_list'] as $sv): ?>
                    <option value="<?php echo htmlspecialchars($sv['MaSV']); ?>">
                        <?php echo htmlspecialchars($sv['HoTen'] . ' (Mã: ' . $sv['MaSV'] . ')'); ?>
                    </option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>
    </div>

    <div class="form-group">
        <label for="maphong">Chọn Phòng:</label>
        <select id="maphong" name="maphong" class="form-control" required>
            <option value="">-- Vui lòng chọn --</option>
             <?php foreach ($data['phong_list'] as $p): ?>
                <option value="<?php echo htmlspecialchars($p['MaPhong']); ?>">
                    <?php 
                        // Giả sử $p có các cột này từ hàm all() của PhongModel
                        echo htmlspecialchars('Phòng ' . $p['SoPhong'] . ' - ' . $p['TenLoaiPhong']); 
                    ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <label for="ngaybd">Ngày Bắt Đầu:</label>
        <input type="date" id="ngaybd" name="ngaybd" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
    </div>

    <div class="form-group">
        <label for="ngaykt">Ngày Kết Thúc:</label>
        <input type="date" id="ngaykt" name="ngaykt" class="form-control" required>
    </div>

    <button type="submit" class="btn-submit">Lưu Hợp Đồng</button>

</form>
<?php
// 3. Gọi Footer (Đóng <main>, <footer>, <script>, </body>, </html>)
require_once __DIR__ . '/../components/footer.php'; 
?>