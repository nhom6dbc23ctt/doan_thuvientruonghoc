<?php
header("Content-Type: text/html; charset=UTF-8");
session_start();
include 'db.php';
mysqli_set_charset($conn, "utf8mb4");

// ✅ Chỉ admin mới có quyền truy cập
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    die("❌ Bạn không có quyền truy cập trang này!");
}

// ✅ Xóa user (CHẶN XÓA ADMIN)
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $checkRole = mysqli_query($conn, "SELECT role FROM users WHERE id=$id");
    $row = mysqli_fetch_assoc($checkRole);
    if ($row && $row['role'] != 'admin') {
        mysqli_query($conn, "DELETE FROM users WHERE id=$id");
    }
    header("Location: users.php");
    exit;
}

// ✅ Thêm user mới
if (isset($_POST['add'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    mysqli_query($conn, "INSERT INTO users (username,password,role) VALUES ('$username','$password','$role')");
    header("Location: users.php");
    exit;
}

// ✅ Sửa user (CHẶN ĐỔI ROLE ADMIN)
if (isset($_POST['edit'])) {
    $id = intval($_POST['id']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);

    $checkRole = mysqli_query($conn, "SELECT role FROM users WHERE id=$id");
    $row = mysqli_fetch_assoc($checkRole);

    if ($row['role'] == 'admin') {
        // Admin chỉ sửa username, password, không đổi role
        mysqli_query($conn, "UPDATE users SET username='$username', password='$password' WHERE id=$id");
    } else {
        mysqli_query($conn, "UPDATE users SET username='$username', password='$password', role='$role' WHERE id=$id");
    }
    header("Location: users.php");
    exit;
}

// ✅ Lấy danh sách user
$users = mysqli_query($conn, "SELECT * FROM users ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>👥 Quản lý người dùng</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background: #f4f6f9; font-family: 'Segoe UI', sans-serif; }
    .container-content {
      max-width: 1100px;
      margin: 80px auto 40px auto;
      background: white;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    }
    .table th { background: #007bff; color: white; }
  </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container container-content">
  <h2 class="text-center mb-4">👥 Quản lý người dùng</h2>

  <!-- ✅ Form thêm người dùng -->
  <div class="card mb-4">
    <div class="card-header">➕ Thêm người dùng mới</div>
    <div class="card-body">
      <form method="POST">
        <div class="row g-2">
          <div class="col-md-3"><input type="text" name="username" class="form-control" placeholder="Tên đăng nhập" required></div>
          <div class="col-md-3"><input type="text" name="password" class="form-control" placeholder="Mật khẩu" required></div>
          <div class="col-md-3">
            <select name="role" class="form-control">
              <option value="user">User</option>
              <option value="librarian">Librarian</option>
              <option value="admin">Admin</option>
            </select>
          </div>
          <div class="col-md-2">
            <button type="submit" name="add" class="btn btn-success w-100">Thêm</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- ✅ Bảng danh sách người dùng -->
  <table class="table table-bordered table-striped align-middle">
    <thead>
      <tr>
        <th>ID</th>
        <th>Tên đăng nhập</th>
        <th>Mật khẩu</th>
        <th>Vai trò</th>
        <th style="width: 180px;">Hành động</th>
      </tr>
    </thead>
    <tbody>
      <?php while($u = mysqli_fetch_assoc($users)): ?>
      <tr>
        <form method="POST">
          <td><?= $u['id'] ?><input type="hidden" name="id" value="<?= $u['id'] ?>"></td>
          <td><input type="text" name="username" class="form-control" value="<?= htmlspecialchars($u['username']) ?>"></td>
          <td><input type="text" name="password" class="form-control" value="<?= htmlspecialchars($u['password']) ?>"></td>
          <td>
            <?php if($u['role'] == 'admin'): ?>
              <input type="text" class="form-control" value="admin" disabled>
              <input type="hidden" name="role" value="admin">
            <?php else: ?>
              <select name="role" class="form-control">
                <option value="user" <?= $u['role']=='user'?'selected':'' ?>>User</option>
                <option value="librarian" <?= $u['role']=='librarian'?'selected':'' ?>>Librarian</option>
                <option value="admin" <?= $u['role']=='admin'?'selected':'' ?>>Admin</option>
              </select>
            <?php endif; ?>
          </td>
          <td>
            <!-- Lưu -->
            <button type="submit" name="edit" class="btn btn-warning btn-sm">💾 Lưu</button>

            <!-- Chặn xóa admin -->
            <?php if ($u['role'] != 'admin'): ?>
              <a href="users.php?delete=<?= $u['id'] ?>" onclick="return confirm('Xóa người dùng này?');" class="btn btn-danger btn-sm">🗑 Xóa</a>
            <?php else: ?>
              <button class="btn btn-secondary btn-sm" disabled>🔒 Không xóa</button>
            <?php endif; ?>
          </td>
        </form>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
