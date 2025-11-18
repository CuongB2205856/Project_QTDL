<?php
// Gán tiêu đề và route
$title = 'Trang chủ';
$currentRoute = '/dashboard';

// Tải header
require_once __DIR__ . '/../components/header.php';
?>
<link rel="stylesheet" href="/assets/CSS/StyleDashboard.css">
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h2 class="bi me-2">Trang chủ</h2>
            <p class="text-muted">Tổng quan hệ thống Ký túc xá.</p>
        </div>
    </div>
</div>

<div class="row">

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Doanh thu (Tháng <?php echo e($currentMonth); ?>)</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?php echo number_format($stats['revenue'] ?? 0, 0, ',', '.'); ?> VNĐ
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-cash-stack fs-1 text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Tổng Sinh Viên</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?php echo e($stats['students']['total'] ?? 0); ?>
                        </div>
                        <div class="text-xs">
                            (<?php echo e($stats['students']['male'] ?? 0); ?> Nam /
                            <?php echo e($stats['students']['female'] ?? 0); ?> Nữ)
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-people-fill fs-1 text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Phòng còn trống</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?php echo e($stats['rooms']['available'] ?? 0); ?> /
                            <?php echo e($stats['rooms']['total'] ?? 0); ?>
                        </div>
                        <div class="text-xs">
                            (Trống / Tổng số phòng)
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-door-open-fill fs-1 text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<hr>

<div class="row">
    <div class="col-lg-6">
        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-file-earmark-spreadsheet-fill me-1"></i>
                Xuất Báo Cáo Doanh Thu
            </div>
            <div class="card-body">
                <p>Chọn nút bên dưới để mở cửa sổ tùy chọn và xuất báo cáo doanh thu theo tháng/năm.</p>
                <button id="btn-show-export-modal" class="btn btn-primary">
                    <i class="bi bi-download me-2"></i>
                    Mở Tùy Chọn Xuất Báo Cáo
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="exportReportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportModalLabel">Xuất Báo Cáo Doanh Thu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="<?php echo BASE_URL; ?>/dashboard/export_report" method="POST">
                <div class="modal-body">
                    <p>Chọn tháng và năm bạn muốn xuất báo cáo.</p>
                    <div class="mb-3">
                        <label for="exportThang" class="form-label">Chọn Tháng</label>
                        <select id="exportThang" name="thang" class="form-select" required></select>
                    </div>
                    <div class="mb-3">
                        <label for="exportNam" class="form-label">Chọn Năm</label>
                        <input type="number" id="exportNam" name="nam" class="form-control"
                            value="<?php echo date('Y'); ?>" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-download me-2"></i>
                        Xuất File CSV
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
// Tải footer
require_once __DIR__ . '/../components/footer.php';
?>

<script>
    $(document).ready(function () {
        // --- Logic cho Popup Xuất Báo cáo (SP2) ---

        // 1. Lấy nút bấm và chuẩn bị modal
        const btnShowModal = $('#btn-show-export-modal');
        const modalElement = document.getElementById('exportReportModal');
        const exportModal = new bootstrap.Modal(modalElement);

        // 2. Lấy các trường select trong modal
        const selectThang = $('#exportThang');
        const selectNam = $('#exportNam');

        // Lấy tháng hiện tại từ PHP
        const currentMonth = <?php echo $currentMonth; ?>;
        // 3. Tự động điền từ tháng 1 đến tháng hiện tại vào select
        for (let i = 1; i <= currentMonth; i++) {
            selectThang.append(new Option('Tháng ' + i, i));
        }
        selectThang.val(currentMonth); // Chọn tháng hiện tại

        // 4. Gán sự kiện click cho nút bấm chính
        btnShowModal.on('click', function () {
            // Cập nhật năm/tháng về hiện tại mỗi khi mở
            selectThang.val(currentMonth);
            selectNam.val(new Date().getFullYear());

            // Mở modal (popup)
            exportModal.show();
        });
    });
</script>