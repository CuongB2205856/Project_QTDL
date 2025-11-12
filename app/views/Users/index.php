<?php
// 1. Set các biến cho header
$title = 'Quản lý Users'; 
$currentRoute = '/users'; // Quan trọng: để active link sidebar

// 2. Gọi Header (Mở <html>, <head>, <body>, nav, sidebar, và <main>)
require_once __DIR__ . '/../components/header.php'; 
?>

<div class="d-flex justify-content-between align-items-center mb-4 mt-4">
    <div>
        <h1 class="h3">Quản lý Người Dùng</h1>
        <nav aria-label="breadcrumb">
        </nav>
    </div>
    <div>
        <button id="btn-add-user" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Thêm User
        </button>
    </div>
</div>

<div id="main-message"></div>

<div class="card">
    <div class="card-header">
        Danh sách Người Dùng
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th scope="col">Mã TK (ID)</th>
                        <th scope="col">Tên Đăng Nhập</th>
                        <th scope="col">Quyền (Role)</th>
                        <th scope="col">Mã Liên Kết</th>
                        <th scope="col" style="min-width: 210px;">Hành động</th>
                    </tr>
                </thead>
                <tbody id="users-table-body">
                    <?php if (empty($users_list)): ?>
                        <tr id="row-empty">
                            <td colspan="5" class="text-center">Chưa có người dùng nào.</td> 
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users_list as $user): ?>
                            <tr id="row-<?php echo $user['UserID']; ?>">
                                <td><?php echo $user['UserID']; ?></td>
                                <td><?php echo htmlspecialchars($user['Username']); ?></td>
                                <td><?php echo htmlspecialchars($user['Role']); ?></td>
                                <td><?php echo htmlspecialchars($user['MaLienKet'] ?? 'N/A'); ?></td>
                                <td>
                                    <button class="btn btn-info btn-sm btn-edit" 
                                            data-id="<?php echo $user['UserID']; ?>">
                                        <i class="bi bi-pencil-square"></i> Sửa
                                    </button>
                                    <button class="btn btn-danger btn-sm btn-delete" 
                                            data-id="<?php echo $user['UserID']; ?>">
                                        <i class="bi bi-trash"></i> Xóa
                                    </button>
                                    <button class="btn btn-warning btn-sm btn-reset-pass" 
                                            data-id="<?php echo $user['UserID']; ?>"
                                            data-username="<?php echo htmlspecialchars($user['Username']); ?>">
                                        <i class="bi bi-key"></i> Reset MK
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

