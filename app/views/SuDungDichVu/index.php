<?php
// Gán tiêu đề và route
$title = 'Quản lý Sử Dụng Dịch Vụ';
$currentRoute = '/sddv';

// Tải header
require_once __DIR__ . '/../components/header.php';
?>

<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h2 class="bi me-2">Quản lý Sử Dụng Dịch Vụ</h2>
        </div>
        <div>
            <button id="btn-show-create-modal" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Thêm Dịch Vụ
            </button>
        </div>
    </div>
</div>

<div id="main-message"></div>

<div class="card">
    <div class="card-header">
        Danh Sách Dịch Vụ Đã Nhập
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th scope="col">STT</th>
                        <th scope="col">Sinh Viên</th>
                        <th scope="col">Phòng</th>
                        <th scope="col">Dịch Vụ</th>
                        <th scope="col">Số Lượng</th>
                        <th scope="col">Tháng/Năm</th>
                        <th scope="col">Hành Động</th>
                    </tr>
                </thead>
                <tbody id="sddv-table-body">
                    <?php if (empty($sddv_list)): ?>
                        <tr id="row-empty">
                            <td colspan="7" class="text-center">Chưa có dữ liệu sử dụng dịch vụ.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($sddv_list as $item): ?>
                            <tr id="row-<?php echo $item['MaSDDV']; ?>">
                                <td><?php echo e($item['MaSDDV']); ?></td>
                                <td><?php echo e($item['HoTen']); ?></td>
                                <td><?php echo e($item['SoPhong']); ?></td>
                                <td><?php echo e($item['TenDichVu']); ?></td>
                                <td><?php echo e($item['SoLuongSuDung']); ?></td>
                                <td><?php echo e($item['ThangSuDungDV'] . '/' . $item['NamSuDungDV']); ?></td>
                                <td>
                                    <button class="btn btn-sm btn-warning btn-edit" data-id="<?php echo $item['MaSDDV']; ?>" title="Sửa">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger btn-delete" data-id="<?php echo $item['MaSDDV']; ?>" title="Xóa">
                                        <i class="bi bi-trash"></i>
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

