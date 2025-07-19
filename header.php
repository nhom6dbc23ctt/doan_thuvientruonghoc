<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>📚 Library Management</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: #f4f6f9;
      font-family: Arial, sans-serif;
    }
    .navbar-brand {
      font-weight: bold;
    }
    .container-box {
      background: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 3px 10px rgba(0,0,0,0.1);
      margin-top: 20px;
    }
  </style>
</head>
<body>

<?php if(isset($_SESSION['user'])): ?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container">
    <a class="navbar-brand" href="dashboard.php">📚 Thư viện</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
          <li class="nav-item"><a class="nav-link" href="dashboard.php">🏠 Dashboard</a></li>
          <li class="nav-item"><a class="nav-link" href="borrow.php">✅ Mượn sách</a></li>
          <li class="nav-item"><a class="nav-link" href="return.php">↩️ Trả sách</a></li>
          <?php if ($_SESSION['user']['role'] != 'user'): ?>
              <li class="nav-item"><a class="nav-link" href="books.php">📚 Quản lý sách</a></li>
          <?php endif; ?>
          <?php if ($_SESSION['user']['role'] == 'admin'): ?>
              <li class="nav-item"><a class="nav-link" href="register.php">➕ Tạo User</a></li>
              <li class="nav-item"><a class="nav-link" href="users.php">👥 Danh sách User</a></li>
          <?php endif; ?>
          <li class="nav-item"><a class="nav-link text-warning" href="logout.php">🚪 Đăng xuất</a></li>
      </ul>
    </div>
  </div>
</nav>
<?php endif; ?>

<div class="container">
