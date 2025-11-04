<h2>Thêm Phòng Mới</h2>

<?php if (isset($success)): ?>
    <p style="color: green;"><?php echo $success; ?></p>
<?php endif; ?>
<?php if (isset($error)): ?>
    <p style="color: red;"><?php echo $error; ?></p>
<?php endif; ?>

<form method="POST" action="">
    <label for="sophong">Số Phòng:</label><br>
    <input type="text" id="sophong" name="sophong" required><br><br>

    <label for="slmax">Số Lượng Tối Đa (SV):</label><br>
    <input type="number" id="slmax" name="slmax" required><br><br>
    
    <label for="maloai">Loại Phòng:</label><br>
    <select id="maloai" name="maloai" required>
        <?php foreach ($loai_phong_list as $lp): ?>
            <option value="<?php echo $lp['MaLoaiPhong']; ?>">
                <?php echo $lp['TenLoaiPhong']; ?> (<?php echo number_format($lp['GiaThue']); ?> VND)
            </option>
        <?php endforeach; ?>
    </select><br><br>

    <button type="submit">Thêm Phòng</button>
</form>