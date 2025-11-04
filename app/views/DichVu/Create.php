<h2>Thêm Dịch Vụ Mới</h2>

<?php if (isset($success)): ?>
    <p style="color: green;"><?php echo $success; ?></p>
<?php endif; ?>

<form method="POST" action="">
    <label for="tendv">Tên Dịch Vụ:</label><br>
    <input type="text" id="tendv" name="tendv" required><br><br>

    <label for="dongia">Đơn Giá:</label><br>
    <input type="number" id="dongia" name="dongia" required><br><br>

    <button type="submit">Thêm Dịch Vụ</button>
</form>

<hr>
<h3>Danh Sách Dịch Vụ Hiện Có</h3>
<table border="1">
    <tr><th>Mã</th><th>Tên Dịch Vụ</th><th>Đơn Giá</th></tr>
    <?php foreach ($dv_list as $dv): ?>
        <tr>
            <td><?php echo $dv['MaDV']; ?></td>
            <td><?php echo $dv['TenDichVu']; ?></td>
            <td><?php echo number_format($dv['DonGiaDichVu']); ?> VND</td>
        </tr>
    <?php endforeach; ?>
</table>