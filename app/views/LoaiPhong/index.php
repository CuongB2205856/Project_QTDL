<h2>Quản Lý Loại Phong</h2>

<button id="btn-show-create-modal">Thêm Loại Phòng Mới</button>
<hr>

<div id="main-message"></div>

<h3>Danh Sách Loại Phòng Hiện Có</h3>
<table border="1" style="width: 100%;">
    <thead>
        <tr>
            <th>Mã</th>
            <th>Tên Loại</th>
            <th>Giá Thuê</th>
            <th style="width: 150px;">Hành động</th>
        </tr>
    </thead>
    <tbody id="loaiphong-table-body">
        <?php if (empty($loai_phong_list)): ?>
            <tr id="row-empty">
                <td colspan="4">Chưa có loại phòng nào.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($loai_phong_list as $lp): ?>
                <tr id="row-<?php echo $lp['MaLoaiPhong']; ?>">
                    <td><?php echo $lp['MaLoaiPhong']; ?></td>
                    <td><?php echo htmlspecialchars($lp['TenLoaiPhong']); ?></td>
                    <td><?php echo number_format($lp['GiaThue']); ?> VND</td>
                    <td>
                        <button class="btn-edit" data-id="<?php echo $lp['MaLoaiPhong']; ?>">Sửa</button>
                        |
                        <button class="btn-delete" data-id="<?php echo $lp['MaLoaiPhong']; ?>">Xóa</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<div id="loaiphong-modal" class="modal">
    <div class="modal-content">
        <span class="modal-close-btn">&times;</span>
        
        <h3 id="modal-title">Thêm Loại Phòng Mới</h3>
        
        <form id="loaiphong-form">
            <input type="hidden" id="form-loaiphong-id" name="id" value="0">

            <label for="tenloai">Tên Loại Phòng:</label><br>
            <input type="text" id="form-tenloai" name="tenloai" required style="width: 95%;"><br><br>

            <label for="giathue">Giá Thuê (VND/tháng):</label><br>
            <input type="number" id="form-giathue" name="giathue" required style="width: 95%;"><br><br>

            <button type="submit">Lưu Lại</button>
        </form>
        
        <div id="modal-message" style="margin-top: 10px;"></div>
    </div>
</div>

<style>
    /* CSS cho Modal */
    .modal {
        display: none; /* Ẩn mặc định */
        position: fixed; /* Ở yên tại chỗ */
        z-index: 1000; /* Hiển thị bên trên tất cả */
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto; /* Cho phép cuộn nếu nội dung dài */
        background-color: rgba(0,0,0,0.5); /* Nền mờ */
    }

    .modal-content {
        background-color: #fefefe;
        margin: 15% auto; /* Canh giữa theo chiều dọc và ngang */
        padding: 20px;
        border: 1px solid #888;
        width: 80%;
        max-width: 500px; /* Giới hạn chiều rộng */
        position: relative;
    }

    /* Nút X (close) */
    .modal-close-btn {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
        position: absolute;
        top: 5px;
        right: 15px;
    }

    .modal-close-btn:hover,
    .modal-close-btn:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }
    
    /* CSS cho thông báo */
    .message-success {
        color: green;
        font-weight: bold;
    }
    .message-error {
        color: red;
        font-weight: bold;
    }
</style>

