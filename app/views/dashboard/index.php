<?php
$title = 'Trang Chủ';
$currentRoute = '/dashboard'; 

require_once __DIR__ . '/../components/header.php';
?>
<link rel="stylesheet" href="assets/CSS/StyleDashboard.css">

<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h2><i class="bi bi-speedometer2 me-2"></i>Dashboard</h2>            
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
require_once __DIR__ . '/../components/footer.php';
?>