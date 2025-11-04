<h2>Thêm Loại Phòng Mới</h2>

<?php if (isset($success)): ?>
    <p style="color: green;"><?php echo $success; ?></p>
<?php endif; ?>
<?php if (isset($error)): ?>
    <p style="color: red;"><?php echo $error; ?></p>
<?php endif; ?>

<form method="POST" action="">
    <label for="tenloai">Tên Loại Phòng:</label><br>
    <input type="text" id="tenloai" name="tenloai" required><br><br>

    <label for="giathue">Giá Thuê (VND/tháng):</label><br>
    <input type="number" id="giathue" name="giathue" required><br><br>

    <button type="submit">Thêm Loại Phòng</button>
</form>

<hr>
<h3>Danh Sách Loại Phòng Hiện Có</h3>
<table border="1">
    <tr><th>Mã</th><th>Tên Loại</th><th>Giá Thuê</th></tr>
    <?php foreach ($loai_phong_list as $lp): ?>
        <tr>
            <td><?php echo $lp['MaLoaiPhong']; ?></td>
            <td><?php echo $lp['TenLoaiPhong']; ?></td>
            <td><?php echo number_format($lp['GiaThue']); ?> VND</td>
        </tr>
    <?php endforeach; ?>
</table>