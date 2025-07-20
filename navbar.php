<?php
if (!isset($_SESSION)) session_start();
$user = $_SESSION['user'] ?? ['username' => 'Guest', 'role' => 'guest'];
$role = $user['role'];
$current = basename($_SERVER['PHP_SELF']); // tên file hiện tại
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
  <div class="container-fluid">
    <a class="navbar-brand" href="dashboard.php">📚 Thư viện số</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link <?=($current=='dashboard.php'?'active':'')?>" href="dashboard.php">🏠 Dashboard</a></li>
        <li class="nav-item"><a class="nav-link <?=($current=='books.php'?'active':'')?>" href="books.php">📖 Quản lý sách</a></li>
        <?php if($role=='admin'): ?>
          <li class="nav-item"><a class="nav-link <?=($current=='users.php'?'active':'')?>" href="users.php">👥 Quản lý người dùng</a></li>
        <?php endif; ?>
        <li class="nav-item"><a class="nav-link <?=($current=='history.php'?'active':'')?>" href="history.php">📜 Lịch sử mượn</a></li>
        <li class="nav-item"><a class="nav-link <?=($current=='borrow.php'?'active':'')?>" href="borrow.php">📚 Mượn sách</a></li>
        <li class="nav-item"><a class="nav-link <?=($current=='return.php'?'active':'')?>" href="return.php">↩️ Trả sách</a></li>
        <li class="nav-item"><a class="nav-link <?=($current=='books_list.php'?'active':'')?>" href="books_list.php">📚 Danh sách sách</a></li>
        <li class="nav-item"><a class="nav-link <?=($current=='cart.php'?'active':'')?>" href="cart.php">🛒 Giỏ sách</a></li>
        <li class="nav-item"><a class="nav-link" target="_blank" href="index.php">🌐 Trang Public</a></li>
      </ul>
      <span class="navbar-text text-white me-3">
        Xin chào, <strong><?= htmlspecialchars($user['username']) ?></strong> (<?= $role ?>)
      </span>
      <a href="logout.php" class="btn btn-outline-light">🚪 Đăng xuất</a>
    </div>
  </div>
</nav>
