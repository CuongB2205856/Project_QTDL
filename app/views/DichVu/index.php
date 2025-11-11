<?php
// app/views/DichVu/Index.php
// $data['dichvu_list']
?>

<h2>Quản Lý Dịch Vụ</h2>

<button id="btn-show-create-modal">Thêm Dịch Vụ Mới</button>
<hr>

<div id="main-message"></div>

<h3>Danh Sách Dịch Vụ Hiện Có</h3>
<table border="1" style="width: 100%;">
    <thead>
        <tr>
            <th>Mã DV</th>
            <th>Tên Dịch Vụ</th>
            <th>Đơn Giá</th>
            <th style="width: 150px;">Hành động</th>
        </tr>
    </thead>
    <tbody id="dichvu-table-body">
        <?php if (empty($dichvu_list)): ?>
            <tr id="row-empty">
                <td colspan="4">Chưa có dịch vụ nào.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($dichvu_list as $dv): ?>
                <tr id="row-<?php echo $dv['MaDV']; ?>">
                    <td><?php echo $dv['MaDV']; ?></td>
                    <td><?php echo htmlspecialchars($dv['TenDichVu']); ?></td>
                    <td><?php echo number_format($dv['DonGiaDichVu']); ?> VND</td>
                    <td>
                        <button class="btn-edit" data-id="<?php echo $dv['MaDV']; ?>">Sửa</button>
                        |
                        <button class="btn-delete" data-id="<?php echo $dv['MaDV']; ?>">Xóa</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<div id="dichvu-modal" class="modal">
    <div class="modal-content">
        <span class="modal-close-btn">&times;</span>
        
        <h3 id="modal-title">Thêm Dịch Vụ Mới</h3>
        
        <form id="dichvu-form">
            <input type="hidden" id="form-dichvu-id" name="id" value="0">

            <label for="tendv">Tên Dịch Vụ:</label><br>
            <input type="text" id="form-tendv" name="tendv" required style="width: 95%;" 
                   placeholder="Ví dụ: Điện (VND/kWh) hoặc Gửi xe (VND/tháng)"><br><br>

            <label for="dongia">Đơn Giá (VND):</label><br>
            <input type="number" id="form-dongia" name="dongia" required style="width: 95%;"><br><br>

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
        const modal = document.getElementById('dichvu-modal');
        const btnShowCreate = document.getElementById('btn-show-create-modal');
        const btnCloseModal = modal.querySelector('.modal-close-btn');
        const form = document.getElementById('dichvu-form');
        const modalTitle = document.getElementById('modal-title');
        const modalMessage = document.getElementById('modal-message');
        const mainMessage = document.getElementById('main-message');
        const tableBody = document.getElementById('dichvu-table-body');
        
        // Form fields
        const formId = document.getElementById('form-dichvu-id');
        const formTenDV = document.getElementById('form-tendv');
        const formDonGia = document.getElementById('form-dongia');

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
            modalTitle.textContent = 'Thêm Dịch Vụ Mới';
            showModalMessage(''); 
            modal.style.display = 'block'; 
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
        function createTableRow(dv) { // dv = rowData (dichvu)
            document.getElementById('row-empty')?.remove();
            
            return `
                <tr id="row-${dv.MaDV}">
                    <td>${dv.MaDV}</td>
                    <td>${escapeHTML(dv.TenDichVu)}</td>
                    <td>${new Intl.NumberFormat('vi-VN').format(dv.DonGiaDichVu)} VND</td>
                    <td>
                        <button class="btn-edit" data-id="${dv.MaDV}">Sửa</button>
                        |
                        <button class="btn-delete" data-id="${dv.MaDV}">Xóa</button>
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
            tableBody.innerHTML = '<tr id="row-empty"><td colspan="4">Chưa có dịch vụ nào.</td></tr>';
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