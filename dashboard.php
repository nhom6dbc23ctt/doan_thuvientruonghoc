<?php
header("Content-Type: text/html; charset=UTF-8");
session_start();
include 'db.php';
mysqli_set_charset($conn, "utf8mb4");

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Dashboard - Thư viện số</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: #f4f6f9;
      font-family: 'Segoe UI', sans-serif;
    }
    .container-content {
      max-width: 1100px;
      margin: 80px auto 40px auto;
      background: white;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    }
    .btn-menu {
      width: 100%;
      margin-bottom: 15px;
      padding: 15px;
      font-size: 18px;
      text-align: left;
    }
  </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container container-content">
  <h2 class="text-center">🏠 Dashboard</h2>
  <p class="text-center text-muted">Chọn tính năng bạn muốn truy cập</p>

  <div class="row mt-4">
    <div class="col-md-6">
      <a href="books.php" class="btn btn-primary btn-menu">📖 Quản lý sách</a>
    </div>
    <div class="col-md-6">
      <a href="history.php" class="btn btn-outline-info btn-menu">📜 Lịch sử mượn sách</a>
    </div>
    <?php if ($_SESSION['user']['role']=='admin'): ?>
    <div class="col-md-6">
      <a href="users.php" class="btn btn-warning btn-menu">👥 Quản lý người dùng</a>
    </div>
    <?php endif; ?>
    <div class="col-md-6">
      <a href="index.php" target="_blank" class="btn btn-secondary btn-menu">🌐 Xem trang Public</a>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
