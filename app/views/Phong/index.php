<?php
// app/views/Phong/Index.php
// $data['phong_list'] (danh sách phòng)
// $data['loai_phong_list'] (danh sách loại phòng cho <select>)
?>

<h2>Quản Lý Phòng</h2>

<button id="btn-show-create-modal">Thêm Phòng Mới</button>
<hr>

<div id="main-message"></div>

<h3>Danh Sách Phòng Hiện Có</h3>
<table border="1" style="width: 100%;">
    <thead>
        <tr>
            <th>Số Phòng</th>
            <th>Loại Phòng</th>
            <th>Giá Thuê (VND/tháng)</th>
            <th>SL Tối Đa</th>
            <th>Tình Trạng</th>
            <th style="width: 150px;">Hành động</th>
        </tr>
    </thead>
    <tbody id="phong-table-body">
        <?php if (empty($phong_list)): ?>
            <tr id="row-empty">
                <td colspan="6">Chưa có phòng nào.</td>
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
                        <button class="btn-edit" data-id="<?php echo $p['MaPhong']; ?>">Sửa</button>
                        |
                        <button class="btn-delete" data-id="<?php echo $p['MaPhong']; ?>">Xóa</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<div id="phong-modal" class="modal">
    <div class="modal-content">
        <span class="modal-close-btn">&times;</span>
        
        <h3 id="modal-title">Thêm Phòng Mới</h3>
        
        <form id="phong-form">
            <input type="hidden" id="form-phong-id" name="id" value="0">

            <label for="maloai">Loại Phòng:</label><br>
            <select id="form-maloai" name="maloai" required style="width: 95%;">
                <option value="">-- Chọn loại phòng --</option>
                <?php foreach ($loai_phong_list as $lp): ?>
                    <option value="<?php echo $lp['MaLoaiPhong']; ?>">
                        <?php echo htmlspecialchars($lp['TenLoaiPhong']); ?> 
                        (<?php echo number_format($lp['GiaThue']); ?> VND)
                    </option>
                <?php endforeach; ?>
            </select><br><br>

            <label for="sophong">Số Phòng:</label><br>
            <input type="text" id="form-sophong" name="sophong" required style="width: 95%;"><br><br>

            <label for="slmax">Số Lượng Tối Đa:</label><br>
            <input type="number" id="form-slmax" name="slmax" required style="width: 95%;"><br><br>

            <button type="submit">Lưu Lại</button>
        </form>
        
        <div id="modal-message" style="margin-top: 10px;"></div>
    </div>
</div>

<style>
    .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.5); }
    .modal-content { background-color: #fefefe; margin: 15% auto; padding: 20px; border: 1px solid #888; width: 80%; max-width: 500px; position: relative; }
    .modal-close-btn { color: #aaa; float: right; font-size: 28px; font-weight: bold; position: absolute; top: 5px; right: 15px; }
    .modal-close-btn:hover, .modal-close-btn:focus { color: black; text-decoration: none; cursor: pointer; }
    .message-success { color: green; font-weight: bold; }
    .message-error { color: red; font-weight: bold; }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        // --- Lấy các đối tượng DOM ---
        const modal = document.getElementById('phong-modal');
        const btnShowCreate = document.getElementById('btn-show-create-modal');
        const btnCloseModal = modal.querySelector('.modal-close-btn');
        const form = document.getElementById('phong-form');
        const modalTitle = document.getElementById('modal-title');
        const modalMessage = document.getElementById('modal-message');
        const mainMessage = document.getElementById('main-message');
        const tableBody = document.getElementById('phong-table-body');
        
        // Form fields
        const formId = document.getElementById('form-phong-id');
        const formMaLoai = document.getElementById('form-maloai');
        const formSoPhong = document.getElementById('form-sophong');
        const formSlMax = document.getElementById('form-slmax');

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

        // --- Hàm reset form và mở modal cho 'Create' ---
        function openCreateModal() {
            form.reset(); 
            formId.value = '0'; 
            modalTitle.textContent = 'Thêm Phòng Mới';
            showModalMessage(''); 
            modal.style.display = 'block'; 
        }

        // --- Hàm lấy dữ liệu và mở modal cho 'Update' ---
        async function openUpdateModal(id) {
            try {
                const response = await fetch(`/phong/get/${id}`);
                const result = await response.json();

                if (result.success) {
                    form.reset();
                    // Điền dữ liệu vào form
                    formId.value = result.data.MaPhong;
                    formMaLoai.value = result.data.MaLoaiPhong; // Set giá trị cho <select>
                    formSoPhong.value = result.data.SoPhong;
                    formSlMax.value = result.data.SoLuongToiDa;
                    
                    modalTitle.textContent = 'Sửa Phòng';
                    showModalMessage('');
                    modal.style.display = 'block';
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

        // --- CÁC HÀM TIỆN ÍCH CHO BẢNG ---
        function createTableRow(p) { // p = rowData (phong)
            document.getElementById('row-empty')?.remove();
            
            // Hàm escapeHTML (định nghĩa ở dưới)
            return `
                <tr id="row-${p.MaPhong}">
                    <td>${escapeHTML(p.SoPhong)}</td>
                    <td>${escapeHTML(p.TenLoaiPhong)}</td>
                    <td>${new Intl.NumberFormat('vi-VN').format(p.GiaThue)}</td>
                    <td>${p.SoLuongToiDa}</td>
                    <td>${escapeHTML(p.TinhTrangPhong)}</td>
                    <td>
                        <button class="btn-edit" data-id="${p.MaPhong}">Sửa</button>
                        |
                        <button class="btn-delete" data-id="${p.MaPhong}">Xóa</button>
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
            tableBody.innerHTML = '<tr id="row-empty"><td colspan="6">Chưa có phòng nào.</td></tr>';
        }
        
        function escapeHTML(str) {
            if (str === null || str === undefined) return '';
            return str.toString().replace(/[&<>"']/g, function(m) {
                return {'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'}[m];
            });
        }

        // --- GÁN SỰ KIỆN (Event Listeners) ---
        btnShowCreate.addEventListener('click', openCreateModal);
        btnCloseModal.addEventListener('click', () => modal.style.display = 'none');
        window.addEventListener('click', (event) => {
            if (event.target == modal) modal.style.display = 'none';
        });
        form.addEventListener('submit', handleFormSubmit);

        tableBody.addEventListener('click', function(event) {
            const target = event.target;
            if (target.classList.contains('btn-edit')) {
                openUpdateModal(target.dataset.id);
            }
            if (target.classList.contains('btn-delete')) {
                handleDelete(target.dataset.id);
            }
        });

    });
</script>