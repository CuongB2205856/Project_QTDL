<?php
// app/views/Users/index.php
// $data['users_list']
// $data['message'], $data['message_type']
?>

<style>
    /* (Bạn có thể dùng lại các style CSS từ file Create.php) */
    .form-group { margin-bottom: 15px; }
    .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
    .form-control { width: 100%; max-width: 400px; padding: 8px; }
    .btn-submit { padding: 10px 20px; background-color: #007bff; color: white; border: none; cursor: pointer; }
    .btn-delete { padding: 5px 10px; background-color: #dc3545; color: white; border: none; cursor: pointer; }
    
    .message-box { padding: 15px; margin-bottom: 20px; border-radius: 5px; }
    .message-success { background-color: #d4edda; color: #155724; }
    .message-danger { background-color: #f8d7da; color: #721c24; }
    
    table { width: 100%; max-width: 800px; border-collapse: collapse; margin-top: 20px; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
</style>

<h2>Quản Lý Người Dùng (Quản Lý)</h2>
<hr>

<?php if ($data['message']): ?>
    <div class="message-box message-<?php echo $data['message_type']; ?>">
        <?php echo htmlspecialchars($data['message']); ?>
    </div>
<?php endif; ?>

<h3>Tạo Người Dùng Mới</h3>
<form action="<?php echo url('users'); ?>" method="POST">
    
    <div class="form-group">
        <label for="username">Tên Đăng Nhập:</label>
        <input type="text" id="username" name="username" class="form-control" required>
    </div>

    <div class="form-group">
        <label for="password">Mật Khẩu:</label>
        <input type="password" id="password" name="password" class="form-control" required>
    </div>

    <button type="submit" class="btn-submit">Tạo Người Dùng</button>
</form>

<hr style="margin-top: 30px;">

<h3>Danh Sách Người Dùng</h3>
<table>
    <thead>
        <tr>
            <th>Tên Đăng Nhập</th>
            <th>Quyền (Role)</th>
            <th>Hành Động</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($data['users_list'])): ?>
            <tr>
                <td colspan="3">Chưa có người dùng nào.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($data['users_list'] as $user): ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['Username']); ?></td>
                    <td><?php echo htmlspecialchars($user['Role']); ?></td>
                    <td>
                        <form action="<?php echo url('users/delete/' . htmlspecialchars($user['UserID'])); ?>" method="POST" style="display: inline;">
                            <button type="submit" class="btn-delete" onclick="return confirm('Bạn có chắc chắn muốn xóa người dùng này?');">
                                Xóa
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>