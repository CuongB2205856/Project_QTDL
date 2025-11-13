<?php
// Giả định BASE_URL đã được định nghĩa trong phạm vi toàn cục hoặc qua Controller
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
    <style>
        :root {
            --primary-color: #667eea;
            --secondary-color: #764ba2;
            --success-color: #1cc88a;
            --danger-color: #e74a3b;
            --warning-color: #f6c23e;
        }

        body {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 40px 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .register-container {
            max-width: 520px;
            width: 100%;
            margin: 20px auto;
        }

        .register-card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.25);
            overflow: hidden;
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .register-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            padding: 35px 30px;
            text-align: center;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .register-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            animation: rotate 20s linear infinite;
        }

        @keyframes rotate {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        .register-header img {
            max-height: 70px;
            margin-bottom: 15px;
            position: relative;
            z-index: 1;
            filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.2));
        }

        .register-header h2 {
            margin: 0;
            font-size: 26px;
            font-weight: 700;
            position: relative;
            z-index: 1;
        }

        .register-header p {
            margin: 8px 0 0;
            opacity: 0.95;
            font-size: 14px;
            position: relative;
            z-index: 1;
        }

        .register-body {
            padding: 35px 30px;
        }

        .section-title {
            color: var(--primary-color);
            font-size: 17px;
            font-weight: 700;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 3px solid #f0f0f0;
            display: flex;
            align-items: center;
            position: relative;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: -3px;
            left: 0;
            width: 60px;
            height: 3px;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
        }

        .section-title i {
            margin-right: 10px;
            font-size: 20px;
        }

        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .text-danger {
            color: var(--danger-color) !important;
        }

        .form-control,
        .form-select {
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            padding: 13px 15px;
            font-size: 15px;
            transition: all 0.3s ease;
            background-color: #fafafa;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.15);
            background-color: #fff;
        }

        .input-icon-wrapper {
            position: relative;
            margin-bottom: 18px;
        }

        .input-icon {
    position: absolute;
    left: 15px;
    top: 50%;
    color: #999;
    z-index: 10;
    font-size: 18px;
}

        .form-control.with-icon,
        .form-select.with-icon {
            padding-left: 48px;
        }

        .btn-register {
            background: linear-gradient(135deg, var(--success-color) 0%, #17a673 100%);
            border: none;
            border-radius: 12px;
            padding: 15px;
            font-size: 16px;
            font-weight: 600;
            color: white;
            transition: all 0.3s ease;
            margin-top: 10px;
            box-shadow: 0 4px 15px rgba(28, 200, 138, 0.3);
        }

        .btn-register:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(28, 200, 138, 0.4);
            background: linear-gradient(135deg, #17a673 0%, var(--success-color) 100%);
        }

        .btn-register:active {
            transform: translateY(-1px);
        }

        .alert {
            border-radius: 12px;
            border: none;
            padding: 15px 18px;
            margin-bottom: 25px;
            animation: slideDown 0.3s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-danger {
            background-color: #ffe6e6;
            color: #d32f2f;
            border-left: 4px solid var(--danger-color);
        }

        .login-link {
            text-align: center;
            margin-top: 25px;
            padding-top: 25px;
            border-top: 2px solid #f0f0f0;
            color: #666;
            font-size: 14px;
        }

        .login-link a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .login-link a:hover {
            color: var(--secondary-color);
            text-decoration: underline;
        }

        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            cursor: pointer;
            color: #999;
            z-index: 10;
            font-size: 18px;
            transition: color 0.3s ease;
        }

        .password-toggle:hover {
            color: var(--primary-color);
        }

        .password-strength {
            height: 5px;
            border-radius: 3px;
            background: #e0e0e0;
            margin-top: 8px;
            overflow: hidden;
        }

        .password-strength-bar {
            height: 100%;
            width: 0;
            transition: all 0.4s ease;
            border-radius: 3px;
        }

        .strength-weak {
            background: linear-gradient(90deg, var(--danger-color), #ff6b6b);
            width: 33%;
        }

        .strength-medium {
            background: linear-gradient(90deg, var(--warning-color), #ffd93d);
            width: 66%;
        }

        .strength-strong {
            background: linear-gradient(90deg, var(--success-color), #00d9a3);
            width: 100%;
        }

        .password-info {
            font-size: 12px;
            color: #999;
            margin-top: 5px;
            display: flex;
            align-items: center;
        }

        .password-info i {
            margin-right: 5px;
        }

        @media (max-width: 576px) {
            .register-container {
                margin: 10px;
            }

            .register-body {
                padding: 25px 20px;
            }

            .register-header {
                padding: 25px 20px;
            }

            .section-title {
                font-size: 16px;
            }

            .register-header h2 {
                font-size: 22px;
            }
        }

        /* Animation cho form inputs */
        .form-control:focus,
        .form-select:focus {
            animation: inputFocus 0.3s ease;
        }

        @keyframes inputFocus {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.02);
            }

            100% {
                transform: scale(1);
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="register-container">
            <div class="register-card">
                <div class="register-header">
                    <img src="/image/CTU_logo.png" alt="Logo CTU">
                    <h2>Đăng ký Tài khoản</h2>
                    <p>Hệ thống Quản Lý Ký Túc Xá - Dành cho Sinh Viên</p>
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
                                maxlength="20" value="<?= $getOld('masv') ?>" placeholder="Ví dụ: B1900000">
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
                            </select>
                        </div>

                        <div class="input-icon-wrapper">
                            <label for="sdt" class="form-label">
                                Số Điện Thoại
                            </label>
                            <i class="bi bi-telephone input-icon"></i>
                            <input type="tel" class="form-control with-icon" id="sdt" name="sdt" maxlength="15"
                                value="<?= $getOld('sdt') ?>" placeholder="Ví dụ: 0912345678">
                        </div>

                        <!-- <div class="section-title mt-4">
                            <i class="bi bi-shield-lock"></i>
                            Bảo mật tài khoản
                        </div> -->

                        <div class="input-icon-wrapper">
                            <label for="password" class="form-label">
                                Mật khẩu <span class="text-danger">*</span>
                            </label>
                            <i class="bi bi-lock input-icon"></i>
                            <input type="password" class="form-control with-icon" id="password" name="password" required
                                placeholder="Nhập mật khẩu" oninput="checkPasswordStrength()">
                            <i class="bi bi-eye password-toggle" onclick="togglePassword('password', 'toggleIcon1')"
                                id="toggleIcon1"></i>                            
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
                            <small class="text-muted d-block mt-1" id="matchText"></small>
                        </div>

                        <button type="submit" class="btn btn-register w-100 mt-3">
                            <i class="bi bi-check-circle me-2"></i>
                            Đăng ký Tài khoản Sinh Viên
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

        // Validate form before submit
        document.getElementById('registerForm').addEventListener('submit', function (e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('password_confirm').value;

            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Mật khẩu xác nhận không khớp!');
                return false;
            }

            if (password.length < 6) {
                e.preventDefault();
                alert('Mật khẩu phải có ít nhất 6 ký tự!');
                return false;
            }
        });

        // Auto-focus first input
        window.addEventListener('load', function () {
            document.getElementById('masv').focus();
        });
    </script>

</body>

</html>