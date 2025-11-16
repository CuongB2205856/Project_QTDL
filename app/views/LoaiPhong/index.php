<?php
$title = 'Quản lý Loại Phòng';
$currentRoute = '/loaiphong';

// 2. Gọi Header
require_once __DIR__ . '/../components/header.php';
?>

<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h2 class="bi me-2">Quản Lý Loại Phòng</h2>         
        </div>
        <div>
            <button id="btn-show-create-modal" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Thêm Loại Phòng Mới
        </button>
        </div>
    </div>
</div>

<div id="main-message"></div>

<div class="card">
    <div class="card-header">
        Danh Sách Loại Phòng Hiện Có
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th scope="col">Mã</th>
                        <th scope="col">Tên Loại</th>
                        <th scope="col">Giá Thuê</th>
                        <th scope="col" style="min-width: 150px;">Hành động</th>
                    </tr>
                </thead>
                <tbody id="loaiphong-table-body">
                    <?php if (empty($loai_phong_list)): ?>
                        <tr id="row-empty">
                            <td colspan="4" class="text-center">Chưa có loại phòng nào.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($loai_phong_list as $lp): ?>
                            <tr id="row-<?php echo $lp['MaLoaiPhong']; ?>">
                                <td><?php echo $lp['MaLoaiPhong']; ?></td>
                                <td><?php echo htmlspecialchars($lp['TenLoaiPhong']); ?></td>
                                <td><?php echo number_format($lp['GiaThue']); ?> VND</td>
                                <td>
                                    <button class="btn btn-info btn-sm btn-edit" data-id="<?php echo $lp['MaLoaiPhong']; ?>">
                                        <i class="bi bi-pencil-square"></i> Sửa
                                    </button>
                                    <button class="btn btn-danger btn-sm btn-delete"
                                        data-id="<?php echo $lp['MaLoaiPhong']; ?>">
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

