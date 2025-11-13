<?php
// 1. Set các biến cho header
$title = 'Quản lý Dịch Vụ';
$currentRoute = '/dichvu'; // Quan trọng: để active link sidebar

// 2. Gọi Header (Mở <html>, <head>, <body>, nav, sidebar, và <main>)
require_once __DIR__ . '/../components/header.php';
?>


<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h2 class="bi me-2">Quản Lý Dịch Vụ</h2>         
        </div>
        <div>
            <button id="btn-show-create-modal" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Thêm Dịch Vụ Mới
        </button>
        </div>
    </div>
</div>

<div id="main-message"></div>

<div class="card">
    <div class="card-header">
        Danh Sách Dịch Vụ Hiện Có
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th scope="col">Mã DV</th>
                        <th scope="col">Tên Dịch Vụ</th>
                        <th scope="col">Đơn Giá</th>
                        <th scope="col" style="min-width: 150px;">Hành động</th>
                    </tr>
                </thead>
                <tbody id="dichvu-table-body">
                    <?php if (empty($dichvu_list)): ?>
                        <tr id="row-empty">
                            <td colspan="4" class="text-center">Chưa có dịch vụ nào.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($dichvu_list as $dv): ?>
                            <tr id="row-<?php echo $dv['MaDV']; ?>">
                                <td><?php echo $dv['MaDV']; ?></td>
                                <td><?php echo htmlspecialchars($dv['TenDichVu']); ?></td>
                                <td><?php echo number_format($dv['DonGiaDichVu']); ?> VND</td>
                                <td>
                                    <button class="btn btn-info btn-sm btn-edit" data-id="<?php echo $dv['MaDV']; ?>">
                                        <i class="bi bi-pencil-square"></i> Sửa
                                    </button>
                                    <button class="btn btn-danger btn-sm btn-delete" data-id="<?php echo $dv['MaDV']; ?>">
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

