<?php
// 1. Set các biến cho header
$title = 'Quản Lý Sinh Viên';
$currentRoute = '/sinhvien'; // Quan trọng: để active link sidebar

// 2. Gọi Header (Mở <html>, <head>, <body>, nav, sidebar, và <main>)
require_once __DIR__ . '/../components/header.php';
?>
<style>
    /* Áp dụng nền xám nhạt cho body giống dashboard */
    body {
        background-color: #f8f9fa;
    }

    /* Tùy chỉnh Card chính chứa bảng */
    .card {
        border: none;
        border-radius: 15px;
        /* Bo góc giống dashboard */
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        /* Đổ bóng nhẹ */
        margin-bottom: 20px;
    }

    /* Tùy chỉnh Header của Card */
    .card-header {
        background: white;
        border-bottom: 2px solid #f0f0f0;
        /* Đường viền dưới giống chart-card */
        padding: 20px 25px;
        font-size: 1.2rem;
        font-weight: 600;
        color: #2d3142;
        /* Màu chữ tiêu đề */
        border-top-left-radius: 15px;
        border-top-right-radius: 15px;
    }

    /* Tùy chỉnh Body của Card */
    .card-body {
        padding: 25px;
    }

    /* Làm đẹp nút "Thêm User" */
    #btn-add-user {
        box-shadow: 0 4px 10px rgba(0, 123, 255, 0.3);
        transition: all 0.3s ease;
        font-weight: 500;
    }

    #btn-add-user:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(0, 123, 255, 0.4);
    }

    /* Tùy chỉnh Bảng (Table) */
    .table thead th {
        background-color: #f8f9fa;
        /* Nền header bảng */
        color: #6c757d;
        /* Màu chữ header */
        text-transform: uppercase;
        font-size: 0.85rem;
        font-weight: 600;
        border-top: none;
        border-bottom-width: 2px;
        padding: 15px;
    }

    .table tbody tr {
        border-bottom: 1px solid #f0f0f0;
        /* Đường kẻ mờ giữa các hàng */
    }

    .table tbody tr:last-child {
        border-bottom: none;
    }

    .table tbody td {
        vertical-align: middle;
        padding: 15px;
        color: #2d3142;
    }

    /* Tùy chỉnh Modal (Form Thêm/Sửa) */
    .modal-content {
        border: none;
        border-radius: 15px;
        /* Bo góc modal */
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
        /* Đổ bóng mạnh hơn cho modal */
    }

    .modal-header {
        border-bottom: 2px solid #f0f0f0;
        padding: 20px 25px;
    }

    .modal-header .modal-title {
        font-size: 1.2rem;
        font-weight: 600;
        color: #2d3142;
    }

    .modal-body {
        padding: 25px;
    }

    .modal-footer {
        padding: 20px 25px;
        background-color: #f8f9fa;
        border-top: 1px solid #f0f0f0;
        border-bottom-left-radius: 15px;
        border-bottom-right-radius: 15px;
    }
