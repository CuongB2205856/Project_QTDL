<?php
// 1. Set các biến cho header
$title = 'Quản lý Hợp đồng'; 
$currentRoute = '/hopdong'; // Quan trọng: để active link sidebar

// 2. Gọi Header
require_once __DIR__ . '/../components/header.php'; 
?>

<div class="d-flex justify-content-between align-items-center mb-4 mt-4">
    <div>
        <h1 class="h3">Quản lý Hợp đồng</h1>
    </div>
    <div>
        <button id="btn-add-hopdong" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Thêm Hợp đồng
        </button>
    </div>
</div>

<div id="main-message"></div>

<div class="card">
    <div class="card-header">
        Danh sách Hợp đồng
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th scope="col">Mã HĐ</th>
                        <th scope="col">Sinh Viên</th>
                        <th scope="col">Số Phòng</th>
                        <th scope="col">Ngày Bắt Đầu</th>
                        <th scope="col">Ngày Kết Thúc</th>
                        <th scope="col">Trạng Thái</th>
                        <th scope="col" style="width: 150px;">Hành động</th>
                    </tr>
                </thead>
                <tbody id="hopdong-table-body">
                    <?php if (empty($hopdong_list)): ?>
                        <tr id="row-empty">
                            <td colspan="8" class="text-center">Chưa có hợp đồng nào.</td> 
                        </tr>
                    <?php else: ?>
                        <?php foreach ($hopdong_list as $hd): ?>
                            <?php echo renderHopDongRow($hd); ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
// Hàm PHP helper để render 1 dòng của bảng
function renderHopDongRow($hd) {
    // Chuyển đổi ngày tháng sang d-m-Y cho dễ đọc
    $ngayBatDau = date('d-m-Y', strtotime($hd['NgayBatDau']));
    $ngayKetThuc = date('d-m-Y', strtotime($hd['NgayKetThuc']));

    $trangThaiBadge = ($hd['TrangThai'] == 'Active')
        ? '<span class="badge bg-success">Hoạt động</span>'
        : '<span class="badge bg-danger">Hết hạn</span>';

    return '
        <tr id="row-' . htmlspecialchars($hd['MaHopDong']) . '">
            <td>' . htmlspecialchars($hd['MaHopDong']) . '</td>
            <td>' . htmlspecialchars($hd['TenSinhVien']) . '</td>
            <td>' . htmlspecialchars($hd['SoPhong']) . '</td>
            <td>' . $ngayBatDau . '</td>
            <td>' . $ngayKetThuc . '</td>
            <td>' . $trangThaiBadge . '</td>
            <td>
                <button class="btn btn-info btn-sm btn-edit" 
                        data-id="' . htmlspecialchars($hd['MaHopDong']) . '">
                    <i class="bi bi-pencil-square"></i>
                </button>
                <button class="btn btn-danger btn-sm btn-delete" 
                        data-id="' . htmlspecialchars($hd['MaHopDong']) . '">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        </tr>
    ';
}
?>


