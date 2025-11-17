<?php
$error = $data['error'] ?? '';
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="shortcut icon" href="CTU_logo.ico" type="image/x-icon">
    <link rel="stylesheet" href="assets/CSS/StyleLogin.css">
</head>

<body>

    <div class="container">
        <div class="login-container mx-auto">
            <div class="login-card">
                <div class="login-header">
                    <img src="assets/image/CTU_logo.png" alt="Logo" style="max-height: 80px; margin-right: 10px;">
                    <h2>Đăng nhập</h2>
                    <p>Hệ thống Quản Lý Ký Túc Xá</p>
                </div>

                <div class="login-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger d-flex align-items-center" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <div><?= htmlspecialchars($error) ?></div>
                        </div>
                    <?php endif; ?>

                    <form action="<?= BASE_URL ?>/login" method="POST">

                        <div class="mb-3">
                            <label for="username" class="form-label">
                                <i class="bi bi-person-circle me-1"></i>
                                Tên đăng nhập
                            </label>
                            <div class="input-group">
                                <i class="bi bi-person-fill input-icon"></i>
                                <input type="text" class="form-control with-icon" id="username" name="username" required
                                    placeholder="Tên đăng nhập">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label">
                                <i class="bi bi-lock-fill me-1"></i>
                                Mật khẩu
                            </label>
                            <div class="input-group">
                                <i class="bi bi-lock-fill input-icon"></i>
                                <input type="password" class="form-control with-icon" id="password" name="password"
                                    required placeholder="Nhập mật khẩu">
                                <i class="bi bi-eye password-toggle" onclick="togglePassword()" id="toggleIcon"></i>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-login w-100">
                            <i class="bi bi-box-arrow-in-right me-2"></i>
                            Đăng nhập
                        </button>

                        <div class="divider">
                            <span>hoặc</span>
                        </div>

                        <div class="register-link">
                            Chưa có tài khoản?
                            <a href="<?= BASE_URL ?>/register">
                                <i class="bi bi-person-plus"></i> Đăng ký ngay
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');

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
    </script>

</body>

</html>