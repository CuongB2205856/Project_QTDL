<?php
$title = 'Quản lý Phòng';
$currentRoute = '/phong';
require_once __DIR__ . '/../components/header.php';
?>
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h2 class="bi me-2">Quản Lý Phòng</h2>
        </div>
        <div>
            <button id="btn-show-create-modal" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Thêm Phòng Mới
            </button>
        </div>
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
<div class="card mb-3">
    <div class="card-header">
        Tra cứu nhanh Sinh viên theo phòng
    </div>
    <div class="card-body">
        <form id="formTraCuuPhong" class="form-inline">
            <div class="form-group mb-2 me-2">
                <label for="inputSoPhong" class="sr-only">Nhập Số Phòng:</label>
                <input type="text" class="form-control" id="inputSoPhong" placeholder="Ví dụ: A101" required>
            </div>
            <button type="submit" class="btn btn-primary mb-2">Tìm kiếm</button>
        </form>

        <div id="ketQuaTraCuu" class="mt-3"></div>
    </div>
</div>
<script>
$(document).ready(function() {
    
    // Khởi tạo Modal
    const modalElement = document.getElementById('phong-modal');
    const bootstrapModal = new bootstrap.Modal(modalElement);

    // Lấy các đối tượng DOM
    const form = $('#phong-form');
    const modalTitle = $('#modal-title');
    const modalMessage = $('#modal-message');
    const mainMessage = $('#main-message');
    const tableBody = $('#phong-table-body');

    // Form fields
    const formId = $('#form-phong-id');
    const formMaLoai = $('#form-maloai');
    const formSoPhong = $('#form-sophong');
    const formSlMax = $('#form-slmax');

    // --- CHỨC NĂNG CRUD (THÊM, SỬA, XÓA PHÒNG) ---

    // 1. Mở Modal Thêm
    $('#btn-show-create-modal').on('click', function() {
        form.trigger('reset');
        formId.val('0');
        modalTitle.text('Thêm Phòng Mới');
        modalMessage.html('');
        bootstrapModal.show();
    });

    // 2. Mở Modal Sửa
    tableBody.on('click', '.btn-edit', function() {
        const id = $(this).data('id');
        
        // Dùng BASE_URL từ header.php
        $.get(BASE_URL + '/phong/get/' + id) 
            .done(function(res) {
                if (res.success) {
                    form.trigger('reset');
                    formId.val(res.data.MaPhong);
                    formMaLoai.val(res.data.MaLoaiPhong);
                    formSoPhong.val(res.data.SoPhong);
                    formSlMax.val(res.data.SoLuongToiDa);

                    modalTitle.text('Sửa Phòng');
                    modalMessage.html('');
                    bootstrapModal.show();
                } else {
                    showMainMessage(res.message, true);
                }
            })
            .fail(function() {
                showMainMessage('Lỗi kết nối khi lấy chi tiết phòng.', true);
            });
    });

    // 3. Xử lý Submit (Thêm & Sửa)
    form.on('submit', function(e) {
        e.preventDefault(); // Ngăn lỗi '?'
        
        const id = formId.val();
        let url = (id === '0' || id === '')
            ? BASE_URL + '/phong/ajax_create'  // Route Thêm
            : BASE_URL + '/phong/ajax_update/' + id; // Route Sửa

        const formData = $(this).serialize();

        $.post(url, formData)
            .done(function(res) {
                if (res.success) {
                    showMainMessage(res.message, false);
                    bootstrapModal.hide();
                    setTimeout(() => { location.reload(); }, 1000); // Tải lại trang
                } else {
                    modalMessage.html('<div class="alert alert-danger">' + res.message + '</div>');
                }
            })
            .fail(function() {
                modalMessage.html('<div class="alert alert-danger">Lỗi kết nối máy chủ.</div>');
            });
    });

    // 4. Xử lý Xóa
    tableBody.on('click', '.btn-delete', function() {
        const id = $(this).data('id');
        if (!confirm('Bạn có chắc chắn muốn xóa phòng này?')) {
            return;
        }

        // Dùng BASE_URL và Route Xóa
        $.post(BASE_URL + '/phong/ajax_delete/' + id, {method: 'POST'}) // Đảm bảo là POST
            .done(function(res) {
                if (res.success) {
                    showMainMessage(res.message, false);
                    $('#row-' + id).remove();
                } else {
                    // Hiển thị lỗi từ Trigger (ví dụ: Phòng đang được thuê)
                    showMainMessage(res.message, true);
                }
            })
            .fail(function() {
                showMainMessage('Lỗi kết nối khi xóa.', true);
            });
    });

    // --- CHỨC NĂNG TRA CỨU (SP1) ---
    
    // [SỬA LỖI] Gán sự kiện cho đúng ID form
    $('#formTraCuuPhong').on('submit', function(e) {
        e.preventDefault(); // Ngăn lỗi '?'
        
        const soPhong = $('#inputSoPhong').val().trim();
        const ketQuaDiv = $('#ketQuaTraCuu');

        if (!soPhong) {
            ketQuaDiv.html('<div class="alert alert-danger">Vui lòng nhập số phòng.</div>');
            return;
        }

        // [SỬA LỖI] Dùng đúng Route và BASE_URL
        const urlTraCuu = BASE_URL + '/phong/ajax_get_sv/' + soPhong;
        ketQuaDiv.html('<div class="alert alert-info">Đang tải...</div>');

        $.get(urlTraCuu)
            .done(function(response) {
                if (response.success) {
                    let html = '';
                    if (response.data && response.data.length > 0) {
                        html = `<h5>Kết quả cho phòng ${escapeHTML(soPhong)}:</h5>`;
                        html += '<table class="table table-bordered mt-2"><thead><tr><th>Mã SV</th><th>Họ Tên</th><th>Giới Tính</th><th>SĐT</th><th>Hạn HĐ</th></tr></thead><tbody>';
                        response.data.forEach(function(sv) {
                            html += '<tr>';
                            html += `<td>${sv.MaSV}</td>`;
                            html += `<td>${escapeHTML(sv.HoTen)}</td>`;
                            html += `<td>${sv.GioiTinh}</td>`;
                            html += `<td>${escapeHTML(sv.SoDienThoai || '')}</td>`;
                            html += `<td>${sv.NgayKetThuc}</td>`;
                            html += '</tr>';
                        });
                        html += '</tbody></table>';
                    } else {
                        html = `<div class="alert alert-warning">${response.message}</div>`;
                    }
                    ketQuaDiv.html(html);
                } else {
                    ketQuaDiv.html('<div class="alert alert-warning">' + response.message + '</div>');
                }
            })
            .fail(function() {
                ketQuaDiv.html('<div class="alert alert-danger">Lỗi kết nối khi tra cứu.</div>');
            });
    });

    // --- HÀM TIỆN ÍCH ---

    function showMainMessage(message, isError = false) {
        const type = isError ? 'danger' : 'success';
        mainMessage.html(`<div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${escapeHTML(message)}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>`);
    }

    // [SỬA LỖI SYNTAX ERROR]
    function escapeHTML(str) {
        if (str === null || str === undefined) return '';
        // Sửa lỗi '&#03C;' thành '&#039;'
        return str.toString().replace(/[&<>"']/g, m => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '\"': '&quot;', "'": '&#039;' }[m]));
    }
});
</script>
<?php
require_once __DIR__ . '/../components/footer.php';
?>