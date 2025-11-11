<?php
// app/views/SinhVien/Index.php
// $data['sinhvien_list']
?>

<h2>Quản Lý Sinh Viên</h2>

<div id="main-message"></div>

<h3>Danh Sách Sinh Viên</h3>
<table border="1" style="width: 100%;">
    <thead>
        <tr>
            <th>Mã SV</th>
            <th>Họ Tên</th>
            <th>Giới Tính</th>
            <th>Số Điện Thoại</th>
            <th>Phòng Đang Ở</th>
            <th>Tình Trạng Tiền</th>
            <th style="width: 250px;">Hành động</th>
        </tr>
    </thead>
    <tbody id="sv-table-body">
        <?php if (empty($sinhvien_list)): ?>
            <tr id="row-empty">
                <td colspan="7">Chưa có sinh viên nào.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($sinhvien_list as $sv): ?>
                <?php $maSV_html = htmlspecialchars($sv['MaSV']); ?>
                <tr id="row-<?php echo $maSV_html; ?>">
                    <td><?php echo $maSV_html; ?></td>
                    <td><?php echo htmlspecialchars($sv['HoTen']); ?></td>
                    <td><?php echo htmlspecialchars($sv['GioiTinh']); ?></td>
                    <td><?php echo htmlspecialchars($sv['SoDienThoai']); ?></td>
                    <td><?php echo htmlspecialchars($sv['SoPhong'] ?? 'Chưa có'); ?></td>
                    <td>
                        <?php 
                            $status = htmlspecialchars($sv['TinhTrangDongTien']);
                            $color = ($status == 'Quá hạn') ? 'red' : 'inherit';
                            echo "<span style='color: $color;'>$status</span>";
                        ?>
                    </td>
                    <td>
                        <button class="btn-details" data-id="<?php echo $maSV_html; ?>">Chi Tiết</button>
                        |
                        <button class="btn-edit" data-id="<?php echo $maSV_html; ?>">Sửa</button>
                        |
                        <button class="btn-delete" data-id="<?php echo $maSV_html; ?>">Xóa</button>
                        |
                        <button class="btn-reset-pass" data-id="<?php echo $maSV_html; ?>">Reset MK</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<div id="sv-modal" class="modal">
    <div class="modal-content">
        <span class="modal-close-btn">&times;</span>
        
        <h3 id="modal-title">Sửa Thông Tin Sinh Viên</h3>
        
        <form id="sv-form">
            <label>Mã SV:</label><br>
            <input type="text" id="form-sv-masv" name="masv" readonly style="width: 95%; background: #eee;"><br><br>

            <label for="form-hoten">Họ Tên:</label><br>
            <input type="text" id="form-hoten" name="hoten" required style="width: 95%;"><br><br>

            <label for="form-gioitinh">Giới Tính:</label><br>
            <select id="form-gioitinh" name="gioitinh" style="width: 95%;">
                <option value="Nam">Nam</option>
                <option value="Nữ">Nữ</option>
                <option value="Khác">Khác</option>
            </select><br><br>

            <label for="form-sdt">Số Điện Thoại:</label><br>
            <input type="text" id="form-sdt" name="sdt" style="width: 95%;"><br><br>

            <button type="submit">Lưu Lại</button>
        </form>
        
        <div id="modal-message" style="margin-top: 10px;"></div>
    </div>
</div>

<div id="details-modal" class="modal">
     <div class="modal-content">
        <span class="modal-close-btn details-close-btn">&times;</span>
        <h3>Chi Tiết Phòng Ở</h3>
        <div id="details-content">
            </div>
    </div>
</div>


