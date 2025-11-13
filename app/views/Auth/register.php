<?php 
// Giả định BASE_URL đã được định nghĩa trong phạm vi toàn cục hoặc qua Controller
$error = $data['error'] ?? '';
$oldData = $data['old_data'] ?? [];

// Helper để lấy giá trị cũ
$getOld = function($field) use ($oldData) {
    return htmlspecialchars($oldData[$field] ?? '');
};
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký Sinh Viên - Quản Lý KTX</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .register-container { max-width: 600px; margin: 50px auto; padding: 30px; background: #fff; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
    </style>
</head>
<body>

<div class="container">
    <div class="register-container">
        <h2 class="text-center mb-4">Đăng ký Tài khoản Sinh Viên</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-danger" role="alert">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form action="<?= BASE_URL ?>/register" method="POST">
            
            <h5 class="mt-3 mb-3 text-primary">Thông tin cá nhân (Bảng SINHVIEN)</h5>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="masv">Mã Sinh Viên (<span class="text-danger">*</span>)</label>
                    <input type="text" class="form-control" id="masv" name="masv" required maxlength="20"
                           value="<?= $getOld('masv') ?>" placeholder="Ví dụ: B1900000">
                </div>
                <div class="form-group col-md-6">
                    <label for="hoten">Họ và Tên (<span class="text-danger">*</span>)</label>
                    <input type="text" class="form-control" id="hoten" name="hoten" required maxlength="100"
                           value="<?= $getOld('hoten') ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="gioitinh">Giới tính</label>
                    <select id="gioitinh" name="gioitinh" class="form-control">
                        <option value="" <?= $getOld('gioitinh') == '' ? 'selected' : '' ?>>-- Chọn --</option>
                        <option value="Nam" <?= $getOld('gioitinh') == 'Nam' ? 'selected' : '' ?>>Nam</option>
                        <option value="Nữ" <?= $getOld('gioitinh') == 'Nữ' ? 'selected' : '' ?>>Nữ</option>
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <label for="sdt">Số Điện Thoại</label>
                    <input type="tel" class="form-control" id="sdt" name="sdt" maxlength="15"
                           value="<?= $getOld('sdt') ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="password">Mật khẩu (<span class="text-danger">*</span>)</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="password_confirm">Xác nhận Mật khẩu (<span class="text-danger">*</span>)</label>
                    <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
                </div>
            </div>

            <button type="submit" class="btn btn-success btn-block mt-4">Đăng ký Tài khoản Sinh Viên</button>
            
            <p class="mt-3 text-center">
                Đã có tài khoản? <a href="<?= BASE_URL ?>/login">Đăng nhập</a>
            </p>
        </form>
    </div>
</div>

</body>
</html>