<div class="modal fade" id="userModal" tabindex="-1" 
     aria-labelledby="userModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userModalLabel">Thêm User Mới</h5>
                <button type="button" class="btn-close" 
                        data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form id="userForm">
                <div class="modal-body">
                    <div id="modal-message"></div>
                    
                    <input type="hidden" id="form-user-id" name="id" value="0">
                    
                    <div class="mb-3">
                        <label for="form-username" class="form-label">Tên Đăng Nhập:</label>
                        <input type="text" class="form-control" id="form-username" name="username" required>
                    </div>
                    
                    <div class="mb-3" id="form-password-group">
                        <label for="form-password" class="form-label">Mật Khẩu:</label>
                        <input type="password" class="form-control" id="form-password" name="password" required>
                    </div>

                    <div class="mb-3">
                        <label for="form-role" class="form-label">Quyền (Role):</label>
                        <select class="form-select" id="form-role" name="role" required>
                            <option value="">-- Chọn quyền --</option>
                            <option value="QuanLy">Quản Lý</option>
                            <option value="SinhVien">Sinh Viên</option>
                        </select>
                    </div>

                    <div class="mb-3" id="form-malienket-group" style="display: none;">
                        <label for="form-malienket" class="form-label">Mã Liên Kết (Mã SV):</label>
                        <input type="text" class="form-control" id="form-malienket" name="malienket" 
                               placeholder="Ví dụ: SV001">
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

        // --- Lấy các đối tượng DOM ---
        const tableBody = document.getElementById('users-table-body');
        const form = document.getElementById('userForm');
        const modalMessage = document.getElementById('modal-message');
        const mainMessage = document.getElementById('main-message');
        const modalTitle = document.getElementById('userModalLabel');
        const btnAddUser = document.getElementById('btn-add-user');
        
        // (QUAN TRỌNG) Điều khiển Bootstrap Modal
        const modalElement = document.getElementById('userModal');
        const bootstrapModal = new bootstrap.Modal(modalElement);

        // Form fields
        const formId = document.getElementById('form-user-id');
        const formUsername = document.getElementById('form-username');
        const formPasswordGroup = document.getElementById('form-password-group');
        const formPassword = document.getElementById('form-password');
        const formRole = document.getElementById('form-role');
        const formMaLienKetGroup = document.getElementById('form-malienket-group');
        const formMaLienKet = document.getElementById('form-malienket');

        // --- Hàm hiển thị thông báo (Bootstrap Alert) ---
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

        // --- Hàm helper để ẩn/hiện trường MaLienKet ---
        function toggleMaLienKetField(role) {
            if (role === 'SinhVien') {
                formMaLienKetGroup.style.display = 'block';
                formMaLienKet.required = true;
            } else {
                formMaLienKetGroup.style.display = 'none';
                formMaLienKet.required = false;
                formMaLienKet.value = ''; // Xóa giá trị nếu không phải SV
            }
        }

        // --- Hàm mở modal cho 'Create' ---
        function openCreateModal() {
            form.reset();
            formId.value = '0';
            modalTitle.textContent = 'Thêm User Mới';
            modalMessage.innerHTML = '';
            
            // Khi tạo mới, hiện trường Mật khẩu và bật required
            formPasswordGroup.style.display = 'block';
            formPassword.required = true; 
            
            toggleMaLienKetField(''); 
            bootstrapModal.show(); // Dùng hàm của Bootstrap
        }

        // --- Hàm mở modal cho 'Update' ---
        async function openUpdateModal(id) {
            try {
                const response = await fetch(`/users/get/${id}`);
                const result = await response.json();

                if (result.success) {
                    form.reset();
                    
                    formId.value = result.data.UserID;
                    formUsername.value = result.data.Username;
                    formRole.value = result.data.Role;
                    formMaLienKet.value = result.data.MaLienKet;

                    // Khi cập nhật, ẩn trường Mật khẩu và tắt required
                    formPasswordGroup.style.display = 'none';
                    formPassword.required = false; 

                    toggleMaLienKetField(result.data.Role);

                    modalTitle.textContent = 'Sửa User';
                    modalMessage.innerHTML = '';
                    bootstrapModal.show(); // Dùng hàm của Bootstrap
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
                ? '/users/ajax_create'
                : `/users/ajax_update/${id}`;

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();

                if (result.success) {
                    bootstrapModal.hide(); // Dùng hàm của Bootstrap
                    showMainMessage(result.message, false);

                    if (id === '0' || id === '') {
                        appendRowToTable(result.newRow);
                    } else {
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
            if (!confirm('Bạn có chắc chắn muốn xóa người dùng này?')) {
                return;
            }

            try {
                const response = await fetch(`/users/ajax_delete/${id}`, {
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
        
        // --- Hàm xử lý Reset Mật khẩu ---
        async function handleResetPassword(id, username) {
             if (!confirm(`Bạn có chắc chắn muốn reset mật khẩu cho '${username}'? (Mật khẩu mới sẽ là '123456')`)) {
                return;
            }
            try {
                const response = await fetch(`/users/ajax_reset_password/${id}`, {
                    method: 'POST'
                });
                const result = await response.json();
                showMainMessage(result.message, !result.success); 
            } catch (error) {
                showMainMessage('Lỗi kết nối: ' + error.message, true);
            }
        }

        // --- CÁC HÀM TIỆN ÍCH CHO BẢNG ---
        function createTableRow(user) {
            document.getElementById('row-empty')?.remove();
            
            const maLienKetDisplay = user.MaLienKet ? escapeHTML(user.MaLienKet) : 'N/A';
            const username = escapeHTML(user.Username);

            return `
                <tr id="row-${user.UserID}">
                    <td>${user.UserID}</td>
                    <td>${username}</td>
                    <td>${escapeHTML(user.Role)}</td>
                    <td>${maLienKetDisplay}</td>
                    <td>
                        <button class="btn btn-info btn-sm btn-edit" data-id="${user.UserID}">
                            <i class="bi bi-pencil-square"></i> Sửa
                        </button>
                        <button class="btn btn-danger btn-sm btn-delete" data-id="${user.UserID}">
                            <i class="bi bi-trash"></i> Xóa
                        </button>
                        <button class="btn btn-warning btn-sm btn-reset-pass" 
                                data-id="${user.UserID}" 
                                data-username="${username}">
                            <i class="bi bi-key"></i> Reset MK
                        </button>
                    </td>
                </tr>
            `;
        }

        function appendRowToTable(rowData) {
            tableBody.insertAdjacentHTML('beforeend', createTableRow(rowData));
        }

        function updateRowInTable(rowData) {
            const row = document.getElementById(`row-${rowData.UserID}`);
            if (row) {
                row.outerHTML = createTableRow(rowData);
            }
        }

        function showEmptyRow() {
            tableBody.innerHTML = '<tr id="row-empty"><td colspan="5" class="text-center">Chưa có người dùng nào.</td></tr>';
        }

        function escapeHTML(str) {
            if (str === null || str === undefined) return '';
            return str.toString().replace(/[&<>"']/g, m => ({'&': '&amp;', '<': '&lt;', '>': '&gt;', '\"': '&quot;', "'": '&#039;'}[m]));
        }

        // --- GÁN SỰ KIỆN (Event Listeners) ---
        btnAddUser.addEventListener('click', openCreateModal);
        
        // (SỬA) Đóng modal khi nhấn ra ngoài (chỉ khi modalElement đã khởi tạo)
        modalElement.addEventListener('click', (event) => {
            if (event.target == modalElement) {
                bootstrapModal.hide();
            }
        });
        
        form.addEventListener('submit', handleFormSubmit);

        formRole.addEventListener('change', () => {
            toggleMaLienKetField(formRole.value);
        });

        tableBody.addEventListener('click', function (event) {
            // (SỬA) Tìm button gần nhất (bao gồm cả icon)
            const target = event.target.closest('button'); 
            if (!target) return;

            const id = target.dataset.id;
            
            if (target.classList.contains('btn-edit')) {
                openUpdateModal(id);
            }
            if (target.classList.contains('btn-delete')) {
                handleDelete(id);
            }
            if (target.classList.contains('btn-reset-pass')) {
                const username = target.dataset.username;
                handleResetPassword(id, username);
            }
        });

    });
</script>

<?php
// 3. Gọi Footer
require_once __DIR__ . '/../components/footer.php'; 
?>