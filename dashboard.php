<?php
session_start();
if (!isset($_SESSION['user'])) header("Location: login.php");
include 'header.php';
?>
<div class="container-box">
  <h2>Xin chào, <span class="text-primary"><?php echo $_SESSION['user']['username']; ?></span>!</h2>
  <p>Chọn chức năng bên dưới:</p>
  <div class="list-group">
      <a href="borrow.php" class="list-group-item list-group-item-action">✅ Mượn sách</a>
      <a href="return.php" class="list-group-item list-group-item-action">↩️ Trả sách</a>
      <?php if ($_SESSION['user']['role'] != 'user'): ?>
          <a href="books.php" class="list-group-item list-group-item-action">📚 Quản lý sách</a>
      <?php endif; ?>
      <?php if ($_SESSION['user']['role'] == 'admin'): ?>
          <a href="register.php" class="list-group-item list-group-item-action">👤 Tạo tài khoản người dùng</a>
          <a href="users.php" class="list-group-item list-group-item-action">📋 Danh sách người dùng</a>
      <?php endif; ?>
      <a href="logout.php" class="list-group-item list-group-item-action text-danger">🚪 Đăng xuất</a>
  </div>
</div>
<?php include 'footer.php'; ?>