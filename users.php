<?php
session_start();
include 'db.php';
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    die("Bạn không có quyền truy cập trang này!");
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    if ($id == $_SESSION['user']['id']) {
        $msg = "<div class='alert alert-danger'>❌ Không thể xóa chính mình!</div>";
    } else {
        mysqli_query($conn, "DELETE FROM users WHERE id=$id");
        $msg = "<div class='alert alert-warning'>⚠️ Đã xóa tài khoản!</div>";
    }
}

$result = mysqli_query($conn, "SELECT * FROM users ORDER BY role DESC, username ASC");
include 'header.php';
?>

<div class="container-box">
  <h2>👥 Danh sách người dùng</h2>
  <?php if(isset($msg)) echo $msg; ?>
  <table class="table table-hover table-bordered">
    <thead class="table-secondary">
      <tr>
        <th>ID</th>
        <th>Tên đăng nhập</th>
        <th>Vai trò</th>
        <th>Mật khẩu</th>
        <th>Hành động</th>
      </tr>
    </thead>
    <tbody>
      <?php while($user = mysqli_fetch_assoc($result)): ?>
        <tr>
          <td><?= $user['id'] ?></td>
          <td><?= htmlspecialchars($user['username']) ?></td>
          <td>
            <?php if($user['role']=='admin'): ?>
              <span class="badge bg-danger">Admin</span>
            <?php elseif($user['role']=='librarian'): ?>
              <span class="badge bg-info text-dark">Thủ thư</span>
            <?php else: ?>
              <span class="badge bg-secondary">User</span>
            <?php endif; ?>
          </td>
          <td><?= $user['password'] ?></td>
          <td>
            <?php if ($user['id'] != $_SESSION['user']['id']): ?>
              <a href="?delete=<?= $user['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Xóa tài khoản này?')">Xóa</a>
            <?php else: ?>
              <span class="text-muted">Đang đăng nhập</span>
            <?php endif; ?>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
  <a href="dashboard.php" class="btn btn-secondary">⬅ Quay lại Dashboard</a>
</div>

<?php include 'footer.php'; ?>