<div class="modal fade" id="loaiphongModal" tabindex="-1" aria-labelledby="loaiphongModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-title">Thêm Loại Phòng Mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="loaiphong-form">
                <div class="modal-body">
                    <div id="modal-message"></div>

                    <input type="hidden" id="form-loaiphong-id" name="id" value="0">

                    <div class="mb-3">
                        <label for="form-tenloai" class="form-label">Tên Loại Phòng:</label>
                        <input type="text" class="form-control" id="form-tenloai" name="tenloai" required>
                    </div>

                    <div class="mb-3">
                        <label for="form-giathue" class="form-label">Giá Thuê (VND/tháng):</label>
                        <input type="number" class="form-control" id="form-giathue" name="giathue" required>
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

        // Lấy các đối tượng DOM
        const btnShowCreate = document.getElementById('btn-show-create-modal');
        const form = document.getElementById('loaiphong-form');
        const modalTitle = document.getElementById('modal-title');
        const modalMessage = document.getElementById('modal-message');
        const mainMessage = document.getElementById('main-message');
        const tableBody = document.getElementById('loaiphong-table-body');

        // (QUAN TRỌNG) Khởi tạo Bootstrap Modal
        const modalElement = document.getElementById('loaiphongModal');
        const bootstrapModal = new bootstrap.Modal(modalElement);

        // Input ẩn lưu ID
        const formId = document.getElementById('form-loaiphong-id');
        const formTenLoai = document.getElementById('form-tenloai');
        const formGiaThue = document.getElementById('form-giathue');

        // Hàm hiển thị thông báo (Bootstrap Alert)
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

        // Hàm reset form và mở modal cho 'Create'
        function openCreateModal() {
            form.reset();
            formId.value = '0';
            modalTitle.textContent = 'Thêm Loại Phòng Mới';
            showModalMessage('');
            bootstrapModal.show(); // Dùng hàm của Bootstrap
        }

        // Hàm lấy dữ liệu và mở modal cho 'Update'
        async function openUpdateModal(id) {
            try {
                const response = await fetch(`/loaiphong/get/${id}`);
                const result = await response.json();

                if (result.success) {
                    form.reset();
                    // Điền dữ liệu vào form
                    formId.value = result.data.MaLoaiPhong;
                    formTenLoai.value = result.data.TenLoaiPhong;
                    formGiaThue.value = result.data.GiaThue;

                    modalTitle.textContent = 'Sửa Loại Phòng';
                    showModalMessage('');
                    bootstrapModal.show(); // Dùng hàm của Bootstrap
                } else {
                    showMainMessage(result.message, true);
                }
            } catch (error) {
                showMainMessage('Lỗi kết nối: ' + error.message, true);
            }
        }

        // Hàm xử lý Submit (Cả Create và Update)
        async function handleFormSubmit(event) {
            event.preventDefault();

            const id = formId.value;
            const formData = new FormData(form);

            let url = (id === '0' || id === '') ? '/loaiphong/ajax_create' : `/loaiphong/ajax_update/${id}`;

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();

                if (result.success) {
                    showModalMessage(result.message, false);

                    if (id === '0' || id === '') {
                        // Create: Thêm hàng mới vào bảng và reset form
                        appendRowToTable(result.newRow);
                        form.reset();
                        formId.value = '0';
                    } else {
                        // Update: Cập nhật hàng trong bảng
                        updateRowInTable(result.updatedRow);
                        // KHÔNG đóng modal sau khi update, giữ nguyên theo yêu cầu cũ
                    }
                } else {
                    showModalMessage(result.message, true);
                }
            } catch (error) {
                showModalMessage('Lỗi kết nối: ' + error.message, true);
            }
        }

        // Hàm xử lý Xóa (Delete)
        async function handleDelete(id) {
            if (!confirm('Bạn có chắc chắn muốn xóa loại phòng này?')) {
                return;
            }

            try {
                const response = await fetch(`/loaiphong/ajax_delete/${id}`, {
                    method: 'POST'
                });
                const result = await response.json();

                if (result.success) {
                    showMainMessage(result.message, false);
                    const row = document.getElementById(`row-${id}`);
                    if (row) {
                        row.remove();
                    }
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


        // CÁC HÀM TIỆN ÍCH CHO BẢNG
        function createTableRow(rowData) {
            document.getElementById('row-empty')?.remove();

            return `
                <tr id="row-${rowData.MaLoaiPhong}">
                    <td>${rowData.MaLoaiPhong}</td>
                    <td>${escapeHTML(rowData.TenLoaiPhong)}</td>
                    <td>${new Intl.NumberFormat('vi-VN').format(rowData.GiaThue)} VND</td>
                    <td>
                        <button class="btn btn-info btn-sm btn-edit" data-id="${rowData.MaLoaiPhong}">
                            <i class="bi bi-pencil-square"></i> Sửa
                        </button>
                        <button class="btn btn-danger btn-sm btn-delete" data-id="${rowData.MaLoaiPhong}">
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
            const row = document.getElementById(`row-${rowData.MaLoaiPhong}`);
            if (row) {
                row.outerHTML = createTableRow(rowData);
            }
        }

        function showEmptyRow() {
            tableBody.innerHTML = '<tr id="row-empty"><td colspan="4" class="text-center">Chưa có loại phòng nào.</td></tr>';
        }

        function escapeHTML(str) {
            if (str === null || str === undefined) return '';
            return str.toString().replace(/[&<>"']/g, m => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '\"': '&quot;', "'": '&#039;' }[m]));
        }


        // GÁN SỰ KIỆN (Event Listeners)

        // 1. Mở modal 'Create' khi nhấn nút "Thêm"
        btnShowCreate.addEventListener('click', openCreateModal);

        // 2. Đóng modal khi nhấn ra ngoài vùng (Bootstrap tự xử lý nhưng vẫn thêm event cho chắc)
        modalElement.addEventListener('click', (event) => {
            if (event.target == modalElement) {
                bootstrapModal.hide();
            }
        });

        // 3. Xử lý Submit form
        form.addEventListener('submit', handleFormSubmit);

        // 4. Xử lý cho các nút "Sửa" và "Xóa" 
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
require_once __DIR__ . '/../components/footer.php';
?>