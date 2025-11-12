<?php
// 1. Set các biến cho header
$title = 'Dashboard - Trang Chủ';
$currentRoute = '/dashboard'; // Quan trọng: để active link sidebar

// 2. Gọi Header (Mở <html>, <head>, <body>, nav, sidebar, và <main>)
require_once __DIR__ . '/../components/header.php';
?>

<style>
    /* Dashboard Stats Cards */
    .dashboard-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: white;
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: var(--card-gradient);
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
    }

    .stat-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }

    .stat-card-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
    }

    .stat-card-title {
        font-size: 0.9rem;
        color: #6c757d;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin: 0;
    }

    .stat-card-value {
        font-size: 2rem;
        font-weight: 700;
        color: #2d3142;
        margin: 10px 0;
    }

    .stat-card-footer {
        display: flex;
        align-items: center;
        font-size: 0.85rem;
        color: #6c757d;
    }

    .stat-card-trend {
        display: inline-flex;
        align-items: center;
        padding: 4px 8px;
        border-radius: 6px;
        font-weight: 600;
        margin-right: 8px;
    }

    .stat-card-trend.up {
        background: #d4edda;
        color: #28a745;
    }

    .stat-card-trend.down {
        background: #f8d7da;
        color: #dc3545;
    }

    /* Card Color Variants */
    .stat-card.blue {
        --card-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .stat-card.blue .stat-card-icon {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .stat-card.green {
        --card-gradient: linear-gradient(135deg, #56ab2f 0%, #a8e063 100%);
    }

    .stat-card.green .stat-card-icon {
        background: linear-gradient(135deg, #56ab2f 0%, #a8e063 100%);
    }

    .stat-card.orange {
        --card-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }

    .stat-card.orange .stat-card-icon {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }

    .stat-card.red {
        --card-gradient: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
    }

    .stat-card.red .stat-card-icon {
        background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
    }

    /* Chart Cards */
    .chart-card {
        background: white;
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        margin-bottom: 20px;
    }

    .chart-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid #f0f0f0;
    }

    .chart-card-title {
        font-size: 1.2rem;
        font-weight: 600;
        color: #2d3142;
        margin: 0;
    }

    /* Quick Actions */
    .quick-actions {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-top: 30px;
    }

    .action-btn {
        display: flex;
        align-items: center;
        padding: 15px 20px;
        background: white;
        border-radius: 12px;
        text-decoration: none;
        color: #2d3142;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
    }

    .action-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
        color: #667eea;
    }

    .action-btn-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        font-size: 1.2rem;
    }

    .action-btn-text {
        font-weight: 600;
    }

    /* Recent Activity */
    .activity-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .activity-item {
        display: flex;
        align-items: center;
        padding: 15px 0;
        border-bottom: 1px solid #f0f0f0;
    }

    .activity-item:last-child {
        border-bottom: none;
    }

    .activity-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        font-size: 1.1rem;
    }

    .activity-icon.success {
        background: #d4edda;
        color: #28a745;
    }

    .activity-icon.info {
        background: #d1ecf1;
        color: #17a2b8;
    }

    .activity-icon.warning {
        background: #fff3cd;
        color: #ffc107;
    }

    .activity-content {
        flex: 1;
    }

    .activity-title {
        font-weight: 600;
        color: #2d3142;
        margin-bottom: 3px;
    }

    .activity-time {
        font-size: 0.85rem;
        color: #6c757d;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .dashboard-stats {
            grid-template-columns: 1fr;
        }

        .stat-card-value {
            font-size: 1.8rem;
        }
    }
</style>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h2><i class="bi bi-speedometer2 me-2"></i>Dashboard</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item active" aria-current="page">
                        <i class="bi bi-house-door me-1"></i>Trang chủ
                    </li>
                </ol>
            </nav>
        </div>
        <div>
            <button class="btn btn-primary">
                <i class="bi bi-download me-2"></i>Xuất báo cáo
            </button>
        </div>
    </div>
</div>

<?php if (isset($error)): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i>
        <strong>Lỗi!</strong> <?php echo $error; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Dashboard Stats -->