</style>
<div class="d-flex justify-content-between align-items-center mb-4 mt-4">
    <div>
        <h1 class="h3">Quản lý Sinh Viên</h1>
        <nav aria-label="breadcrumb">
        </nav>
    </div>
    <div>
        <button id="btn-add-sinhvien" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Thêm Sinh Viên
        </button>
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
                        <th scope="col">Hành động</th>
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
                <h5 class="modal-title" id="sinhVienModalLabel">Thêm Sinh Viên Mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="sinhVienForm">
                <div class="modal-body">
                    <div id="modal-message"></div>

                    <input type="hidden" name="MaSV" id="MaSV">

                    <div class="mb-3">
                        <label for="HoTen" class="form-label">Họ Tên:</label>
                        <input type="text" class="form-control" id="HoTen" name="HoTen" required>
                    </div>

                    <div class="mb-3">
                        <label for="GioiTinh" class="form-label">Giới Tính:</label>
                        <select class="form-select" id="GioiTinh" name="GioiTinh" required>
                            <option value="Nam">Nam</option>
                            <option value="Nữ">Nữ</option>
                            <option value="Khác">Khác</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="DiaChi" class="form-label">Địa Chỉ:</label>
                        <input type="text" class="form-control" id="DiaChi" name="DiaChi" required>
                    </div>

                    <div class="mb-3">
                        <label for="SoDienThoai" class="form-label">Số Điện Thoại:</label>
                        <input type="tel" class="form-control" id="SoDienThoai" name="SoDienThoai">
                    </div>

                    <div class="mb-3">
                        <label for="Email" class="form-label">Email:</label>
                        <input type="email" class="form-control" id="Email" name="Email">
                    </div>
                    <div class="mb-3" id="wrap-password">
                        <label for="Password" class="form-label">Mật khẩu:</label>
                        <input type="password" class="form-control" id="Password" name="Password">
                        <div class="form-text">Chỉ cần nhập khi tạo mới. Mật khẩu mặc định sẽ được tạo.</div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary" id="btn-submit">Lưu thay đổi</button>
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
        const btnAddSinhVien = document.getElementById('btn-add-sinhvien');

        // *ĐIỀU KHIỂN BOOTSTRAP MODAL*
        // Thay thế cho code (getElementById) và (.style.display) của bạn
        const modalElement = document.getElementById('sinhVienModal');
        const bootstrapModal = new bootstrap.Modal(modalElement);

        let currentAction = 'create'; // 'create' hoặc 'update'

        // --- Hàm xử lý ---

        // Hàm mở Modal để TẠO MỚI (Trigger bởi nút "Thêm Sinh Viên")
        function openCreateModal() {
            currentAction = 'create';
            form.reset();
            document.getElementById('MaSV').value = '';
            document.getElementById('MaSV').readOnly = false; // Cho phép nhập MaSV khi tạo
            modalTitle.textContent = 'Thêm Sinh Viên Mới';
            modalMessage.innerHTML = '';

            // === THÊM 2 DÒNG NÀY ===
            document.getElementById('wrap-password').style.display = 'block'; // Hiện trường pass
            document.getElementById('Password').required = true; // Bắt buộc nhập pass
            // ======================

            bootstrapModal.show();
        }

        // SỬA HÀM NÀY
        function openUpdateModal(id) {
            currentAction = 'update';
            form.reset();
            modalMessage.innerHTML = '';
            modalTitle.textContent = 'Cập nhật Thông tin Sinh Viên';

            // === THÊM 2 DÒNG NÀY ===
            document.getElementById('wrap-password').style.display = 'none'; // Ẩn trường pass
            document.getElementById('Password').required = false; // Không bắt buộc
            // ======================

            fetch(`/api/sinhvien/get/${id}`)
                .then(response => response.json())
                .then(data => {
                    // SỬA DÒNG NÀY (từ data.sinhvien thành data.data)
                    // Hoặc sửa ở Controller (tôi sẽ chọn sửa ở Controller, xem Bước 3)
                    if (data.success && data.sinhvien) {
                        const sv = data.sinhvien;

                        document.getElementById('MaSV').value = sv.MaSV;
                        document.getElementById('MaSV').readOnly = true; // Không cho sửa MaSV
                        document.getElementById('HoTen').value = sv.HoTen;
                        document.getElementById('GioiTinh').value = sv.GioiTinh;
                        document.getElementById('DiaChi').value = sv.DiaChi;
                        document.getElementById('SoDienThoai').value = sv.SoDienThoai;
                        document.getElementById('Email').value = sv.Email;

                        bootstrapModal.show();
                    } else {
                        showMessage(mainMessage, data.message || 'Không tìm thấy sinh viên.', 'danger');
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    showMessage(mainMessage, 'Lỗi khi tải dữ liệu. Vui lòng thử lại.', 'danger');
                });
        }

        // Hàm xử lý SUBMIT (Dùng cho cả Create và Update)
        function handleFormSubmit(event) {
            event.preventDefault();
            const formData = new FormData(form);
            const url = (currentAction === 'create') ? '/api/sinhvien/create' : '/api/sinhvien/update';

            fetch(url, {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Dùng hàm của Bootstrap để ĐÓNG dialog
                        bootstrapModal.hide();
                        showMessage(mainMessage, data.message, 'success');

                        // Cập nhật lại bảng
                        if (currentAction === 'create') {
                            appendSinhVienToTable(data.sinhvien);
                        } else {
                            updateSinhVienInTable(data.sinhvien);
                        }
                    } else {
                        // Hiển thị lỗi bên TRONG modal
                        showMessage(modalMessage, data.message || 'Đã xảy ra lỗi. Vui lòng kiểm tra lại.', 'danger');
                    }
                })
                .catch(error => {
                    console.error('Submit error:', error);
                    showMessage(modalMessage, 'Lỗi kết nối. Vui lòng thử lại.', 'danger');
                });
        }

        // Hàm XÓA (Trigger bởi nút "Xóa")
        function deleteSinhVien(id) {
            if (!confirm(`Bạn có chắc chắn muốn xóa sinh viên mã ${id}?`)) {
                return;
            }

            fetch(`/api/sinhvien/delete/${id}`, {
                method: 'POST' // Hoặc 'DELETE' tùy vào router của bạn
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showMessage(mainMessage, data.message, 'success');
                        const row = document.getElementById(`row-${id}`);
                        if (row) {
                            row.remove();
                        }
                        // Kiểm tra nếu bảng rỗng
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


        // --- Hàm hiển thị & Cập nhật Giao diện (Giữ nguyên) ---

        function showMessage(element, message, type = 'success') {
            element.innerHTML = `<div class="alert alert-${type}">${escapeHTML(message)}</div>`;
        }

        function appendSinhVienToTable(sv) {
            // Xóa dòng "chưa có" nếu nó tồn tại
            const emptyRow = document.getElementById('row-empty');
            if (emptyRow) {
                emptyRow.remove();
            }

            tableBody.insertAdjacentHTML('beforeend', createTableRow(sv));
        }

        function updateSinhVienInTable(sv) {
            const row = document.getElementById(`row-${sv.MaSV}`);
            if (row) {
                row.innerHTML = createTableRow(sv, false); // Chỉ cập nhật nội dung bên trong
            }
        }

        function createTableRow(sv, includeRowTag = true) {
            const soPhong = sv.SoPhong ? escapeHTML(sv.SoPhong) : 'Chưa có';
            const content = `
                <td>${escapeHTML(sv.MaSV)}</td>
                <td>${escapeHTML(sv.HoTen)}</td>
                <td>${escapeHTML(sv.GioiTinh)}</td>
                <td>${escapeHTML(sv.SoDienThoai)}</td>
                <td>${soPhong}</td>
                <td>
                    <button class="btn btn-info btn-sm btn-edit" data-id="${escapeHTML(sv.MaSV)}">
                        <i class="bi bi-pencil-square"></i> Sửa
                    </button>
                    <button class="btn btn-danger btn-sm btn-delete" data-id="${escapeHTML(sv.MaSV)}">
                        <i class="bi bi-trash"></i> Xóa
                    </button>
                </td>
            `;
            return includeRowTag ? `<tr id="row-${escapeHTML(sv.MaSV)}">${content}</tr>` : content;
        }

        function showEmptyRow() {
            tableBody.innerHTML = '<tr id="row-empty"><td colspan="6" class="text-center">Chưa có sinh viên nào.</td></tr>';
        }

        function escapeHTML(str) {
            if (str === null || str === undefined) return '';
            return str.toString().replace(/[&<>\"']/g, function (m) {
                return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '\"': '&quot;', "'": '&#039;' }[m];
            });
        }


        // --- GÁN SỰ KIỆN (Event Listeners) ---

        // Nút Thêm Sinh Viên
        btnAddSinhVien.addEventListener('click', openCreateModal);

        // Submit Form
        form.addEventListener('submit', handleFormSubmit);

        // Các nút Sửa/Xóa (dùng event delegation)
        tableBody.addEventListener('click', function (event) {
            const target = event.target.closest('button'); // Tìm nút gần nhất
            if (!target) return;

            const id = target.dataset.id;

            if (target.classList.contains('btn-edit')) {
                openUpdateModal(id);
            }
            if (target.classList.contains('btn-delete')) {
                deleteSinhVien(id);
            }
        });

    }); // Hết DOMContentLoaded
</script>

<?php
// 3. GỌI FOOTER (Đây là dòng quan trọng nhất bạn đã quên)
// Nó sẽ đóng <main>, <footer>, và tải BOOTSTRAP JS
require_once __DIR__ . '/../components/footer.php';
?>