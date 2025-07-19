<?php
session_start();
if (!isset($_SESSION['user'])) header("Location: login.php");
?>
<?php include 'header.php'; ?>
<h3>Xin chào, <?php echo $_SESSION['user']['username']; ?>!</h3>
<ul>
    <li><a href="books.php">📚 Quản lý sách</a></li>
    <li><a href="borrow.php">✅ Mượn sách</a></li>
    <li><a href="return.php">↩️ Trả sách</a></li>
    <?php if ($_SESSION['user']['role'] == 'admin'): ?>
        <li><a href="register.php">👤 Tạo tài khoản người dùng</a></li>
        <li><a href="users.php">📋 Danh sách người dùng</a></li>
    <?php endif; ?>
    <li><a href="logout.php">🚪 Đăng xuất</a></li>
</ul>
<?php include 'footer.php'; ?>