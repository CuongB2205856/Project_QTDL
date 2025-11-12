<?php
// 1. Set các biến cho header
$title = 'Dashboard Trang Chủ'; 
$currentRoute = '/dashboard'; // Quan trọng: để active link sidebar

// 2. Gọi Header (Mở <html>, <head>, <body>, nav, sidebar, và <main>)
require_once __DIR__ . '/../components/header.php'; 
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

<h2 class="mt-4">Bảng điều khiển (Dashboard)</h2>

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
<h2 class="mt-4">Nội dung dashboard của bạn...</h2>
<p>Bây giờ nội dung đã hiển thị chính xác bên phải sidebar (trên PC) và bên dưới header (trên Mobile).</p>


<?php
// 3. Gọi Footer (Đóng <main>, <footer>, <script>, </body>, </html>)
require_once __DIR__ . '/../components/footer.php'; 
?>