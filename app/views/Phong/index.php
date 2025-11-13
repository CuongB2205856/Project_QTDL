<?php
// 1. Set các biến cho header
$title = 'Quản Lý Phòng';
$currentRoute = '/phong'; // Quan trọng: để active link sidebar

// 2. Gọi Header (Mở <html>, <head>, <body>, nav, sidebar, và <main>)
require_once __DIR__ . '/../components/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4 mt-4">
    <div>
        <h1 class="h3">Quản Lý Phòng</h1>

    </div>
    <div>
        <button id="btn-show-create-modal" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Thêm Phòng Mới
        </button>
    </div>
</div>

<div id="main-message"></div>

<div class="card">
    <div class="card-header">
        Danh Sách Phòng Hiện Có
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th scope="col">Số Phòng</th>
                        <th scope="col">Loại Phòng</th>
                        <th scope="col">Giá Thuê (VND/tháng)</th>
                        <th scope="col">SL Tối Đa</th>
                        <th scope="col">Tình Trạng</th>
                        <th scope="col" style="min-width: 150px;">Hành động</th>
                    </tr>
                </thead>
                <tbody id="phong-table-body">
                    <?php if (empty($phong_list)): ?>
                        <tr id="row-empty">
                            <td colspan="6" class="text-center">Chưa có phòng nào.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($phong_list as $p): ?>
                            <tr id="row-<?php echo $p['MaPhong']; ?>">
                                <td><?php echo htmlspecialchars($p['SoPhong']); ?></td>
                                <td><?php echo htmlspecialchars($p['TenLoaiPhong']); ?></td>
                                <td><?php echo number_format($p['GiaThue']); ?></td>
                                <td><?php echo $p['SoLuongToiDa']; ?></td>
                                <td><?php echo htmlspecialchars($p['TinhTrangPhong']); ?></td>
                                <td>
                                    <button class="btn btn-info btn-sm btn-edit" data-id="<?php echo $p['MaPhong']; ?>">
                                        <i class="bi bi-pencil-square"></i> Sửa
                                    </button>
                                    <button class="btn btn-danger btn-sm btn-delete" data-id="<?php echo $p['MaPhong']; ?>">
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

<div class="modal fade" id="phong-modal" tabindex="-1" aria-labelledby="phongModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-title">Thêm Phòng Mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="phong-form">
                <div class="modal-body">
                    <div id="modal-message"></div>

                    <input type="hidden" id="form-phong-id" name="id" value="0">

                    <div class="mb-3">
                        <label for="form-maloai" class="form-label">Loại Phòng:</label>
                        <select class="form-select" id="form-maloai" name="maloai" required>
                            <option value="">-- Chọn loại phòng --</option>
                            <?php foreach ($loai_phong_list as $lp): ?>
                                <option value="<?php echo $lp['MaLoaiPhong']; ?>">
                                    <?php echo htmlspecialchars($lp['TenLoaiPhong']); ?>
                                    (<?php echo number_format($lp['GiaThue']); ?> VND)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="form-sophong" class="form-label">Số Phòng:</label>
                        <input type="text" class="form-control" id="form-sophong" name="sophong" required>
                    </div>

                    <div class="mb-3">
                        <label for="form-slmax" class="form-label">Số Lượng Tối Đa:</label>
                        <input type="number" class="form-control" id="form-slmax" name="slmax" required>
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
        const form = document.getElementById('phong-form');
        const modalTitle = document.getElementById('modal-title');
        const modalMessage = document.getElementById('modal-message');
        const mainMessage = document.getElementById('main-message');
        const tableBody = document.getElementById('phong-table-body');

        // (QUAN TRỌNG) Khởi tạo Bootstrap Modal
        const modalElement = document.getElementById('phong-modal');
        const bootstrapModal = new bootstrap.Modal(modalElement);

        // Form fields
        const formId = document.getElementById('form-phong-id');
        const formMaLoai = document.getElementById('form-maloai');
        const formSoPhong = document.getElementById('form-sophong');
        const formSlMax = document.getElementById('form-slmax');

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
            mainMessage.innerHTML = `<div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${escapeHTML(message)}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>`;
        }

        // --- Hàm mở modal cho 'Create' ---
        function openCreateModal() {
            form.reset();
            formId.value = '0';
            modalTitle.textContent = 'Thêm Phòng Mới';
            showModalMessage('');
            bootstrapModal.show();
        }

        // --- Hàm mở modal cho 'Update' ---
        async function openUpdateModal(id) {
            try {
                const response = await fetch(`/phong/get/${id}`);
                const result = await response.json();

                if (result.success) {
                    form.reset();
                    formId.value = result.data.MaPhong;
                    formMaLoai.value = result.data.MaLoaiPhong;
                    formSoPhong.value = result.data.SoPhong;
                    formSlMax.value = result.data.SoLuongToiDa;

                    modalTitle.textContent = 'Sửa Phòng';
                    showModalMessage('');
                    bootstrapModal.show();
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
                ? '/phong/ajax_create'
                : `/phong/ajax_update/${id}`;

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();

                if (result.success) {
                    showModalMessage(result.message, false);

                    if (id === '0' || id === '') {
                        appendRowToTable(result.newRow);
                        form.reset();
                        formId.value = '0';
                    } else {
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
            if (!confirm('Bạn có chắc chắn muốn xóa phòng này?')) {
                return;
            }

            try {
                const response = await fetch(`/phong/ajax_delete/${id}`, {
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
        function createTableRow(p) {
            document.getElementById('row-empty')?.remove();

            return `
                <tr id="row-${p.MaPhong}">
                    <td>${escapeHTML(p.SoPhong)}</td>
                    <td>${escapeHTML(p.TenLoaiPhong)}</td>
                    <td>${new Intl.NumberFormat('vi-VN').format(p.GiaThue)}</td>
                    <td>${p.SoLuongToiDa}</td>
                    <td>${escapeHTML(p.TinhTrangPhong)}</td>
                    <td>
                        <button class="btn btn-info btn-sm btn-edit" data-id="${p.MaPhong}">
                            <i class="bi bi-pencil-square"></i> Sửa
                        </button>
                        <button class="btn btn-danger btn-sm btn-delete" data-id="${p.MaPhong}">
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
            const row = document.getElementById(`row-${rowData.MaPhong}`);
            if (row) {
                row.outerHTML = createTableRow(rowData);
            }
        }

        function showEmptyRow() {
            tableBody.innerHTML = '<tr id="row-empty"><td colspan="6" class="text-center">Chưa có phòng nào.</td></tr>';
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