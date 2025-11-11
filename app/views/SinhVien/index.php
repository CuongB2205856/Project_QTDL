<?php
// 1. Set các biến cho header
$title = 'Quản Lý Sinh Viên'; 
$currentRoute = '/sinhvien'; // Quan trọng: để active link sidebar

// 2. Gọi Header (Mở <html>, <head>, <body>, nav, sidebar, và <main>)
require_once __DIR__ . '/../components/header.php'; 
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3">Quản lý Sinh Viên</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb m-0">
                <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Quản lý Sinh Viên</li>
            </ol>
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


<div class="modal fade" id="sinhVienModal" tabindex="-1" 
     aria-labelledby="sinhVienModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sinhVienModalLabel">Thêm Sinh Viên Mới</h5>
                <button type="button" class="btn-close" 
                        data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form id="sinhVienForm">
                <div class="modal-body">
                    <div id="modal-message"></div>
                    
                    <input type="hidden" name="MaSV" id="MaSV">
                    
                    <div class="mb-3">
                        <label for="HoTen" class="form-label">Họ Tên:</label>
                        <input type="text" class="form-control" 
                               id="HoTen" name="HoTen" required>
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
                        <label for="NgaySinh" class="form-label">Ngày Sinh:</label>
                        <input type="date" class="form-control" 
                               id="NgaySinh" name="NgaySinh" required>
                    </div>

                    <div class="mb-3">
                        <label for="DiaChi" class="form-label">Địa Chỉ:</label>
                        <input type="text" class="form-control" 
                               id="DiaChi" name="DiaChi" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="SoDienThoai" class="form-label">Số Điện Thoại:</label>
                        <input type="tel" class="form-control" 
                               id="SoDienThoai" name="SoDienThoai">
                    </div>

                    <div class="mb-3">
                        <label for="Email" class="form-label">Email:</label>
                        <input type="email" class="form-control" 
                               id="Email" name="Email">
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" 
                            data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary" 
                            id="btn-submit">Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function() {
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
            form.reset(); // Xóa trắng form
            document.getElementById('MaSV').value = ''; // Đảm bảo MaSV rỗng
            modalTitle.textContent = 'Thêm Sinh Viên Mới';
            modalMessage.innerHTML = '';
            
            // Dùng hàm của Bootstrap để MỞ dialog
            bootstrapModal.show();
        }

        // Hàm mở Modal để CẬP NHẬT (Trigger bởi nút "Sửa")
        function openUpdateModal(id) {
            currentAction = 'update';
            form.reset();
            modalMessage.innerHTML = '';
            modalTitle.textContent = 'Cập nhật Thông tin Sinh Viên';

            fetch(`/api/sinhvien/get/${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.sinhvien) {
                        const sv = data.sinhvien;
                        // Điền dữ liệu vào form
                        document.getElementById('MaSV').value = sv.MaSV;
                        document.getElementById('HoTen').value = sv.HoTen;
                        document.getElementById('GioiTinh').value = sv.GioiTinh;
                        // Cần định dạng lại ngày tháng (YYYY-MM-DD)
                        document.getElementById('NgaySinh').value = sv.NgaySinh.split(' ')[0]; 
                        document.getElementById('DiaChi').value = sv.DiaChi;
                        document.getElementById('SoDienThoai').value = sv.SoDienThoai;
                        document.getElementById('Email').value = sv.Email;

                        // Dùng hàm của Bootstrap để MỞ dialog
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
             return str.toString().replace(/[&<>\"']/g, function(m) {
                 return {'&': '&amp;', '<': '&lt;', '>': '&gt;', '\"': '&quot;', "'": '&#039;'}[m];
             });
        }


        // --- GÁN SỰ KIỆN (Event Listeners) ---

        // Nút Thêm Sinh Viên
        btnAddSinhVien.addEventListener('click', openCreateModal);

        // Submit Form
        form.addEventListener('submit', handleFormSubmit);

        // Các nút Sửa/Xóa (dùng event delegation)
        tableBody.addEventListener('click', function(event) {
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