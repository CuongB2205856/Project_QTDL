<?php
// app/views/StudentPanel/index.php
// $data['details'] chứa toàn bộ thông tin
// $data['maSV']

// 1. Gọi Header mới
$title = 'Trang Cá Nhân: ' . htmlspecialchars($data['details']['HoTen'] ?? $data['maSV']);
require_once __DIR__ . '/student_header.php';
?>

<!-- Hero Section -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm bg-gradient" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="card-body text-white py-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <h2 class="mb-2">
                            <i class="bi bi-person-circle me-2"></i>
                            Xin chào, <strong><?php echo htmlspecialchars($data['details']['HoTen'] ?? $data['maSV']); ?></strong>
                        </h2>
                        <p class="mb-0 opacity-75">
                            <i class="bi bi-calendar-check me-1"></i>
                            Hôm nay là <?php echo date('d/m/Y'); ?>
                        </p>
                    </div>
                    <div class="mt-3 mt-md-0">
                        <button class="btn btn-light btn-lg" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                            <i class="bi bi-shield-lock me-2"></i>Đổi Mật Khẩu
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Stats -->
<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100 hover-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="rounded-circle bg-info bg-opacity-10 p-3">
                            <i class="bi bi-door-open fs-1 text-info"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted text-uppercase mb-1">Phòng Ở</h6>
                        <?php if (!empty($data['details']['SoPhong'])): ?>
                            <h3 class="mb-0 text-primary fw-bold"><?php echo htmlspecialchars($data['details']['SoPhong']); ?></h3>
                            <small class="text-muted"><?php echo htmlspecialchars($data['details']['TenLoaiPhong']); ?></small>
                        <?php else: ?>
                            <h5 class="mb-0 text-danger">Chưa có phòng</h5>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100 hover-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="rounded-circle bg-warning bg-opacity-10 p-3">
                            <i class="bi bi-cash-stack fs-1 text-warning"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted text-uppercase mb-1">Công Nợ</h6>
                        <?php if (!empty($data['details']['GiaTienPhaiDong'])): ?>
                            <h3 class="mb-0 text-danger fw-bold"><?php echo number_format($data['details']['GiaTienPhaiDong']); ?> ₫</h3>
                            <small class="text-muted">
                                <i class="bi bi-clock me-1"></i>
                                Hạn: <?php echo date("d/m/Y", strtotime($data['details']['NgayDenHanDongTien'])); ?>
                            </small>
                        <?php else: ?>
                            <h5 class="mb-0 text-success">
                                <i class="bi bi-check-circle me-1"></i>Không có nợ
                            </h5>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Detailed Information -->
