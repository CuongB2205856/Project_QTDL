<?php
$error = $data['error'] ?? '';
$oldData = $data['old_data'] ?? [];

// Helper để lấy giá trị cũ
$getOld = function ($field) use ($oldData) {
    return htmlspecialchars($oldData[$field] ?? '');
};
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="shortcut icon" href="CTU_logo.ico" type="image/x-icon">
    <link rel="stylesheet" href="assets/CSS/StyleRegister.css">
</head>

<body>
    <div class="container">
        <div class="register-container">
            <div class="register-card">
                <div class="register-header">
                    <img src="assets/image/CTU_logo.png" alt="Logo" style="max-height: 80px; margin-right: 10px;">
                    <h2>Đăng ký Tài khoản</h2>
                    <p>Hệ thống Quản Lý Ký Túc Xá</p>
                </div>

                <div class="register-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger d-flex align-items-center" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <div><?= htmlspecialchars($error) ?></div>
                        </div>
                    <?php endif; ?>

                    <form action="<?= BASE_URL ?>/register" method="POST" id="registerForm">

                        <div class="section-title">
                            <i class="bi bi-person-badge"></i>
                            Thông tin cá nhân
                        </div>

                        <div class="input-icon-wrapper">
                            <label for="masv" class="form-label">
                                Mã Sinh Viên <span class="text-danger">*</span>
                            </label>
                            <i class="bi bi-credit-card-2-front input-icon"></i>
                            <input type="text" class="form-control with-icon" id="masv" name="masv" required
                                maxlength="20" value="<?= $getOld('masv') ?>" placeholder="Nhập Mã Số Sinh Viên"
                                onblur="validateMSSV(this.value)" oninput="clearMSSVError()">
                        </div>
                        <div id="mssv-error" style="color: red; margin-top: 5px;">
                        </div>

                        <div class="input-icon-wrapper">
                            <label for="hoten" class="form-label">
                                Họ và Tên <span class="text-danger">*</span>
                            </label>
                            <i class="bi bi-person input-icon"></i>
                            <input type="text" class="form-control with-icon" id="hoten" name="hoten" required
                                maxlength="100" value="<?= $getOld('hoten') ?>" placeholder="Nhập họ và tên đầy đủ">
                        </div>

                        <div class="input-icon-wrapper">
                            <label for="gioitinh" class="form-label">
                                Giới tính
                            </label>
                            <i class="bi bi-gender-ambiguous input-icon"></i>
                            <select id="gioitinh" name="gioitinh" class="form-select with-icon">
                                <option value="" <?= $getOld('gioitinh') == '' ? 'selected' : '' ?>>-- Chọn giới tính --
                                </option>
                                <option value="Nam" <?= $getOld('gioitinh') == 'Nam' ? 'selected' : '' ?>>Nam</option>
                                <option value="Nữ" <?= $getOld('gioitinh') == 'Nữ' ? 'selected' : '' ?>>Nữ</option>
                                <option value="Nữ" <?= $getOld('gioitinh') == 'Khác' ? 'selected' : '' ?>>Khác</option>
                            </select>
                        </div>

                        <div class="input-icon-wrapper">
                            <label for="sdt" class="form-label">
                                Số Điện Thoại
                            </label>
                            <i class="bi bi-telephone input-icon"></i>
                            <input type="tel" class="form-control with-icon" id="sdt" name="sdt" maxlength="15"
                                value="<?= $getOld('sdt') ?>" placeholder="Nhập số điện thoại"
                                onblur="validatePhoneNumber(this.value)" oninput="clearError()">
                        </div>
                        <div id="sdt-error" style="color: red; margin-top: 5px;">
                        </div>

                        <div class="input-icon-wrapper">
                            <label for="password" class="form-label">
                                Mật khẩu <span class="text-danger">*</span>
                            </label>
                            <i class="bi bi-lock input-icon"></i>
                            <input type="password" class="form-control with-icon" id="password" name="password" required
                                placeholder="Nhập mật khẩu" onblur="validatePassword(this.value)"
                                oninput="clearPasswordError()"">
                            <i class=" bi bi-eye password-toggle" onclick="togglePassword('password', 'toggleIcon1')"
                                id="toggleIcon1"></i>
                        </div>
                        <div id="password-error" style="color: red; margin-top: 5px;">
                        </div>

                        <div class="input-icon-wrapper">
                            <label for="password_confirm" class="form-label">
                                Xác nhận Mật khẩu <span class="text-danger">*</span>
                            </label>
                            <i class="bi bi-lock-fill input-icon"></i>
                            <input type="password" class="form-control with-icon" id="password_confirm"
                                name="password_confirm" required placeholder="Nhập lại mật khẩu"
                                oninput="checkPasswordMatch()">
                            <i class="bi bi-eye password-toggle"
                                onclick="togglePassword('password_confirm', 'toggleIcon2')" id="toggleIcon2"></i>
                        </div>
                        <small class="text-muted d-block mt-1" id="matchText"></small>

                        <button type="submit" class="btn btn-register w-100 mt-3">
                            <i class="bi me-2"></i>
                            Đăng Ký
                        </button>

                        <div class="login-link">
                            Đã có tài khoản?
                            <a href="<?= BASE_URL ?>/login">
                                <i class="bi bi-box-arrow-in-right"></i> Đăng nhập ngay
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>

        // Hàm hiển thị mật khẩu
        function togglePassword(inputId, iconId) {
            const passwordInput = document.getElementById(inputId);
            const toggleIcon = document.getElementById(iconId);

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('bi-eye');
                toggleIcon.classList.add('bi-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('bi-eye-slash');
                toggleIcon.classList.add('bi-eye');
            }
        }

        // Hàm kiểm tra mật khẩu có trùng khớp chưa
        function checkPasswordMatch() {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('password_confirm').value;
            const matchText = document.getElementById('matchText');

            if (confirmPassword === '') {
                matchText.textContent = '';
                return;
            }

            if (password === confirmPassword) {
                matchText.innerHTML = '<i class="bi bi-check-circle-fill"></i> Mật khẩu khớp';
                matchText.style.color = '#1cc88a';
            } else {
                matchText.innerHTML = '<i class="bi bi-x-circle-fill"></i> Mật khẩu không khớp';
                matchText.style.color = '#e74a3b';
            }
        }


        // Hàm xóa thông báo lỗi mật khẩu khi người dùng click ra ngoài (onblur)
        function clearPasswordError() {
            const errorDiv = document.getElementById('password-error');
            errorDiv.textContent = "";
            errorDiv.style.color = 'red';
        }

        // Kiểm tra Mật khẩu khi người dùng click ra ngoài (onblur)
        function validatePassword(password) {
            const complexityRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;
            const errorDiv = document.getElementById('password-error');

            password = password.trim();

            if (password === "") {
                errorDiv.textContent = "";
                return;
            }

            // Thực hiện kiểm tra Regex
            if (!complexityRegex.test(password)) {
                // KHÔNG Hợp lệ: Liệt kê các điều kiện bị thiếu
                let errorMsg = "Mật khẩu KHÔNG hợp lệ. Phải có:";

                if (password.length < 8) {
                    errorMsg += " Tối thiểu 8 ký tự,";
                }
                if (!/(?=.*[a-z])/.test(password)) {
                    errorMsg += " Chữ thường,";
                }
                if (!/(?=.*[A-Z])/.test(password)) {
                    errorMsg += " Chữ hoa,";
                }
                if (!/(?=.*\d)/.test(password)) {
                    errorMsg += " Số,";
                }
                if (!/(?=.*[\W_])/.test(password)) {
                    errorMsg += " Ký tự đặc biệt.";
                }

                errorDiv.textContent = errorMsg.replace(/,$/, '.');
                errorDiv.style.color = 'red';
            }
        }

        // Hàm xóa thông báo lỗi MSSV khi người dùng click ra ngoài (onblur)
        function clearMSSVError() {
            const errorDiv = document.getElementById('mssv-error');
            errorDiv.textContent = "";
            errorDiv.style.color = 'red';
        }

        // Hàm kiểm tra MSSV khi người dùng click ra ngoài (onblur)
        function validateMSSV(mssv) {
            const regex = /^B\d{7}$/;
            const errorDiv = document.getElementById('mssv-error');

            mssv = mssv.trim();

            if (mssv === "") {
                errorDiv.textContent = "";
                return;
            }

            // Thực hiện kiểm tra Regex
            if (!regex.test(mssv)) {
                // KHÔNG Hợp lệ
                errorDiv.textContent = "MSSV KHÔNG hợp lệ.";
                errorDiv.style.color = 'red';
            }
        }

        // Hàm kiểm tra số điện thoại khi người dùng click ra ngoài (onblur)
        function validatePhoneNumber(sdt) {
            const regex = /^(0|\+84|84)\d{9}$/;
            const errorDiv = document.getElementById('sdt-error');
            sdt = sdt.trim();

            // 2. Kiểm tra chuỗi rỗng
            if (sdt === "") {
                // Nếu rỗng, xóa thông báo lỗi (ví dụ: khi click vào rồi click ra ngay)
                errorDiv.textContent = "";
                errorDiv.style.color = 'red';
                return;
            }

            // Thực hiện kiểm tra Regex
            if (!regex.test(sdt)) {
                // KHÔNG Hợp lệ: Hiển thị thông báo lỗi
                errorDiv.textContent = "Số điện thoại KHÔNG hợp lệ";
                errorDiv.style.color = 'red'; // Màu đỏ cho lỗi
            }
        }

        // Hàm xóa thông báo lỗi số điện thoại khi người dùng click ra ngoài (onblur)
        function clearError() {
            const errorDiv = document.getElementById('sdt-error');
            errorDiv.textContent = "";
            errorDiv.style.color = 'red';
        }

        // Hàm kiểm tra trước khi submit
        document.getElementById('registerForm').addEventListener('submit', function (e) {
            // LẤY GIÁ TRỊ VÀ REGEXES
            const password = document.getElementById('password').value.trim();
            const confirmPassword = document.getElementById('password_confirm').value.trim();
            const mssv = document.getElementById('masv').value.trim();
            const sdt = document.getElementById('sdt').value.trim();

            const PasswordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;
            const MSSVRegex = /^B\d{7}$/;
            const PhoneRegex = /^(0|\+84|84)\d{9}$/;

            // --- BẮT ĐẦU KIỂM TRA ĐỊNH DẠNG ---

            // 1. Kiểm tra MSSV (Nếu không rỗng thì phải hợp lệ)
            if (!MSSVRegex.test(mssv)) {
                e.preventDefault();
                alert('Mã số sinh viên không hợp lệ!');
                return false;
            }

            // 2. Kiểm tra Khớp Mật khẩu
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Mật khẩu xác nhận không khớp!');
                return false;
            }

            // 3. Kiểm tra Độ phức tạp Mật khẩu
            if (!PasswordRegex.test(password)) {
                e.preventDefault();
                alert('Mật khẩu quá yếu!');
                return false;
            }

            // 4. Kiểm tra Số điện thoại (Chỉ kiểm tra nếu có nhập)
            if (sdt !== "" && !PhoneRegex.test(sdt)) {
                e.preventDefault();
                alert('Số điện thoại không hợp lệ!');
                return false;
            }
        });
    </script>

</body>

</html>