<div class="modal fade" id="dichvuModal" tabindex="-1" aria-labelledby="dichvuModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-title">Thêm Dịch Vụ Mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="dichvu-form">
                <div class="modal-body">
                    <div id="modal-message"></div>

                    <input type="hidden" id="form-dichvu-id" name="id" value="0">

                    <div class="mb-3">
                        <label for="form-tendv" class="form-label">Tên Dịch Vụ:</label>
                        <input type="text" class="form-control" id="form-tendv" name="tendv" required
                            placeholder="Ví dụ: Điện (VND/kWh) hoặc Gửi xe (VND/tháng)">
                    </div>

                    <div class="mb-3">
                        <label for="form-dongia" class="form-label">Đơn Giá (VND):</label>
                        <input type="number" class="form-control" id="form-dongia" name="dongia" required>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary">Lưu Lại</button>
                </div>
            </form>

        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {

        // --- Lấy các đối tượng DOM ---
        const btnShowCreate = document.getElementById('btn-show-create-modal');
        const form = document.getElementById('dichvu-form');
        const modalTitle = document.getElementById('modal-title');
        const modalMessage = document.getElementById('modal-message');
        const mainMessage = document.getElementById('main-message');
        const tableBody = document.getElementById('dichvu-table-body');

        // (QUAN TRỌNG) Khởi tạo Bootstrap Modal
        const modalElement = document.getElementById('dichvuModal');
        const bootstrapModal = new bootstrap.Modal(modalElement);

        // Form fields
        const formId = document.getElementById('form-dichvu-id');
        const formTenDV = document.getElementById('form-tendv');
        const formDonGia = document.getElementById('form-dongia');

        // --- Hàm hiển thị thông báo (Bootstrap Alert) ---
        function showModalMessage(message, isError = false) {
            // SỬA LẠI TẠI ĐÂY:
            if (!message) {
                // Nếu message rỗng, thì xóa trắng nội dung của modal-message
                modalMessage.innerHTML = '';
                return; // Dừng hàm
            }

            // Chỉ tạo alert khi có nội dung message
            const type = isError ? 'danger' : 'success';
            modalMessage.innerHTML = `<div class="alert alert-${type}">${escapeHTML(message)}</div>`;
        }

        function showMainMessage(message, isError = false) {
            const type = isError ? 'danger' : 'success';
            // Dùng alert-dismissible cho phép đóng thông báo
            mainMessage.innerHTML = `<div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${escapeHTML(message)}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>`;
        }

        // --- Hàm reset form và mở modal cho 'Create' ---
        function openCreateModal() {
            form.reset();
            formId.value = '0';
            modalTitle.textContent = 'Thêm Dịch Vụ Mới';
            showModalMessage('');
            bootstrapModal.show(); // Dùng hàm của Bootstrap
        }

        // --- Hàm lấy dữ liệu và mở modal cho 'Update' ---
        async function openUpdateModal(id) {
            try {
                const response = await fetch(`/dichvu/get/${id}`);
                const result = await response.json();

                if (result.success) {
                    form.reset();
                    // Điền dữ liệu vào form
                    formId.value = result.data.MaDV;
                    formTenDV.value = result.data.TenDichVu;
                    formDonGia.value = result.data.DonGiaDichVu;

                    modalTitle.textContent = 'Sửa Dịch Vụ';
                    showModalMessage('');
                    bootstrapModal.show(); // Dùng hàm của Bootstrap
                } else {
                    showMainMessage(result.message, true);
                }
            } catch (error) {
                showMainMessage('Lỗi kết nối: ' + error.message, true);
            }
        }

        // --- Hàm xử lý Submit (Cả Create và Update) ---
        async function handleFormSubmit(event) {
            event.preventDefault();

            const id = formId.value;
            const formData = new FormData(form);

            let url = (id === '0' || id === '')
                ? '/dichvu/ajax_create'
                : `/dichvu/ajax_update/${id}`;

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();

                if (result.success) {
                    showModalMessage(result.message, false);

                    if (id === '0' || id === '') {
                        // --- Xử lý THÊM MỚI (Create) ---
                        appendRowToTable(result.newRow);
                        form.reset();
                        formId.value = '0';
                    } else {
                        // --- Xử lý CẬP NHẬT (Update) ---
                        updateRowInTable(result.updatedRow);
                    }
                } else {
                    showModalMessage(result.message, true);
                }
            } catch (error) {
                showModalMessage('Lỗi kết nối: ' + error.message, true);
            }
        }

        // --- Hàm xử lý Xóa (Delete) ---
        async function handleDelete(id) {
            if (!confirm('Bạn có chắc chắn muốn xóa dịch vụ này?')) {
                return;
            }

            try {
                const response = await fetch(`/dichvu/ajax_delete/${id}`, {
                    method: 'POST'
                });
                const result = await response.json();

                if (result.success) {
                    showMainMessage(result.message, false);
                    document.getElementById(`row-${id}`)?.remove();
                    if (tableBody.getElementsByTagName('tr').length === 0) {
                        showEmptyRow();
                    }
                } else {
                    showMainMessage(result.message, true);
                }
            } catch (error) {
                showMainMessage('Lỗi kết nối: ' + error.message, true);
            }
        }

        // --- CÁC HÀM TIỆN ÍCH CHO BẢNG ---
        function createTableRow(dv) { // dv = rowData (dichvu)
            document.getElementById('row-empty')?.remove();

            return `
                <tr id="row-${dv.MaDV}">
                    <td>${dv.MaDV}</td>
                    <td>${escapeHTML(dv.TenDichVu)}</td>
                    <td>${new Intl.NumberFormat('vi-VN').format(dv.DonGiaDichVu)} VND</td>
                    <td>
                        <button class="btn btn-info btn-sm btn-edit" data-id="${dv.MaDV}">
                            <i class="bi bi-pencil-square"></i> Sửa
                        </button>
                        <button class="btn btn-danger btn-sm btn-delete" data-id="${dv.MaDV}">
                            <i class="bi bi-trash"></i> Xóa
                        </button>
                    </td>
                </tr>
            `;
        }

        function appendRowToTable(rowData) {
            tableBody.insertAdjacentHTML('beforeend', createTableRow(rowData));
        }

        function updateRowInTable(rowData) {
            const row = document.getElementById(`row-${rowData.MaDV}`);
            if (row) {
                row.outerHTML = createTableRow(rowData);
            }
        }

        function showEmptyRow() {
            tableBody.innerHTML = '<tr id="row-empty"><td colspan="4" class="text-center">Chưa có dịch vụ nào.</td></tr>';
        }

        function escapeHTML(str) {
            if (str === null || str === undefined) return '';
            return str.toString().replace(/[&<>"']/g, m => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '\"': '&quot;', "'": '&#039;' }[m]));
        }

        // --- GÁN SỰ KIỆN (Event Listeners) ---
        btnShowCreate.addEventListener('click', openCreateModal);

        // Đóng modal khi nhấn ra ngoài
        modalElement.addEventListener('click', (event) => {
            if (event.target == modalElement) {
                bootstrapModal.hide();
            }
        });

        form.addEventListener('submit', handleFormSubmit);

        tableBody.addEventListener('click', function (event) {
            const target = event.target.closest('button');
            if (!target) return;

            const id = target.dataset.id;

            if (target.classList.contains('btn-edit')) {
                openUpdateModal(id);
            }
            if (target.classList.contains('btn-delete')) {
                handleDelete(id);
            }
        });

    });
</script>

<?php
// 3. Gọi Footer (Đóng <main>, <footer>, <script>, </body>, </html>)
require_once __DIR__ . '/../components/footer.php';
?>