<style>
    /* */
    .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.5); }
    .modal-content { background-color: #fefefe; margin: 15% auto; padding: 20px; border: 1px solid #888; width: 80%; max-width: 500px; position: relative; }
    .modal-close-btn { color: #aaa; float: right; font-size: 28px; font-weight: bold; position: absolute; top: 5px; right: 15px; }
    .modal-close-btn:hover, .modal-close-btn:focus { color: black; text-decoration: none; cursor: pointer; }
    .message-success { color: green; font-weight: bold; }
    .message-error { color: red; font-weight: bold; }
</style>

<script>
    // Toàn bộ JS này dựa trên logic của app/views/Phong/index.php
    document.addEventListener('DOMContentLoaded', function() {

        // --- Lấy các đối tượng DOM ---
        const modal = document.getElementById('sv-modal');
        const btnCloseModal = modal.querySelector('.modal-close-btn');
        const form = document.getElementById('sv-form');
        const modalTitle = document.getElementById('modal-title');
        const modalMessage = document.getElementById('modal-message');
        const mainMessage = document.getElementById('main-message');
        const tableBody = document.getElementById('sv-table-body');
        
        // Modal Chi tiết
        const detailsModal = document.getElementById('details-modal');
        const btnCloseDetailsModal = detailsModal.querySelector('.details-close-btn');
        const detailsContent = document.getElementById('details-content');

        // Form fields
        const formMaSV = document.getElementById('form-sv-masv');
        const formHoTen = document.getElementById('form-hoten');
        const formGioiTinh = document.getElementById('form-gioitinh');
        const formSdt = document.getElementById('form-sdt');

        // --- Hàm hiển thị thông báo ---
        function showModalMessage(message, isError = false) {
            modalMessage.textContent = message;
            modalMessage.className = isError ? 'message-error' : 'message-success';
        }
        function showMainMessage(message, isError = false) {
            mainMessage.textContent = message;
            mainMessage.className = isError ? 'message-error' : 'message-success';
            setTimeout(() => { mainMessage.textContent = ''; }, 3000);
        }

        // --- Hàm lấy dữ liệu và mở modal cho 'Update' ---
        async function openUpdateModal(maSV) {
            try {
                const response = await fetch(`/sinhvien/get/${maSV}`);
                const result = await response.json();

                if (result.success) {
                    form.reset();
                    // Điền dữ liệu vào form
                    formMaSV.value = result.data.MaSV;
                    formHoTen.value = result.data.HoTen;
                    formGioiTinh.value = result.data.GioiTinh;
                    formSdt.value = result.data.SoDienThoai;
                    
                    modalTitle.textContent = 'Sửa Thông Tin Sinh Viên';
                    showModalMessage('');
                    modal.style.display = 'block';
                } else {
                    showMainMessage(result.message, true);
                }
            } catch (error) {
                showMainMessage('Lỗi kết nối: ' + error.message, true);
            }
        }
        
        // --- Hàm lấy và hiển thị chi tiết phòng ở ---
        async function showRoomDetails(maSV) {
             try {
                const response = await fetch(`/sinhvien/ajax_get_room_details/${maSV}`);
                const result = await response.json();
                
                let content = '';
                if (result.success) {
                    const d = result.data;
                    content = `
                        <p><strong>Họ Tên:</strong> ${escapeHTML(d.HoTen)}</p>
                        <p><strong>Mã SV:</strong> ${escapeHTML(d.MaSV)}</p>
                        <hr>
                        <p><strong>Số Phòng:</strong> ${escapeHTML(d.SoPhong)}</p>
                        <p><strong>Loại Phòng:</strong> ${escapeHTML(d.TenLoaiPhong)}</p>
                        <p><strong>Giá Thuê:</strong> ${new Intl.NumberFormat('vi-VN').format(d.GiaThue)} VND/tháng</p>
                        <p><strong>Ngày Bắt Đầu:</strong> ${d.NgayBatDau}</p>
                        <p><strong>Ngày Kết Thúc:</strong> ${d.NgayKetThuc}</p>
                    `;
                } else {
                    content = `<p class="message-error">${result.message}</p>`;
                }
                detailsContent.innerHTML = content;
                detailsModal.style.display = 'block';
                
            } catch (error) {
                detailsContent.innerHTML = `<p class="message-error">Lỗi kết nối: ${error.message}</p>`;
                detailsModal.style.display = 'block';
            }
        }

        // --- Hàm xử lý Submit (Chỉ Update) ---
        async function handleFormSubmit(event) {
            event.preventDefault(); 
            
            const maSV = formMaSV.value;
            const formData = new FormData(form);
            let url = `/sinhvien/ajax_update/${maSV}`;

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();

                if (result.success) {
                    showModalMessage(result.message, false);
                    // Cập nhật dữ liệu trên bảng
                    updateRowInTable(result.updatedRowData);
                } else {
                    showModalMessage(result.message, true);
                }
            } catch (error) {
                showModalMessage('Lỗi kết nối: ' + error.message, true);
            }
        }
        
        // --- Hàm xử lý Xóa (Delete) ---
        async function handleDelete(maSV) {
            if (!confirm(`Bạn có chắc chắn muốn xóa sinh viên [${maSV}]? Thao tác này KHÔNG thể hoàn tác.`)) {
                return;
            }

            try {
                const response = await fetch(`/sinhvien/ajax_delete/${maSV}`, {
                    method: 'POST' 
                });
                const result = await response.json();

                if (result.success) {
                    showMainMessage(result.message, false);
                    document.getElementById(`row-${maSV}`)?.remove();
                    if (tableBody.rows.length === 0) {
                        showEmptyRow();
                    }
                } else {
                    showMainMessage(result.message, true);
                }
            } catch (error) {
                showMainMessage('Lỗi kết nối: ' + error.message, true);
            }
        }
        
        // --- Hàm xử lý Reset Mật Khẩu ---
        async function handleResetPass(maSV) {
            if (!confirm(`Bạn có chắc chắn muốn ĐẶT LẠI MẬT KHẨU cho sinh viên [${maSV}]?`)) {
                return;
            }

            try {
                const response = await fetch(`/sinhvien/ajax_reset_password/${maSV}`, {
                    method: 'POST' 
                });
                const result = await response.json();

                if (result.success) {
                    showMainMessage(result.message, false);
                } else {
                    showMainMessage(result.message, true);
                }
            } catch (error) {
                showMainMessage('Lỗi kết nối: ' + error.message, true);
            }
        }


        // --- CÁC HÀM TIỆN ÍCH CHO BẢNG ---
        function updateRowInTable(sv) {
            const row = document.getElementById(`row-${sv.MaSV}`);
            if (row) {
                // Cập nhật các cell
                row.cells[1].textContent = escapeHTML(sv.HoTen);
                row.cells[2].textContent = escapeHTML(sv.GioiTinh);
                row.cells[3].textContent = escapeHTML(sv.SoDienThoai);
                // (Không cập nhật phòng ở, tình trạng tiền vì findById không có)
            }
        }
        
        function showEmptyRow() {
            tableBody.innerHTML = '<tr id="row-empty"><td colspan="7">Chưa có sinh viên nào.</td></tr>';
        }
        
        function escapeHTML(str) {
            if (str === null || str === undefined) return '';
            return str.toString().replace(/[&<>"']/g, function(m) {
                return {'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'}[m];
            });
        }

        // --- GÁN SỰ KIỆN (Event Listeners) ---
        btnCloseModal.addEventListener('click', () => modal.style.display = 'none');
        btnCloseDetailsModal.addEventListener('click', () => detailsModal.style.display = 'none');
        
        window.addEventListener('click', (event) => {
            if (event.target == modal) modal.style.display = 'none';
            if (event.target == detailsModal) detailsModal.style.display = 'none';
        });
        
        form.addEventListener('submit', handleFormSubmit);

        tableBody.addEventListener('click', function(event) {
            const target = event.target;
            // Dùng .closest() để lấy data-id ngay cả khi click vào icon bên trong
            const button = target.closest('button'); 
            if (!button) return;

            const maSV = button.dataset.id;
            if (!maSV) return;

            if (button.classList.contains('btn-edit')) {
                openUpdateModal(maSV);
            }
            if (button.classList.contains('btn-delete')) {
                handleDelete(maSV);
            }
            if (button.classList.contains('btn-details')) {
                showRoomDetails(maSV);
            }
            if (button.classList.contains('btn-reset-pass')) {
                handleResetPass(maSV);
            }
        });

    });
</script>