<?php
session_start();
include 'db.php';

// Chỉ admin mới truy cập được
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    die("Bạn không có quyền truy cập trang này!");
}

// Xử lý xóa tài khoản
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    // Không cho phép xóa chính mình
    if ($id == $_SESSION['user']['id']) {
        $msg = "<p class='text-danger'>❌ Không thể tự xóa tài khoản đang đăng nhập!</p>";
    } else {
        mysqli_query($conn, "DELETE FROM users WHERE id=$id");
        $msg = "<p class='text-success'>✅ Đã xóa tài khoản thành công!</p>";
    }
}

// Lấy danh sách user
$result = mysqli_query($conn, "SELECT * FROM users ORDER BY role DESC, username ASC");
?>
<?php include 'header.php'; ?>
<h2>Danh sách người dùng</h2>

<?php if (isset($msg)) echo $msg; ?>

<table class="table table-bordered">
    <tr>
        <th>ID</th>
        <th>Tên đăng nhập</th>
        <th>Vai trò</th>
        <th>Mật khẩu</th>
        <th>Hành động</th>
    </tr>
    <?php while($user = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td><?= $user['id'] ?></td>
            <td><?= $user['username'] ?></td>
            <td><?= $user['role'] ?></td>
            <td><?= $user['password'] ?></td>
            <td>
                <?php if ($user['id'] != $_SESSION['user']['id']): ?>
                    <a href="?delete=<?= $user['id'] ?>" onclick="return confirm('Xóa tài khoản này?');" class="btn btn-danger btn-sm">Xóa</a>
                <?php else: ?>
                    <span class="text-muted">Đang đăng nhập</span>
                <?php endif; ?>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

<a href="dashboard.php" class="btn btn-secondary">⬅ Quay lại Dashboard</a>
<?php include 'footer.php'; ?>