<div class="row g-4">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="mb-0 text-info">
                    <i class="bi bi-building me-2"></i>
                    Chi Tiết Phòng Ở
                </h5>
            </div>
            <div class="card-body">
                <?php if (!empty($data['details']['SoPhong'])): ?>
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">
                                <i class="bi bi-door-closed me-2"></i>Số phòng
                            </span>
                            <strong class="text-primary"><?php echo htmlspecialchars($data['details']['SoPhong']); ?></strong>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">
                                <i class="bi bi-grid-3x3 me-2"></i>Loại phòng
                            </span>
                            <strong><?php echo htmlspecialchars($data['details']['TenLoaiPhong']); ?></strong>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">
                                <i class="bi bi-currency-dollar me-2"></i>Giá thuê/tháng
                            </span>
                            <strong class="text-success"><?php echo number_format($data['details']['GiaTienThuePhong']); ?> ₫</strong>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="bi bi-house-x fs-1 text-muted mb-3"></i>
                        <p class="text-danger mb-0">Bạn hiện không có hợp đồng ở phòng nào còn hiệu lực.</p>
                        <small class="text-muted">Vui lòng liên hệ quản lý ký túc xá để được hỗ trợ.</small>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="mb-0 text-warning">
                    <i class="bi bi-receipt me-2"></i>
                    Thông Tin Thanh Toán
                </h5>
            </div>
            <div class="card-body">
                <?php if (!empty($data['details']['GiaTienPhaiDong'])): ?>
                    <div class="alert alert-warning border-0 mb-3" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <strong>Bạn có khoản thanh toán cần xử lý!</strong>
                    </div>
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">
                                <i class="bi bi-wallet2 me-2"></i>Số tiền phải đóng
                            </span>
                            <strong class="text-danger fs-5"><?php echo number_format($data['details']['GiaTienPhaiDong']); ?> ₫</strong>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">
                                <i class="bi bi-calendar-event me-2"></i>Ngày đến hạn
                            </span>
                            <strong class="text-danger"><?php echo date("d/m/Y", strtotime($data['details']['NgayDenHanDongTien'])); ?></strong>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button class="btn btn-warning w-100">
                            <i class="bi bi-credit-card me-2"></i>Thanh Toán Ngay
                        </button>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="bi bi-check-circle fs-1 text-success mb-3"></i>
                        <h5 class="text-success mb-2">Tài khoản đã thanh toán đầy đủ</h5>
                        <p class="text-muted mb-0">Hiện không có công nợ nào.</p>
                        <small class="text-muted d-block mt-2">
                            <i>(Nếu có phát sinh dịch vụ hoặc đến kỳ, hóa đơn mới sẽ xuất hiện ở đây)</i>
                        </small>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Change Password Modal -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="changePasswordModalLabel">
                    <i class="bi bi-shield-lock me-2"></i>Đổi Mật Khẩu
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="change-pass-form">
                    <div class="mb-3">
                        <label for="old_pass" class="form-label">
                            <i class="bi bi-lock me-1"></i>Mật khẩu cũ
                        </label>
                        <input type="password" id="old_pass" name="old_pass" class="form-control" required placeholder="Nhập mật khẩu hiện tại">
                    </div>
                    
                    <div class="mb-3">
                        <label for="new_pass" class="form-label">
                            <i class="bi bi-key me-1"></i>Mật khẩu mới
                        </label>
                        <input type="password" id="new_pass" name="new_pass" class="form-control" required placeholder="Nhập mật khẩu mới (tối thiểu 6 ký tự)">
                        <div class="form-text">Mật khẩu phải có ít nhất 6 ký tự</div>
                    </div>

                    <div class="mb-3">
                        <label for="confirm_new_pass" class="form-label">
                            <i class="bi bi-key-fill me-1"></i>Xác nhận mật khẩu mới
                        </label>
                        <input type="password" id="confirm_new_pass" name="confirm_new_pass" class="form-control" required placeholder="Nhập lại mật khẩu mới">
                    </div>

                    <div id="pass-form-message" class="mb-3"></div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i>Xác Nhận Đổi
                        </button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-2"></i>Hủy
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('change-pass-form');
    const messageEl = document.getElementById('pass-form-message');
    const modal = document.getElementById('changePasswordModal');

    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        messageEl.innerHTML = '<div class="alert alert-info mb-0"><i class="bi bi-hourglass-split me-2"></i>Đang xử lý...</div>';

        const newPass = document.getElementById('new_pass').value;
        const confirmPass = document.getElementById('confirm_new_pass').value;

        if (newPass !== confirmPass) {
            messageEl.innerHTML = '<div class="alert alert-danger mb-0"><i class="bi bi-x-circle me-2"></i>Mật khẩu mới không khớp!</div>';
            return;
        }

        if (newPass.length < 6) {
            messageEl.innerHTML = '<div class="alert alert-danger mb-0"><i class="bi bi-x-circle me-2"></i>Mật khẩu mới phải từ 6 ký tự trở lên.</div>';
            return;
        }

        const formData = new FormData(form);

        try {
            const response = await fetch('/student/ajax_change_password', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();

            if (result.success) {
                messageEl.innerHTML = '<div class="alert alert-success mb-0"><i class="bi bi-check-circle me-2"></i>' + result.message + '</div>';
                form.reset();
                setTimeout(() => {
                    bootstrap.Modal.getInstance(modal).hide();
                    messageEl.innerHTML = '';
                }, 2000);
            } else {
                messageEl.innerHTML = '<div class="alert alert-danger mb-0"><i class="bi bi-x-circle me-2"></i>' + result.message + '</div>';
            }

        } catch (error) {
            messageEl.innerHTML = '<div class="alert alert-danger mb-0"><i class="bi bi-exclamation-triangle me-2"></i>Lỗi kết nối: ' + error.message + '</div>';
        }
    });

    // Reset form when modal is closed
    modal.addEventListener('hidden.bs.modal', function () {
        form.reset();
        messageEl.innerHTML = '';
    });
});
</script>

<?php
// 3. Gọi Footer mới
require_once __DIR__ . '/student_footer.php';
?>