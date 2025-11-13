<?php
// 1. Set các biến cho header
$title = 'Quản Lý Sinh Viên';
$currentRoute = '/sinhvien'; // Quan trọng: để active link sidebar

// 2. Gọi Header
require_once __DIR__ . '/../components/header.php';
?>
<style>
    /* (CSS styles của bạn giữ nguyên, không cần thay đổi) */
    body { background-color: #f8f9fa; }
    .card { border: none; border-radius: 15px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08); margin-bottom: 20px; }
    .card-header { background: white; border-bottom: 2px solid #f0f0f0; padding: 20px 25px; font-size: 1.2rem; font-weight: 600; color: #2d3142; border-top-left-radius: 15px; border-top-right-radius: 15px; }
    .card-body { padding: 25px; }
    .table thead th { background-color: #f8f9fa; color: #6c757d; text-transform: uppercase; font-size: 0.85rem; font-weight: 600; border-top: none; border-bottom-width: 2px; padding: 15px; }
    .table tbody tr { border-bottom: 1px solid #f0f0f0; }
    .table tbody tr:last-child { border-bottom: none; }
    .table tbody td { vertical-align: middle; padding: 15px; color: #2d3142; }
    .modal-content { border: none; border-radius: 15px; box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12); }
    .modal-header { border-bottom: 2px solid #f0f0f0; padding: 20px 25px; }
    .modal-header .modal-title { font-size: 1.2rem; font-weight: 600; color: #2d3142; }
    .modal-body { padding: 25px; }
    .modal-footer { padding: 20px 25px; background-color: #f8f9fa; border-top: 1px solid #f0f0f0; border-bottom-left-radius: 15px; border-bottom-right-radius: 15px; }
</style>

<div class="d-flex justify-content-between align-items-center mb-4 mt-4">
    <div>
        <h1 class="h3">Quản lý Sinh Viên</h1>
    </div>
</div>

<div id="main-message"></div>

<div class="card">
    <div class="card-header">
        Danh sách Sinh Viên
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th scope="col">Mã SV</th>
                        <th scope="col">Họ Tên</th>
                        <th scope="col">Giới Tính</th>
                        <th scope="col">Số Điện Thoại</th>
                        <th scope="col">Phòng Hiện Tại</th>
                        <th scope="col" style="min-width: 220px;">Hành động</th>
                    </tr>
                </thead>
                <tbody id="sinhvien-table-body">
                    <?php if (empty($sinhvien_list)): ?>
                        <tr id="row-empty">
                            <td colspan="6" class="text-center">Chưa có sinh viên nào.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($sinhvien_list as $sv): ?>
                            <tr id="row-<?php echo htmlspecialchars($sv['MaSV']); ?>">
                                <td><?php echo htmlspecialchars($sv['MaSV']); ?></td>
                                <td><?php echo htmlspecialchars($sv['HoTen']); ?></td>
                                <td><?php echo htmlspecialchars($sv['GioiTinh']); ?></td>
                                <td><?php echo htmlspecialchars($sv['SoDienThoai']); ?></td>
                                <td><?php echo htmlspecialchars($sv['SoPhong'] ?? 'Chưa có'); ?></td>
                                <td>
                                    <button class="btn btn-info btn-sm btn-edit"
                                        data-id="<?php echo htmlspecialchars($sv['MaSV']); ?>">
                                        <i class="bi bi-pencil-square"></i> Sửa
                                    </button>
                                    <button class="btn btn-danger btn-sm btn-delete"
                                        data-id="<?php echo htmlspecialchars($sv['MaSV']); ?>">
                                        <i class="bi bi-trash"></i> Xóa
                                    </button>
                                    <button class="btn btn-warning btn-sm btn-reset-pass"
                                        data-id="<?php echo htmlspecialchars($sv['MaSV']); ?>"
                                        data-username="<?php echo htmlspecialchars($sv['HoTen']); // Dùng HoTen để confirm ?>">
                                        <i class="bi bi-key"></i> Reset MK
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="sinhVienModal" tabindex="-1" aria-labelledby="sinhVienModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sinhVienModalLabel">Cập nhật Thông tin Sinh Viên</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="sinhVienForm">
                <div class="modal-body">
                    <div id="modal-message"></div>

                    <div class="mb-3">
                        <label for="MaSV" class="form-label">Mã Sinh Viên:</label>
                        <input type="text" class="form-control" id="MaSV" name="masv" readonly>
                    </div>

                    <div class="mb-3">
                        <label for="HoTen" class="form-label">Họ Tên:</label>
                        <input type="text" class="form-control" id="HoTen" name="hoten" required>
                    </div>

                    <div class="mb-3">
                        <label for="GioiTinh" class="form-label">Giới Tính:</label>
                        <select class="form-select" id="GioiTinh" name="gioitinh" required>
                            <option value="Nam">Nam</option>
                            <option value="Nữ">Nữ</option>
                            <option value="Khác">Khác</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="SoDienThoai" class="form-label">Số Điện Thoại:</label>
                        <input type="tel" class="form-control" id="SoDienThoai" name="sdt">
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary" id="btn-submit">Lưu Cập Nhật</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // --- Khai báo biến ---
        const tableBody = document.getElementById('sinhvien-table-body');
        const form = document.getElementById('sinhVienForm');
        const modalMessage = document.getElementById('modal-message');
        const mainMessage = document.getElementById('main-message');
        const modalTitle = document.getElementById('sinhVienModalLabel');
        
        const modalElement = document.getElementById('sinhVienModal');
        const bootstrapModal = new bootstrap.Modal(modalElement);

        const maSVInput = document.getElementById('MaSV');
        const hoTenInput = document.getElementById('HoTen');
        const gioiTinhInput = document.getElementById('GioiTinh');
        const soDienThoaiInput = document.getElementById('SoDienThoai');

        // --- Hàm mở Modal để CẬP NHẬT (Đã sửa) ---
        function openUpdateModal(id) {
            form.reset();
            modalMessage.innerHTML = '';
            modalTitle.textContent = 'Cập nhật Thông tin Sinh Viên';

            // ==========================================================
            // SỬA LỖI TẠI ĐÂY
            // URL phải là /get/ (giống các module khác)
            // không phải /ajax_get_details/
            const fetchUrl = `/sinhvien/get/${id}`;
            // ==========================================================

            fetch(fetchUrl)
                .then(response => {
                    if (!response.ok) {
                        // Nếu server trả về 404 hoặc 500, ném lỗi để .catch() bắt
                        throw new Error('Network response was not ok: ' + response.statusText);
                    }
                    return response.json();
                })
                .then(result => {
                    if (result.success && result.data) { 
                        const sv = result.data;

                        maSVInput.value = sv.MaSV;
                        hoTenInput.value = sv.HoTen;
                        gioiTinhInput.value = sv.GioiTinh;
                        soDienThoaiInput.value = sv.SoDienThoai;

                        bootstrapModal.show();
                    } else {
                        // Lỗi logic (ví dụ: success: false)
                        showMessage(mainMessage, result.message || 'Không tìm thấy sinh viên.', 'danger');
                    }
                })
                .catch(error => {
                    // Lỗi fetch (404, 500, network error)
                    console.error('Fetch error:', error);
                    // Đây là thông báo lỗi bạn đã thấy:
                    showMessage(mainMessage, 'Lỗi khi tải dữ liệu. Vui lòng thử lại.', 'danger'); 
                });
        }

        // --- Hàm xử lý SUBMIT (Chỉ Cập nhật) ---
        function handleFormSubmit(event) {
            event.preventDefault();
            const formData = new FormData(form);
            const id = maSVInput.value;
            const url = `/sinhvien/ajax_update/${id}`; 

            fetch(url, {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        bootstrapModal.hide();
                        showMessage(mainMessage, data.message, 'success');
                        updateSinhVienInTable(data.updatedRowData); 
                    } else {
                        showMessage(modalMessage, data.message || 'Đã xảy ra lỗi.', 'danger');
                    }
                })
                .catch(error => {
                    console.error('Submit error:', error);
                    showMessage(modalMessage, 'Lỗi kết nối. Vui lòng thử lại.', 'danger');
                });
        }

        // --- Hàm XÓA ---
        function deleteSinhVien(id) {
            if (!confirm(`Bạn có chắc chắn muốn xóa sinh viên mã ${id}?`)) {
                return;
            }

            fetch(`/sinhvien/ajax_delete/${id}`, {
                method: 'POST' 
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showMessage(mainMessage, data.message, 'success');
                        document.getElementById(`row-${id}`)?.remove();
                        if (tableBody.getElementsByTagName('tr').length === 0) {
                            showEmptyRow();
                        }
                    } else {
                        showMessage(mainMessage, data.message || 'Lỗi khi xóa.', 'danger');
                    }
                })
                .catch(error => {
                    console.error('Delete error:', error);
                    showMessage(mainMessage, 'Lỗi kết nối. Vui lòng thử lại.', 'danger');
                });
        }

        // --- Hàm Reset Mật khẩu ---
        async function handleResetPassword(id, username) {
            if (!confirm(`Bạn có chắc chắn muốn reset mật khẩu cho '${username}'? (Mật khẩu mới sẽ là '123456')`)) {
                return;
            }
            try {
                const response = await fetch(`/sinhvien/ajax_reset_password/${id}`, {
                    method: 'POST'
                });
                const result = await response.json();
                showMessage(mainMessage, result.message, !result.success);
            } catch (error) {
                showMessage(mainMessage, 'Lỗi kết nối: ' + error.message, true);
            }
        }

        // --- Hàm hiển thị & Cập nhật Giao diện ---

        function showMessage(element, message, type = 'success') {
             const typeClass = (type === 'danger' || type === false) ? 'danger' : 'success';
             element.innerHTML = `<div class="alert alert-${typeClass} alert-dismissible fade show" role="alert">
                ${escapeHTML(message)}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>`;
        }

        function updateSinhVienInTable(sv) {
            const row = document.getElementById(`row-${sv.MaSV}`);
            if (row) {
                row.innerHTML = createTableRow(sv, false); 
            }
        }

        function createTableRow(sv, includeRowTag = true) {
            const soPhong = sv.SoPhong ? escapeHTML(sv.SoPhong) : 'Chưa có';
            const maSV = escapeHTML(sv.MaSV);
            const hoTen = escapeHTML(sv.HoTen);

            const content = `
                <td>${maSV}</td>
                <td>${hoTen}</td>
                <td>${escapeHTML(sv.GioiTinh)}</td>
                <td>${escapeHTML(sv.SoDienThoai)}</td>
                <td>${soPhong}</td>
                <td>
                    <button class="btn btn-info btn-sm btn-edit" data-id="${maSV}">
                        <i class="bi bi-pencil-square"></i> Sửa
                    </button>
                    <button class="btn btn-danger btn-sm btn-delete" data-id="${maSV}">
                        <i class="bi bi-trash"></i> Xóa
                    </button>
                    <button class="btn btn-warning btn-sm btn-reset-pass" 
                            data-id="${maSV}" 
                            data-username="${hoTen}">
                        <i class="bi bi-key"></i> Reset MK
                    </button>
                </td>
            `;
            return includeRowTag ? `<tr id="row-${maSV}">${content}</tr>` : content;
        }

        function showEmptyRow() {
            tableBody.innerHTML = '<tr id="row-empty"><td colspan="6" class="text-center">Chưa có sinh viên nào.</td></tr>';
        }

        function escapeHTML(str) {
            if (str === null || str === undefined) return '';
            return str.toString().replace(/[&<>"']/g, m => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '\"': '&quot;', "'": '&#039;' }[m]));
        }

        // --- GÁN SỰ KIỆN (Event Listeners) ---

        form.addEventListener('submit', handleFormSubmit);

        tableBody.addEventListener('click', function (event) {
            const target = event.target.closest('button');
            if (!target) return;

            const id = target.dataset.id;

            if (target.classList.contains('btn-edit')) {
                openUpdateModal(id);
            }
            if (target.classList.contains('btn-delete')) {
                deleteSinhVien(id);
            }
            if (target.classList.contains('btn-reset-pass')) {
                const username = target.dataset.username;
                handleResetPassword(id, username);
            }
        });

    }); // Hết DOMContentLoaded
</script>

<?php
// 3. GỌI FOOTER
require_once __DIR__ . '/../components/footer.php';
?>