<?php
// app/views/dashboard/index.php
?>

<style>
    .dashboard-container {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        font-family: Arial, sans-serif;
    }
    .stat-card {
        flex: 1;
        min-width: 200px;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        color: white;
    }
    .stat-card h3 {
        margin-top: 0;
        font-size: 1.2em;
        font-weight: 300;
    }
    .stat-card .stat-number {
        font-size: 2.5em;
        font-weight: bold;
    }
    .card-blue { background-color: #007bff; }
    .card-green { background-color: #28a745; }
    .card-yellow { background-color: #ffc107; color: #333; }
    .card-red { background-color: #dc3545; }
</style>

<h2>Bảng điều khiển (Dashboard)</h2>

<?php if (isset($error)): ?>
    <p style="color: red;"><?php echo $error; ?></p>
<?php endif; ?>

<div class="dashboard-container">
    <div class="stat-card card-blue">
        <h3>Tổng số Sinh viên</h3>
        <p class="stat-number"><?php echo $total_students ?? 0; ?></p>
    </div>
    
    <div class="stat-card card-green">
        <h3>Tổng số Phòng</h3>
        <p class="stat-number"><?php echo $total_rooms ?? 0; ?></p>
    </div>

    <div class="stat-card card-yellow">
        <h3>Doanh thu (Đã TT)</h3>
        <p class="stat-number"><?php echo number_format($total_revenue_paid ?? 0); ?> VND</p>
    </div>

    <div class="stat-card card-red">
        <h3>Hóa đơn (Chưa TT)</h3>
        <p class="stat-number"><?php echo $total_unpaid_invoices ?? 0; ?></p>
    </div>
</div>

<hr>
<h3>Menu Chức năng</h3>
<ul>
    <li><a href="/loaiphong">Quản lý Loại Phòng</a></li>
    <li><a href="/phong/">Quản lý Phòng</a></li>
    <li><a href="/dichvu/">Quản lý Dịch vụ</a></li>
    <li><a href="/sinhvien">Quản lý sinh viên</a></li>
</ul>