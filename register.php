<?php
header("Content-Type: text/html; charset=UTF-8");
include 'db.php';
mysqli_set_charset($conn, "utf8mb4");

$msg = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Kiểm tra tài khoản đã tồn tại chưa
    $check = mysqli_query($conn, "SELECT * FROM users WHERE username='$username'");
    if (mysqli_num_rows($check) > 0) {
        $msg = "<div class='alert alert-danger text-center'>❌ Tên đăng nhập đã tồn tại!</div>";
    } else {
        // Thêm tài khoản mới (role mặc định user)
        mysqli_query($conn, "INSERT INTO users (username, password, role) VALUES ('$username','$password','user')");
        $msg = "<div class='alert alert-success text-center'>✅ Đăng ký thành công! <a href='login.php'>Đăng nhập</a></div>";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Đăng ký tài khoản | Thư viện Trường học số</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(135deg, #00b09b, #96c93d);
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      font-family: 'Segoe UI', sans-serif;
    }
    .register-box {
      background: white;
      padding: 30px;
      border-radius: 12px;
      width: 100%;
      max-width: 400px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.2);
    }
    .register-box h2 {
      text-align: center;
      margin-bottom: 20px;
      color: #28a745;
    }
    .btn-register {
      width: 100%;
    }
    .link-login {
      text-align: center;
      margin-top: 15px;
    }
  </style>
</head>
<body>

<div class="register-box">
  <h2>📝 Đăng ký tài khoản</h2>
  <p class="text-center text-muted">Tạo tài khoản mới để sử dụng hệ thống</p>

  <?php if($msg) echo $msg; ?>

  <form method="POST">
    <div class="mb-3">
      <label class="form-label">Tên đăng nhập</label>
      <input type="text" name="username" class="form-control" placeholder="Nhập tên đăng nhập" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Mật khẩu</label>
      <input type="password" name="password" class="form-control" placeholder="Nhập mật khẩu" required>
    </div>
    <button type="submit" class="btn btn-success btn-register">Đăng ký</button>
  </form>

  <div class="link-login">
    <p>Đã có tài khoản? <a href="login.php">Đăng nhập</a></p>
    <a href="index.php" class="btn btn-outline-secondary btn-sm mt-2">⬅ Quay lại Trang chủ</a>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