<script>
    // Chờ cho toàn bộ trang được tải
    document.addEventListener('DOMContentLoaded', function() {

        // --- Lấy các đối tượng DOM ---
        const modal = document.getElementById('loaiphong-modal');
        const btnShowCreate = document.getElementById('btn-show-create-modal');
        const btnCloseModal = document.querySelector('.modal-close-btn');
        const form = document.getElementById('loaiphong-form');
        const modalTitle = document.getElementById('modal-title');
        const modalMessage = document.getElementById('modal-message');
        const mainMessage = document.getElementById('main-message');
        const tableBody = document.getElementById('loaiphong-table-body');
        
        // Input ẩn lưu ID
        const formId = document.getElementById('form-loaiphong-id');
        const formTenLoai = document.getElementById('form-tenloai');
        const formGiaThue = document.getElementById('form-giathue');

        // --- Hàm hiển thị thông báo ---
        function showModalMessage(message, isError = false) {
            modalMessage.textContent = message;
            modalMessage.className = isError ? 'message-error' : 'message-success';
        }
        
        function showMainMessage(message, isError = false) {
            mainMessage.textContent = message;
            mainMessage.className = isError ? 'message-error' : 'message-success';
            // Tự xóa thông báo sau 3 giây
            setTimeout(() => { mainMessage.textContent = ''; }, 3000);
        }

        // --- Hàm reset form và mở modal cho 'Create' ---
        function openCreateModal() {
            form.reset(); // Xóa sạch form
            formId.value = '0'; // Đặt ID về 0 (hoặc rỗng) để biết là 'create'
            modalTitle.textContent = 'Thêm Loại Phòng Mới';
            showModalMessage(''); // Xóa thông báo cũ
            modal.style.display = 'block'; // Hiển thị modal
        }

        // --- Hàm lấy dữ liệu và mở modal cho 'Update' ---
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
            event.preventDefault(); // Ngăn trang tải lại
            
            const id = formId.value;
            const formData = new FormData(form);
            
            let url = '';
            // Quyết định URL là Create hay Update
            if (id === '0' || id === '') {
                url = '/loaiphong/ajax_create';
            } else {
                url = `/loaiphong/ajax_update/${id}`;
            }

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
                        // Thêm hàng mới vào bảng
                        appendRowToTable(result.newRow);
                        // Xóa form để chuẩn bị thêm cái mới (như yêu cầu)
                        form.reset();
                        formId.value = '0';
                    } else {
                        // --- Xử lý CẬP NHẬT (Update) ---
                        // Cập nhật hàng trong bảng
                        updateRowInTable(result.updatedRow);
                        // Yêu cầu của bạn là không tự tắt,
                        // nên chúng ta giữ nguyên modal
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
            if (!confirm('Bạn có chắc chắn muốn xóa loại phòng này?')) {
                return;
            }

            try {
                // Dùng POST để xóa an toàn hơn
                const response = await fetch(`/loaiphong/ajax_delete/${id}`, {
                    method: 'POST' 
                });
                const result = await response.json();

                if (result.success) {
                    showMainMessage(result.message, false);
                    // Xóa hàng khỏi bảng
                    const row = document.getElementById(`row-${id}`);
                    if (row) {
                        row.remove();
                    }
                    // Kiểm tra xem bảng có rỗng không
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

        // Hàm tạo HTML cho 1 hàng
        function createTableRow(rowData) {
            // Xóa hàng "Rỗng" nếu có
            document.getElementById('row-empty')?.remove();

            return `
                <tr id="row-${rowData.MaLoaiPhong}">
                    <td>${rowData.MaLoaiPhong}</td>
                    <td>${escapeHTML(rowData.TenLoaiPhong)}</td>
                    <td>${new Intl.NumberFormat('vi-VN').format(rowData.GiaThue)} VND</td>
                    <td>
                        <button class="btn-edit" data-id="${rowData.MaLoaiPhong}">Sửa</button>
                        |
                        <button class="btn-delete" data-id="${rowData.MaLoaiPhong}">Xóa</button>
                    </td>
                </tr>
            `;
        }

        // Hàm thêm hàng mới vào bảng
        function appendRowToTable(rowData) {
            const rowHTML = createTableRow(rowData);
            // Thêm vào cuối bảng
            tableBody.insertAdjacentHTML('beforeend', rowHTML);
        }
        
        // Hàm cập nhật 1 hàng đã có
        function updateRowInTable(rowData) {
            const row = document.getElementById(`row-${rowData.MaLoaiPhong}`);
            if (row) {
                const rowHTML = createTableRow(rowData);
                // Thay thế hàng cũ bằng hàng mới
                row.outerHTML = rowHTML;
            }
        }
        
        // Hiển thị hàng "Chưa có dữ liệu"
        function showEmptyRow() {
            tableBody.innerHTML = '<tr id="row-empty"><td colspan="4">Chưa có loại phòng nào.</td></tr>';
        }
        
        // Hàm bảo mật cơ bản (tránh XSS)
        function escapeHTML(str) {
            return str.replace(/[&<>"']/g, function(m) {
                return {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#039;'
                }[m];
            });
        }


        // --- GÁN SỰ KIỆN (Event Listeners) ---

        // 1. Mở modal 'Create' khi nhấn nút "Thêm"
        btnShowCreate.addEventListener('click', openCreateModal);

        // 2. Đóng modal khi nhấn nút "X"
        btnCloseModal.addEventListener('click', function() {
            modal.style.display = 'none';
        });

        // 3. Đóng modal khi nhấn ra ngoài vùng
        window.addEventListener('click', function(event) {
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        });

        // 4. Xử lý Submit form
        form.addEventListener('submit', handleFormSubmit);

        // 5. Xử lý cho các nút "Sửa" và "Xóa" (dùng event delegation)
        // Vì các nút này có thể được thêm/xóa động
        tableBody.addEventListener('click', function(event) {
            const target = event.target;
            
            // Nếu nhấn nút "Sửa"
            if (target.classList.contains('btn-edit')) {
                const id = target.dataset.id;
                openUpdateModal(id);
            }
            
            // Nếu nhấn nút "Xóa"
            if (target.classList.contains('btn-delete')) {
                const id = target.dataset.id;
                handleDelete(id);
            }
        });

    });
</script>