<div class="modal fade" id="sddvModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">Thêm Dịch Vụ Sử Dụng</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="sddvForm">
                    <input type="hidden" id="MaSDDV" name="MaSDDV">
                    <div id="modal-message"></div>

                    <div class="mb-3">
                        <label for="MaHD" class="form-label">Hợp đồng (SV - Phòng)</label>
                        <select id="MaHD" name="MaHD" class="form-select" required>
                            <option value="">-- Chọn hợp đồng --</option>
                            <?php foreach ($hopdong_list_active as $hd): ?>
                                <option value="<?php echo $hd['MaHD']; ?>">
                                    <?php echo e($hd['HoTen'] . ' - ' . $hd['SoPhong']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="MaDV" class="form-label">Loại Dịch Vụ</label>
                        <select id="MaDV" name="MaDV" class="form-select" required>
                            <option value="">-- Chọn dịch vụ --</option>
                            <?php foreach ($dichvu_list_all as $dv): ?>
                                
                                    <option value="<?php echo $dv['MaDV']; ?>">
                                        <?php echo e($dv['TenDichVu']); ?>
                                    </option>
                                
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="SoLuongSuDung" class="form-label">Số Lượng</label>
                        <input type="number" class="form-control" id="SoLuongSuDung" name="SoLuongSuDung" min="0" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="ThangSuDungDV" class="form-label">Tháng</label>
                            <input type="number" class="form-control" id="ThangSuDungDV" name="ThangSuDungDV" min="1" max="12" value="<?php echo date('m'); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="NamSuDungDV" class="form-label">Năm</label>
                            <input type="number" class="form-control" id="NamSuDungDV" name="NamSuDungDV" value="<?php echo date('Y'); ?>" required>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn btn-primary">Lưu</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<?php require_once __DIR__ . '/../components/footer.php'; ?>

<script>
$(document).ready(function() {
    const sddvModal = new bootstrap.Modal(document.getElementById('sddvModal'));
    const modal = document.getElementById('sddvModal');
    const form = $('#sddvForm');
    const modalLabel = $('#modalLabel');
    const mainMessage = $('#main-message');
    const modalMessage = $('#modal-message');

    let currentEditId = null;

    // 1. Mở Modal Thêm
    $('#btn-show-create-modal').on('click', function() {
        currentEditId = null;
        form.trigger('reset');
        modalLabel.text('Thêm Dịch Vụ Sử Dụng');
        $('#MaSDDV').val('');
        
        // Cho phép sửa các trường chính
        $('#MaHD').prop('disabled', false);
        $('#MaDV').prop('disabled', false);
        $('#ThangSuDungDV').prop('disabled', false);
        $('#NamSuDungDV').prop('disabled', false);
        
        modalMessage.html('');
        sddvModal.show();
    });

    // 2. Mở Modal Sửa
    $('#sddv-table-body').on('click', '.btn-edit', function() {
        currentEditId = $(this).data('id');
        modalLabel.text('Cập nhật Số Lượng Dịch Vụ');
        form.trigger('reset');
        modalMessage.html('');

        // Lấy dữ liệu chi tiết
        $.get(BASE_URL + '/sddv/get/' + currentEditId)
            .done(function(res) {
                if (res.success) {
                    $('#MaSDDV').val(res.data.MaSDDV);
                    $('#MaHD').val(res.data.MaHD).prop('disabled', true);
                    $('#MaDV').val(res.data.MaDV).prop('disabled', true);
                    $('#SoLuongSuDung').val(res.data.SoLuongSuDung);
                    $('#ThangSuDungDV').val(res.data.ThangSuDungDV).prop('disabled', true);
                    $('#NamSuDungDV').val(res.data.NamSuDungDV).prop('disabled', true);
                    sddvModal.show();
                } else {
                    alert(res.message);
                }
            });
    });

    // 3. Xử lý Submit Form (Cả Thêm và Sửa)
    form.on('submit', function(e) {
        e.preventDefault();
        
        let url = '';
        let data = {};

        if (currentEditId) {
            // Sửa
            url = BASE_URL + '/sddv/ajax_update/' + currentEditId;
            data = { SoLuongSuDung: $('#SoLuongSuDung').val() }; // Chỉ gửi số lượng
        } else {
            // Thêm mới
            url = BASE_URL + '/sddv/ajax_create'; //
            data = form.serialize();
        }

        $.post(url, data)
            .done(function(res) {
                if (res.success) {
                    sddvModal.hide();
                    mainMessage.html('<div class="alert alert-success">' + res.message + '</div>');
                    // Tải lại trang để thấy thay đổi
                    setTimeout(() => location.reload(), 1000); 
                } else {
                    modalMessage.html('<div class="alert alert-danger">' + res.message + '</div>');
                }
            })
            .fail(function() {
                modalMessage.html('<div class="alert alert-danger">Lỗi kết nối máy chủ.</div>');
            });
    });

    // 4. Xử lý Xóa
    $('#sddv-table-body').on('click', '.btn-delete', function() {
        const id = $(this).data('id');
        if (confirm('Bạn có chắc muốn xóa dịch vụ này? Hành động này có thể thất bại nếu đã có hóa đơn liên quan.')) {
            $.post(BASE_URL + '/sddv/ajax_delete/' + id)
                .done(function(res) {
                    if (res.success) {
                        $('#row-' + id).remove();
                        mainMessage.html('<div class="alert alert-success">' + res.message + '</div>');
                    } else {
                        mainMessage.html('<div class="alert alert-danger">' + res.message + '</div>');
                    }
                })
                .fail(function() {
                    mainMessage.html('<div class="alert alert-danger">Lỗi kết nối máy chủ.</div>');
                });
        }
    });

});
</script>