<div class="dashboard-stats">
    <div class="stat-card blue">
        <div class="stat-card-header">
            <div class="stat-card-icon">
                <i class="bi bi-people-fill"></i>
            </div>
        </div>
        <h3 class="stat-card-title">Tổng Sinh Viên</h3>
        <div class="stat-card-value"><?php echo number_format($total_students ?? 0); ?></div>
        <div class="stat-card-footer">
            <span class="stat-card-trend up">
                <i class="bi bi-arrow-up me-1"></i>+12%
            </span>
            <span>So với tháng trước</span>
        </div>
    </div>

    <div class="stat-card green">
        <div class="stat-card-header">
            <div class="stat-card-icon">
                <i class="bi bi-door-open"></i>
            </div>
        </div>
        <h3 class="stat-card-title">Tổng Số Phòng</h3>
        <div class="stat-card-value"><?php echo number_format($total_rooms ?? 0); ?></div>
        <div class="stat-card-footer">
            <span class="stat-card-trend up">
                <i class="bi bi-arrow-up me-1"></i>+5%
            </span>
            <span>Công suất sử dụng</span>
        </div>
    </div>

    <div class="stat-card orange">
        <div class="stat-card-header">
            <div class="stat-card-icon">
                <i class="bi bi-cash-stack"></i>
            </div>
        </div>
        <h3 class="stat-card-title">Doanh Thu (Đã TT)</h3>
        <div class="stat-card-value"><?php echo number_format($total_revenue_paid ?? 0); ?></div>
        <div class="stat-card-footer">
            <span class="stat-card-trend up">
                <i class="bi bi-arrow-up me-1"></i>+8%
            </span>
            <span>VND trong tháng</span>
        </div>
    </div>

    <div class="stat-card red">
        <div class="stat-card-header">
            <div class="stat-card-icon">
                <i class="bi bi-exclamation-triangle"></i>
            </div>
        </div>
        <h3 class="stat-card-title">Hóa Đơn Chưa TT</h3>
        <div class="stat-card-value"><?php echo number_format($total_unpaid_invoices ?? 0); ?></div>
        <div class="stat-card-footer">
            <span class="stat-card-trend down">
                <i class="bi bi-arrow-down me-1"></i>-3%
            </span>
            <span>Giảm so với kỳ trước</span>
        </div>
    </div>
</div>

<!-- Charts and Activity -->
<div class="row">
    <div class="col-lg-8">
        <div class="chart-card">
            <div class="chart-card-header">
                <h3 class="chart-card-title">
                    <i class="bi bi-graph-up me-2"></i>Biểu Đồ Doanh Thu
                </h3>
                <div class="btn-group btn-group-sm" role="group">
                    <button type="button" class="btn btn-outline-primary active">Tháng</button>
                    <button type="button" class="btn btn-outline-primary">Quý</button>
                    <button type="button" class="btn btn-outline-primary">Năm</button>
                </div>
            </div>
            <div
                style="height: 300px; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); border-radius: 10px;">
                <p class="text-muted mb-0">
                    <i class="bi bi-bar-chart-line fs-1 d-block mb-2"></i>
                    Biểu đồ thống kê sẽ hiển thị ở đây
                </p>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="chart-card">
            <div class="chart-card-header">
                <h3 class="chart-card-title">
                    <i class="bi bi-clock-history me-2"></i>Hoạt Động Gần Đây
                </h3>
            </div>
            <ul class="activity-list">
                <li class="activity-item">
                    <div class="activity-icon success">
                        <i class="bi bi-check-circle"></i>
                    </div>
                    <div class="activity-content">
                        <div class="activity-title">Thanh toán thành công</div>
                        <div class="activity-time">Sinh viên B1234567 - 5 phút trước</div>
                    </div>
                </li>
                <li class="activity-item">
                    <div class="activity-icon info">
                        <i class="bi bi-person-plus"></i>
                    </div>
                    <div class="activity-content">
                        <div class="activity-title">Sinh viên mới đăng ký</div>
                        <div class="activity-time">Nguyễn Văn A - 15 phút trước</div>
                    </div>
                </li>
                <li class="activity-item">
                    <div class="activity-icon warning">
                        <i class="bi bi-exclamation-circle"></i>
                    </div>
                    <div class="activity-content">
                        <div class="activity-title">Hóa đơn sắp đến hạn</div>
                        <div class="activity-time">Phòng A101 - 1 giờ trước</div>
                    </div>
                </li>
                <li class="activity-item">
                    <div class="activity-icon success">
                        <i class="bi bi-file-earmark-check"></i>
                    </div>
                    <div class="activity-content">
                        <div class="activity-title">Hợp đồng được ký kết</div>
                        <div class="activity-time">Sinh viên C7654321 - 2 giờ trước</div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>


<?php
// 3. Gọi Footer (Đóng <main>, <footer>, <script>, </body>, </html>)
require_once __DIR__ . '/../components/footer.php';
?>