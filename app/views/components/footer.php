</div> <footer class="container-fluid bg-light py-3 border-top mt-auto">
    <div class="text-center text-muted">
        &copy; <?php echo date('Y'); ?> Bản quyền thuộc về Ký túc xá Đại học Cần Thơ.
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" 
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" 
        crossorigin="anonymous"></script>

<div class="modal fade" id="adminChangePasswordModal" tabindex="-1" aria-labelledby="adminChangePasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="adminChangePasswordModalLabel">
                    <i class="bi bi-shield-lock me-2"></i>Đổi Mật Khẩu Quản Trị
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="admin-change-pass-form">
                    <div class="mb-3">
                        <label for="admin_old_pass" class="form-label">
                            <i class="bi bi-lock me-1"></i>Mật khẩu cũ
                        </label>
                        <input type="password" id="admin_old_pass" name="old_pass" class="form-control" required placeholder="Nhập mật khẩu hiện tại">
                    </div>
                    
                    <div class="mb-3">
                        <label for="admin_new_pass" class="form-label">
                            <i class="bi bi-key me-1"></i>Mật khẩu mới
                        </label>
                        <input type="password" id="admin_new_pass" name="new_pass" class="form-control" required placeholder="Nhập mật khẩu mới (tối thiểu 6 ký tự)">
                    </div>

                    <div class="mb-3">
                        <label for="admin_confirm_new_pass" class="form-label">
                            <i class="bi bi-key-fill me-1"></i>Xác nhận mật khẩu mới
                        </label>
                        <input type="password" id="admin_confirm_new_pass" name="confirm_new_pass" class="form-control" required placeholder="Nhập lại mật khẩu mới">
                    </div>

                    <div id="admin-pass-form-message" class="mb-3"></div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i>Xác Nhận Đổi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const adminPassModalEl = document.getElementById('adminChangePasswordModal');
    if (adminPassModalEl) {
        const adminPassForm = document.getElementById('admin-change-pass-form');
        const adminPassMessageEl = document.getElementById('admin-pass-form-message');
        const adminBootstrapModal = new bootstrap.Modal(adminPassModalEl);

        adminPassForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            adminPassMessageEl.innerHTML = '<div class="alert alert-info mb-0">Đang xử lý...</div>';

            const newPass = document.getElementById('admin_new_pass').value;
            const confirmPass = document.getElementById('admin_confirm_new_pass').value;

            if (newPass.length < 6) {
                adminPassMessageEl.innerHTML = '<div class="alert alert-danger mb-0">Mật khẩu mới phải từ 6 ký tự trở lên.</div>';
                return;
            }
            if (newPass !== confirmPass) {
                adminPassMessageEl.innerHTML = '<div class="alert alert-danger mb-0">Mật khẩu mới không khớp!</div>';
                return;
            }

            const formData = new FormData(adminPassForm);

            try {
                // Sử dụng route mới mà chúng ta sẽ tạo ở bước 3 & 4
                const response = await fetch('/users/ajax_admin_change_password', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();

                if (result.success) {
                    adminPassMessageEl.innerHTML = '<div class="alert alert-success mb-0">' + result.message + '</div>';
                    adminPassForm.reset();
                    setTimeout(() => {
                        adminBootstrapModal.hide();
                        adminPassMessageEl.innerHTML = '';
                    }, 2000);
                } else {
                    adminPassMessageEl.innerHTML = '<div class="alert alert-danger mb-0">' + result.message + '</div>';
                }
            } catch (error) {
                adminPassMessageEl.innerHTML = '<div class="alert alert-danger mb-0">Lỗi kết nối: ' + error.message + '</div>';
            }
        });

        // Reset form khi modal bị đóng
        adminPassModalEl.addEventListener('hidden.bs.modal', function () {
            adminPassForm.reset();
            adminPassMessageEl.innerHTML = '';
        });
    }
});
</script>
</body>
</html>