<div class="modal fade" id="hopDongModal" tabindex="-1" 
     aria-labelledby="hopDongModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="hopDongModalLabel">Tạo Hợp đồng Mới</h5>
                <button type="button" class="btn-close" 
                        data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form id="hopDongForm">
                <div class="modal-body">
                    <div id="modal-message"></div>
                    
                    <input type="hidden" id="MaHopDong" name="MaHopDong">
                    
                    <div class="mb-3">
                        <label for="MaSV" class="form-label">Sinh viên:</label>
                        <select class="form-select" id="MaSV" name="MaSV" required>
                            <option value="">-- Chọn sinh viên --</option>
                            <?php foreach ($sinhvien_list as $sv): ?>
                                <option value="<?php echo htmlspecialchars($sv['MaSV']); ?>">
                                    <?php echo htmlspecialchars($sv['HoTen']) . ' (' . htmlspecialchars($sv['MaSV']) . ')'; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="MaPhong" class="form-label">Phòng:</label>
                        <select class="form-select" id="MaPhong" name="MaPhong" required>
                            <option value="">-- Chọn phòng --</option>
                             <?php foreach ($phong_list as $phong): ?>
                                <option value="<?php echo htmlspecialchars($phong['MaPhong']); ?>">
                                    <?php echo 'Phòng ' . htmlspecialchars($phong['SoPhong']) . ' (Còn ' . htmlspecialchars($phong['SoChoTrong']) . ' chỗ)'; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    

                    <div class="mb-3">
                        <label for="NgayBatDau" class="form-label">Ngày Bắt Đầu:</label>
                        <input type="date" class="form-control" 
                               id="NgayBatDau" name="NgayBatDau" required>
                    </div>

                    <div class="mb-3">
                        <label for="NgayKetThuc" class="form-label">Ngày Kết Thúc:</label>
                        <input type="date" class="form-control" 
                               id="NgayKetThuc" name="NgayKetThuc" required>
                    </div>
                    
                    <div class="mb-3" id="trangthai-group" style="display: none;">
                        <label for="TrangThai" class="form-label">Trạng Thái:</label>
                        <select class="form-select" id="TrangThai" name="TrangThai">
                            <option value="Active">Active (Hoạt động)</option>
                            <option value="Expired">Expired (Hết hạn)</option>
                        </select>
                    </div>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" 
                            data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary" 
                            id="btn-submit">Lưu</button>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- Khai báo biến ---
        const tableBody = document.getElementById('hopdong-table-body');
        const form = document.getElementById('hopDongForm');
        const modalMessage = document.getElementById('modal-message');
        const mainMessage = document.getElementById('main-message');
        const modalTitle = document.getElementById('hopDongModalLabel');
        const btnAddHopDong = document.getElementById('btn-add-hopdong');
        
        // Điều khiển Bootstrap Modal
        const modalElement = document.getElementById('hopDongModal');
        const bootstrapModal = new bootstrap.Modal(modalElement);

        let currentAction = 'create'; // 'create' hoặc 'update'

        // --- Các trường trong Form ---
        const maHopDongInput = document.getElementById('MaHopDong');
        const maSVSelect = document.getElementById('MaSV');
        const maPhongSelect = document.getElementById('MaPhong');
        const ngayBatDauInput = document.getElementById('NgayBatDau');
        const ngayKetThucInput = document.getElementById('NgayKetThuc');
        const trangThaiGroup = document.getElementById('trangthai-group');
        const trangThaiSelect = document.getElementById('TrangThai');

        // --- Hàm mở Modal để TẠO MỚI ---
        function openCreateModal() {
            currentAction = 'create';
            form.reset(); 
            maHopDongInput.value = '';
            
            modalTitle.textContent = 'Thêm Hợp đồng Mới';
            modalMessage.innerHTML = '';
            
            // Bật lại các trường select
            maSVSelect.disabled = false;
            maPhongSelect.disabled = false;
            trangThaiGroup.style.display = 'none'; // Ẩn trạng thái khi tạo mới
            
            bootstrapModal.show();
        }

        // --- Hàm mở Modal để CẬP NHẬT ---
        function openUpdateModal(id) {
            currentAction = 'update';
            form.reset();
            modalMessage.innerHTML = '';
            modalTitle.textContent = 'Cập nhật Hợp đồng';

            // Tắt các trường không cho phép sửa
            maSVSelect.disabled = true;
            maPhongSelect.disabled = true;
            trangThaiGroup.style.display = 'block'; // Hiển thị trạng thái

            // API "get"
            fetch(`/api/hopdong/get/${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.hopdong) {
                        const hd = data.hopdong;
                        
                        // Điền dữ liệu vào form
                        maHopDongInput.value = hd.MaHopDong;
                        maSVSelect.value = hd.MaSV;
                        maPhongSelect.value = hd.MaPhong;
                        ngayBatDauInput.value = hd.NgayBatDau;
                        ngayKetThucInput.value = hd.NgayKetThuc;
                        trangThaiSelect.value = hd.TrangThai;

                        bootstrapModal.show();
                    } else {
                        showMessage(mainMessage, data.message || 'Không tìm thấy hợp đồng.', 'danger');
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    showMessage(mainMessage, 'Lỗi khi tải dữ liệu. Vui lòng thử lại.', 'danger');
                });
        }

        // --- Hàm xử lý SUBMIT (Dùng cho cả Create và Update) ---
        function handleFormSubmit(event) {
            event.preventDefault();
            
            // Bật lại trường bị vô hiệu hóa để FormData có thể lấy giá trị
            maSVSelect.disabled = false;
            maPhongSelect.disabled = false;
            
            const formData = new FormData(form);
            const url = (currentAction === 'create') ? '/api/hopdong/create' : '/api/hopdong/update';

            fetch(url, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    bootstrapModal.hide(); // Đóng dialog
                    showMessage(mainMessage, data.message, 'success');
                    
                    // Tải lại trang để cập nhật mọi thứ (danh sách HĐ, danh sách SV, Phòng)
                    location.reload(); 
                    
                } else {
                    // Hiển thị lỗi bên TRONG modal
                    showMessage(modalMessage, data.message || 'Đã xảy ra lỗi.', 'danger');
                }
            })
            .catch(error => {
                console.error('Submit error:', error);
                showMessage(modalMessage, 'Lỗi kết nối. Vui lòng thử lại.', 'danger');
            })
            .finally(() => {
                // Tắt lại các trường (nếu là update) để giữ nguyên trạng thái
                if (currentAction === 'update') {
                    maSVSelect.disabled = true;
                    maPhongSelect.disabled = true;
                }
            });
        }

        // --- Hàm XÓA (Trigger bởi nút "Xóa") ---
        function deleteHopDong(id) {
            if (!confirm(`Bạn có chắc chắn muốn xóa hợp đồng mã ${id}?`)) {
                return;
            }

            fetch(`/api/hopdong/delete/${id}`, { // API đã có sẵn
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

        // --- Các hàm tiện ích ---
        
        function showMessage(element, message, type = 'success') {
            element.innerHTML = `<div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${escapeHTML(message)}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>`;
        }

        function showEmptyRow() {
            tableBody.innerHTML = '<tr id="row-empty"><td colspan="8" class="text-center">Chưa có hợp đồng nào.</td></tr>';
        }
        
        function escapeHTML(str) { 
             if (str === null || str === undefined) return '';
             return str.toString().replace(/[&<>\"']/g, m => ({'&': '&amp;', '<': '&lt;', '>': '&gt;', '\"': '&quot;', "'": '&#039;'}[m]));
        }

        // --- GÁN SỰ KIỆN (Event Listeners) ---

        // Nút Thêm Hợp đồng
        btnAddHopDong.addEventListener('click', openCreateModal);

        // Submit Form
        form.addEventListener('submit', handleFormSubmit);

        // Các nút Sửa/Xóa (dùng event delegation)
        tableBody.addEventListener('click', function(event) {
            const target = event.target.closest('button'); 
            if (!target) return;

            const id = target.dataset.id;
            
            if (target.classList.contains('btn-edit')) {
                openUpdateModal(id);
            }
            if (target.classList.contains('btn-delete')) {
                deleteHopDong(id);
            }
        });
        
    }); // Hết DOMContentLoaded
</script>

<?php
// 3. GỌI FOOTER
require_once __DIR__ . '/../components/footer.php'; 
?>