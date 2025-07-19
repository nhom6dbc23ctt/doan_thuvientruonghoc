<?php
session_start();
include 'db.php';

// Nếu đã đăng nhập và không phải admin thì chặn
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    die("Bạn phải là admin mới được tạo tài khoản!");
}

// Xử lý khi submit form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password']; // Lưu plain text (không mã hóa)
    $role = $_POST['role'];

    // Kiểm tra trùng username
    $check = mysqli_query($conn, "SELECT * FROM users WHERE username='$username'");
    if (mysqli_num_rows($check) > 0) {
        $msg = "<p class='text-danger'>Tên đăng nhập đã tồn tại!</p>";
    } else {
        mysqli_query($conn, "INSERT INTO users (username, password, role) VALUES ('$username', '$password', '$role')");
        $msg = "<p class='text-success'>✅ Tạo tài khoản thành công!</p>";
    }
}
?>
<?php include 'header.php'; ?>
<h2>Tạo tài khoản người dùng</h2>

<form method="post" class="mb-3">
    <label>Tên đăng nhập:</label>
    <input type="text" name="username" class="form-control mb-2" required>

    <label>Mật khẩu:</label>
    <input type="text" name="password" class="form-control mb-2" required>

    <label>Vai trò:</label>
    <select name="role" class="form-select mb-2">
        <option value="user">Người dùng (user)</option>
        <option value="librarian">Thủ thư (librarian)</option>
        <option value="admin">Quản trị (admin)</option>
    </select>

    <button type="submit" class="btn btn-success">Tạo tài khoản</button>
</form>

<?php if (isset($msg)) echo $msg; ?>

<a href="dashboard.php" class="btn btn-secondary">⬅ Quay lại Dashboard</a>
<?php include 'footer.php'; ?>
