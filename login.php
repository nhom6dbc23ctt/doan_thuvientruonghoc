<?php
header("Content-Type: text/html; charset=UTF-8");
session_start();
include 'db.php';
mysqli_set_charset($conn, "utf8mb4");

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {
        $_SESSION['user'] = mysqli_fetch_assoc($result);
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "❌ Sai tên đăng nhập hoặc mật khẩu!";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Đăng nhập | Thư viện Trường học số</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(135deg, #007bff, #00c6ff);
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      font-family: 'Segoe UI', sans-serif;
    }
    .login-box {
      background: white;
      padding: 30px;
      border-radius: 12px;
      width: 100%;
      max-width: 400px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.2);
    }
    .login-box h2 {
      text-align: center;
      margin-bottom: 20px;
      color: #007bff;
    }
    .btn-login {
      width: 100%;
    }
    .link-register {
      text-align: center;
      margin-top: 15px;
    }
  </style>
</head>
<body>

<div class="login-box">
  <h2>📚 HỆ THỐNG THƯ VIỆN</h2>
  <p class="text-center text-muted">Vui lòng đăng nhập để tiếp tục</p>

  <?php if($error): ?>
    <div class="alert alert-danger text-center"><?= $error ?></div>
  <?php endif; ?>

  <form method="POST">
    <div class="mb-3">
      <label class="form-label">Tên đăng nhập</label>
      <input type="text" name="username" class="form-control" placeholder="Nhập tên đăng nhập" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Mật khẩu</label>
      <input type="password" name="password" class="form-control" placeholder="Nhập mật khẩu" required>
    </div>
    <button type="submit" class="btn btn-primary btn-login">Đăng nhập</button>
  </form>

  <div class="link-register">
    <p>Chưa có tài khoản? <a href="register.php">Đăng ký ngay</a></p>
    <a href="index.php" class="btn btn-outline-secondary btn-sm mt-2">⬅ Quay lại Trang chủ</a>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
