<?php
// app/views/StudentPanel/index.php
// $data['details'] chứa toàn bộ thông tin
// $data['maSV']
?>

<style>
    .profile-card {
        padding: 20px;
        border-radius: 8px;
        background-color: #f9f9f9;
        margin-bottom: 25px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .profile-card h3 {
        border-bottom: 2px solid #eee;
        padding-bottom: 10px;
        margin-top: 0;
    }
    .profile-card p {
        font-size: 1.1em;
        line-height: 1.6;
    }
    .profile-card p strong {
        display: inline-block;
        min-width: 180px;
        color: #555;
    }
    .text-danger {
        color: #e74a3b;
        font-weight: bold;
    }
    .text-success {
        color: #1cc88a;
        font-weight: bold;
    }
    #pass-form-message { margin-top: 15px; font-weight: bold; }
</style>

<h2>Bảng Điều Khiển Sinh Viên</h2>
<p>Xin chào, <strong><?php echo htmlspecialchars($data['details']['HoTen'] ?? $data['maSV']); ?></strong>.</p>
<hr>

<div class="profile-card">
    <h3><i class="fas fa-fw fa-door-open"></i> Thông Tin Phòng Ở</h3>
    <?php if (!empty($data['details']['SoPhong'])): ?>
        <p><strong>Phòng đang ở:</strong> <?php echo htmlspecialchars($data['details']['SoPhong']); ?></p>
        <p><strong>Loại phòng:</strong> <?php echo htmlspecialchars($data['details']['TenLoaiPhong']); ?></p>
        <p><strong>Giá thuê phòng (cơ bản):</strong> <?php echo number_format($data['details']['GiaTienThuePhong']); ?> VND/tháng</p>
    <?php else: ?>
        <p class="text-danger">Bạn hiện không có hợp đồng ở phòng nào còn hiệu lực.</p>
    <?php endif; ?>
</div>

<div class="profile-card">
    <h3><i class="fas fa-fw fa-money-check-alt"></i> Thông Tin Thanh Toán</h3>
    
    <?php if (!empty($data['details']['GiaTienPhaiDong'])): ?>
        <p><strong>Số tiền cần đóng (hóa đơn gần nhất):</strong> 
            <span class="text-danger"><?php echo number_format($data['details']['GiaTienPhaiDong']); ?> VND</span>
        </p>
        <p><strong>Ngày đến hạn thanh toán:</strong> 
            <span class="text-danger"><?php echo date("d/m/Y", strtotime($data['details']['NgayDenHanDongTien'])); ?></span>
        </p>
    <?php else: ?>
         <p class="text-success">Hiện không có công nợ nào.</p>
         <p><i>(Nếu có phát sinh dịch vụ hoặc đến kỳ, hóa đơn mới sẽ xuất hiện ở đây)</i></p>
    <?php endif; ?>
</div>

<div class="profile-card">
    <h3><i class="fas fa-fw fa-key"></i> Đổi Mật Khẩu</h3>
    <form id="change-pass-form">
        <label for="old_pass">Mật khẩu cũ:</label><br>
        <input type="password" id="old_pass" name="old_pass" required style="width: 300px;"><br><br>
        
        <label for="new_pass">Mật khẩu mới:</label><br>
        <input type="password" id="new_pass" name="new_pass" required style="width: 300px;"><br><br>

        <label for="confirm_new_pass">Xác nhận mật khẩu mới:</label><br>
        <input type="password" id="confirm_new_pass" name="confirm_new_pass" required style="width: 300px;"><br><br>

        <button type="submit">Xác Nhận Đổi</button>
        <div id="pass-form-message"></div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('change-pass-form');
    const messageEl = document.getElementById('pass-form-message');

    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        messageEl.textContent = 'Đang xử lý...';
        messageEl.className = '';

        const newPass = document.getElementById('new_pass').value;
        const confirmPass = document.getElementById('confirm_new_pass').value;

        if (newPass !== confirmPass) {
            messageEl.textContent = 'Mật khẩu mới không khớp!';
            messageEl.className = 'text-danger';
            return;
        }

        if (newPass.length < 6) {
             messageEl.textContent = 'Mật khẩu mới phải từ 6 ký tự trở lên.';
             messageEl.className = 'text-danger';
             return;
        }

        const formData = new FormData(form);

        try {
            const response = await fetch('/student/ajax_change_password', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();

            if (result.success) {
                messageEl.textContent = result.message;
                messageEl.className = 'text-success';
                form.reset();
            } else {
                messageEl.textContent = result.message;
                messageEl.className = 'text-danger';
            }

        } catch (error) {
            messageEl.textContent = 'Lỗi kết nối: ' + error.message;
            messageEl.className = 'text-danger';
        }
    });
});
</script>
<?php
// 3. Gọi Footer (Đóng <main>, <footer>, <script>, </body>, </html>)
require_once __DIR__ . '/../components/footer.php';
?>