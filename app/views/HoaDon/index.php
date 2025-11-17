<?php
$title = 'Quản lý Hóa Đơn';
$currentRoute = '/hoadon'; // Cập nhật route này

require_once __DIR__ . '/../components/header.php';

// Gán giá trị mặc định cho tháng/năm
$currentMonth = date('m');
$currentYear = date('Y');
?>

<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h2 class="bi me-2">Quản Lý Hóa Đơn</h2>
        </div>
        <div>
            <button id="btn-show-create-modal" class="btn btn-primary" hidden>
                <i class="bi bi-plus-lg"></i> Lập Hóa Đơn Mới
            </button>
        </div>
    </div>
</div>

<div id="main-message"></div>

<div class="card">
    <div class="card-header">
        Danh Sách Hóa Đơn
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th scope="col">Mã HĐ</th>
                        <th scope="col">Sinh Viên</th>
                        <th scope="col">Phòng</th>
                        <th scope="col">Dịch Vụ</th>
                        <th scope="col">Tháng/Năm</th>
                        <th scope="col">Tổng Tiền</th>
                        <th scope="col">Trạng Thái</th>
                        <th scope="col" style="min-width: 200px;">Hành động</th>
                    </tr>
                </thead>
                <tbody id="hoadon-table-body">
                    <?php if (empty($hoadon_list)): ?>
                        <tr id="row-empty">
                            <td colspan="8" class="text-center">Chưa có hóa đơn nào.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($hoadon_list as $hd): ?>
                            <?php echo view_hoadon_row($hd); // Dùng hàm helper bên dưới ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="hoadonModal" tabindex="-1" aria-labelledby="hoadonModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-title">Lập Hóa Đơn Mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="hoadon-form">
                <div class="modal-body">
                    <div id="modal-message"></div>

                    <input type="hidden" id="form-hoadon-id" name="id" value="0">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="form-mahd" class="form-label">Hợp đồng (Sinh viên - Phòng):</label>
                            <select class="form-select" id="form-mahd" name="mahd" required>
                                <option value="" disabled selected>-- Chọn hợp đồng --</option>
                                <?php foreach ($hopdong_list as $hd): ?>
                                    <option value="<?php echo $hd['MaHD']; ?>">
                                        <?php echo htmlspecialchars($hd['HoTen'] . ' - P.' . $hd['SoPhong']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="form-madv" class="form-label">Dịch Vụ:</label>
                            <select class="form-select" id="form-madv" name="madv" required>
                                <option value="" disabled selected>-- Chọn dịch vụ --</option>
                                <?php foreach ($dichvu_list as $dv): ?>
                                    <option value="<?php echo $dv['MaDV']; ?>" <?php if($dv['MaDV'] == 6) echo 'data-is-room-fee="true"'; ?>>
                                        <?php echo htmlspecialchars($dv['TenDichVu']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3" id="group-soluong">
                            <label for="form-soluong" class="form-label">Số Lượng:</label>
                            <input type="number" class="form-control" id="form-soluong" name="soluong" value="1" min="1" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="form-thang" class="form-label">Tháng:</label>
                            <input type="number" class="form-control" id="form-thang" name="thang" 
                                   value="<?php echo $currentMonth; ?>" min="1" max="12" required>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="form-nam" class="form-label">Năm:</label>
                            <input type="number" class="form-control" id="form-nam" name="nam" 
                                   value="<?php echo $currentYear; ?>" min="2020" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="form-ngaylap" class="form-label">Ngày Lập Hóa Đơn:</label>
                            <input type="date" class="form-control" id="form-ngaylap" name="ngaylap" 
                                   value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="form-ngayhethan" class="form-label">Ngày Hết Hạn:</label>
                            <input type="date" class="form-control" id="form-ngayhethan" name="ngayhethan" 
                                   value="<?php echo date('Y-m-d', strtotime('+7 days')); ?>" required>
                        </div>
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

<?php
/**
 * Hàm PHP helper để render 1 dòng của bảng hóa đơn
 * (Đặt ở đây cho tiện, hoặc bạn có thể chuyển vào functions.php)
 */
function view_hoadon_row($hd) {
    $trangthai_text = htmlspecialchars($hd['TrangThaiThanhToan']);
    $trangthai_class = ($trangthai_text == 'Đã thanh toán') ? 'badge bg-success' : 'badge bg-warning';

    $button_thanhtoan = ($trangthai_text == 'Chưa thanh toán') 
        ? '<button class="btn btn-success btn-sm btn-mark-paid" data-id="'. $hd['MaHoaDon'] .'">
               <i class="bi bi-check-lg"></i> Thanh Toán
           </button>' 
        : '';

    return '
        <tr id="row-'. $hd['MaHoaDon'] .'">
            <td>'. $hd['MaHoaDon'] .'</td>
            <td>'. htmlspecialchars($hd['TenSinhVien']) .'</td>
            <td>'. htmlspecialchars($hd['SoPhong']) .'</td>
            <td>'. htmlspecialchars($hd['TenDichVu']) .'</td>
            <td>'. sprintf('%02d', $hd['ThangSuDungDV']) .'/'. $hd['NamSuDungDV'] .'</td>
            <td>'. number_format($hd['TongTienThanhToan']) .' VND</td>
            <td><span class="'. $trangthai_class .'">'. $trangthai_text .'</span></td>
            <td>
                '. $button_thanhtoan .'
                <button class="btn btn-danger btn-sm btn-delete" data-id="'. $hd['MaHoaDon'] .'">
                    <i class="bi bi-trash"></i> Xóa
                </button>
            </td>
        </tr>
    ';
}
?>


<script>
    document.addEventListener('DOMContentLoaded', function () {

        // Lấy các đối tượng DOM
        const btnShowCreate = document.getElementById('btn-show-create-modal');
        const form = document.getElementById('hoadon-form');
        const modalTitle = document.getElementById('modal-title');
        const modalMessage = document.getElementById('modal-message');
        const mainMessage = document.getElementById('main-message');
        const tableBody = document.getElementById('hoadon-table-body');

        // Khởi tạo Bootstrap Modal
        const modalElement = document.getElementById('hoadonModal');
        const bootstrapModal = new bootstrap.Modal(modalElement);

        // Form fields
        const formId = document.getElementById('form-hoadon-id');
        const formMaDV = document.getElementById('form-madv');
        const groupSoLuong = document.getElementById('group-soluong');
        const inputSoLuong = document.getElementById('form-soluong');

        // Hàm hiển thị thông báo
        function showModalMessage(message, isError = false) {
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

        // Hàm reset form và mở modal cho 'Create'
        function openCreateModal() {
            form.reset();
            formId.value = '0'; // Luôn là tạo mới
            modalTitle.textContent = 'Lập Hóa Đơn Mới';
            showModalMessage('');
            
            // Set lại giá trị default cho tháng/năm/ngày
            document.getElementById('form-thang').value = '<?php echo $currentMonth; ?>';
            document.getElementById('form-nam').value = '<?php echo $currentYear; ?>';
            document.getElementById('form-ngaylap').value = '<?php echo date('Y-m-d'); ?>';
            document.getElementById('form-ngayhethan').value = '<?php echo date('Y-m-d', strtotime('+7 days')); ?>';
            
            // Reset logic ẩn/hiện
            groupSoLuong.style.display = 'block';
            inputSoLuong.required = true;

            bootstrapModal.show();
        }
        
        // === LOGIC NGHIỆP VỤ (JS): Ẩn/Hiện ô Số Lượng ===
        formMaDV.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            // Kiểm tra data attribute 'data-is-room-fee' (đã gán ở MaDV=6)
            if (selectedOption.dataset.isRoomFee === 'true') {
                groupSoLuong.style.display = 'none';
                inputSoLuong.required = false; // Không bắt buộc nhập
                inputSoLuong.value = 1; // Tự gán giá trị 1
            } else {
                groupSoLuong.style.display = 'block';
                inputSoLuong.required = true; // Bắt buộc nhập
            }
        });


        // Hàm xử lý Submit (Chỉ xử lý Create)
        async function handleFormSubmit(event) {
            event.preventDefault();

            const formData = new FormData(form);
            const url = '/hoadon/ajax_create'; // Chỉ có tạo mới

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();

                if (result.success) {
                    showModalMessage(result.message, false);
                    appendRowToTable(result.newRow); // Thêm dòng mới vào bảng
                    form.reset(); // Reset form
                    // Tự động đóng modal sau 1s
                    setTimeout(() => {
                         bootstrapModal.hide();
                    }, 1000);
                } else {
                    showModalMessage(result.message, true);
                }
            } catch (error) {
                showModalMessage('Lỗi kết nối: ' + error.message, true);
            }
        }

        // Hàm xử lý Xóa (Delete)
        async function handleDelete(id) {
            if (!confirm('Bạn có chắc chắn muốn xóa hóa đơn này?\nHành động này KHÔNG THỂ hoàn tác.')) {
                return;
            }

            try {
                const response = await fetch(`/hoadon/ajax_delete/${id}`, {
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
        
        // Hàm xử lý "Đánh dấu đã thanh toán"
        async function handleMarkPaid(id) {
            if (!confirm('Xác nhận thanh toán cho hóa đơn #' + id + '?')) {
                return;
            }
            
            const formData = new FormData();
            formData.append('status', 'Đã thanh toán');

            try {
                const response = await fetch(`/hoadon/ajax_update_status/${id}`, {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();

                if (result.success) {
                    showMainMessage(result.message, false);
                    updateRowInTable(result.updatedRow); // Cập nhật lại dòng
                } else {
                    showMainMessage(result.message, true);
                }
            } catch (error) {
                showMainMessage('Lỗi kết nối: ' + error.message, true);
            }
        }


        // CÁC HÀM TIỆN ÍCH CHO BẢNG
        // (Sử dụng hàm PHP helper từ server để render)
        const phpHelperFunction = <?php echo json_encode(function_exists('view_hoadon_row')); ?>;

        function createTableRow(rowData) {
            document.getElementById('row-empty')?.remove();
            
            // Chúng ta không thể gọi hàm PHP 'view_hoadon_row' từ JS.
            // Vì vậy, chúng ta phải render thủ công ở JS,
            // HOẶC, chúng ta sẽ gọi fetch() để lấy HTML của dòng mới.
            // Để đơn giản, chúng ta sẽ render thủ công ở JS:

            const trangthai_text = escapeHTML(rowData.TrangThaiThanhToan);
            const trangthai_class = (trangthai_text === 'Đã thanh toán') ? 'badge bg-success' : 'badge bg-warning';

            const button_thanhtoan = (trangthai_text === 'Chưa thanh toán')
                ? `<button class="btn btn-success btn-sm btn-mark-paid" data-id="${rowData.MaHoaDon}">
                       <i class="bi bi-check-lg"></i> Thanh Toán
                   </button>`
                : '';
                
            const thang = String(rowData.ThangSuDungDV).padStart(2, '0');
            const tongtien = new Intl.NumberFormat('vi-VN').format(rowData.TongTienThanhToan);

            return `
                <tr id="row-${rowData.MaHoaDon}">
                    <td>${rowData.MaHoaDon}</td>
                    <td>${escapeHTML(rowData.TenSinhVien)}</td>
                    <td>${escapeHTML(rowData.SoPhong)}</td>
                    <td>${escapeHTML(rowData.TenDichVu)}</td>
                    <td>${thang}/${rowData.NamSuDungDV}</td>
                    <td>${tongtien} VND</td>
                    <td><span class="${trangthai_class}">${trangthai_text}</span></td>
                    <td>
                        ${button_thanhtoan}
                        <button class="btn btn-danger btn-sm btn-delete" data-id="${rowData.MaHoaDon}">
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
            const row = document.getElementById(`row-${rowData.MaHoaDon}`);
            if (row) {
                row.outerHTML = createTableRow(rowData);
            }
        }

        function showEmptyRow() {
            tableBody.innerHTML = '<tr id="row-empty"><td colspan="8" class="text-center">Chưa có hóa đơn nào.</td></tr>';
        }

        function escapeHTML(str) {
            if (str === null || str === undefined) return '';
            return str.toString().replace(/[&<>"']/g, m => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '\"': '&quot;', "'": '&#039;' }[m]));
        }

        // GÁN SỰ KIỆN (Event Listeners)
        btnShowCreate.addEventListener('click', openCreateModal);
        form.addEventListener('submit', handleFormSubmit);

        tableBody.addEventListener('click', function (event) {
            const target = event.target.closest('button');
            if (!target) return;

            const id = target.dataset.id;

            if (target.classList.contains('btn-delete')) {
                handleDelete(id);
            }
            if (target.classList.contains('btn-mark-paid')) {
                handleMarkPaid(id);
            }
        });

    });
</script>

<?php
require_once __DIR__ . '/../components/footer.php';
?>