<?php
header("Content-Type: text/html; charset=UTF-8");
session_start();
include 'db.php';
mysqli_set_charset($conn, "utf8mb4");
error_reporting(E_ALL); ini_set('display_errors',1);

// ✅ Chỉ admin & librarian mới vào được
if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] != 'admin' && $_SESSION['user']['role'] != 'librarian')) {
    die("❌ Bạn không có quyền truy cập trang này!");
}

$user = $_SESSION['user'];
$role = $user['role'];
$msg = "";

// ✅ Tạo thư mục upload nếu chưa có
$upload_dir = __DIR__ . "/uploads/books/";
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0775, true);
}

// ✅ Xóa sách
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    // Xóa ảnh bìa cũ
    $res = mysqli_query($conn, "SELECT image FROM books WHERE id=$id");
    if ($res && mysqli_num_rows($res)>0){
        $img = mysqli_fetch_assoc($res);
        if(!empty($img['image']) && file_exists($upload_dir.$img['image'])){
            unlink($upload_dir.$img['image']);
        }
    }
    mysqli_query($conn, "DELETE FROM books WHERE id=$id");
    header("Location: books.php");
    exit;
}

// ✅ Thêm sách mới
if (isset($_POST['add'])) {
    $title    = mysqli_real_escape_string($conn, $_POST['title']);
    $author   = mysqli_real_escape_string($conn, $_POST['author']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $quantity = intval($_POST['quantity']);

    $image_name = NULL;
    if(isset($_FILES['image']) && $_FILES['image']['error']==0){
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif'];
        if(in_array($ext,$allowed)){
            $image_name = time().'_'.uniqid().'.'.$ext;
            move_uploaded_file($_FILES['image']['tmp_name'],$upload_dir.$image_name);
        }
    }

    mysqli_query($conn, "INSERT INTO books (title,author,category,quantity,image) VALUES ('$title','$author','$category',$quantity,".($image_name?"'$image_name'":"NULL").")");
    header("Location: books.php");
    exit;
}

// ✅ Sửa sách
if (isset($_POST['edit'])) {
    $id       = intval($_POST['id']);
    $title    = mysqli_real_escape_string($conn, $_POST['title']);
    $author   = mysqli_real_escape_string($conn, $_POST['author']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $quantity = intval($_POST['quantity']);

    // Kiểm tra nếu upload ảnh mới
    $image_sql = "";
    if(isset($_FILES['image']) && $_FILES['image']['error']==0){
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif'];
        if(in_array($ext,$allowed)){
            // Xóa ảnh cũ
            $old = mysqli_query($conn,"SELECT image FROM books WHERE id=$id");
            if($old && mysqli_num_rows($old)>0){
                $o = mysqli_fetch_assoc($old);
                if(!empty($o['image']) && file_exists($upload_dir.$o['image'])){
                    unlink($upload_dir.$o['image']);
                }
            }
            // Upload mới
            $image_name = time().'_'.uniqid().'.'.$ext;
            move_uploaded_file($_FILES['image']['tmp_name'],$upload_dir.$image_name);
            $image_sql = ", image='$image_name'";
        }
    }

    mysqli_query($conn, "UPDATE books SET title='$title', author='$author', category='$category', quantity=$quantity $image_sql WHERE id=$id");
    header("Location: books.php");
    exit;
}

// ✅ Lấy danh sách sách
$books = mysqli_query($conn, "SELECT * FROM books ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>📖 Quản lý sách - Thư viện số</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: #f4f6f9;
      font-family: 'Segoe UI', sans-serif;
    }
    .navbar-brand { font-weight: bold; }
    .container-content {
      max-width: 1100px;
      margin: 80px auto 40px auto;
      background: white;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    }
    .table th { background: #007bff; color: white; }
    .thumb { width: 60px; height: auto; border-radius:4px; }
  </style>
</head>
<body>

<!-- ✅ NAVBAR đồng bộ với Dashboard -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
  <div class="container-fluid">
    <a class="navbar-brand" href="dashboard.php">📚 Thư viện số</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link" href="dashboard.php">🏠 Dashboard</a></li>
        <li class="nav-item"><a class="nav-link active" href="books.php">📖 Quản lý sách</a></li>
        <?php if($role == 'admin'): ?>
          <li class="nav-item"><a class="nav-link" href="users.php">👥 Quản lý người dùng</a></li>
        <?php endif; ?>
        <li class="nav-item"><a class="nav-link" href="history.php">📜 Lịch sử mượn</a></li>
        <li class="nav-item"><a class="nav-link" target="_blank" href="books_list.php">🌐 Trang Public</a></li>
      </ul>
      <span class="navbar-text text-white me-3">
        Xin chào, <strong><?= htmlspecialchars($user['username']) ?></strong> (<?= $role ?>)
      </span>
      <a href="logout.php" class="btn btn-outline-light">🚪 Đăng xuất</a>
    </div>
  </div>
</nav>

<div class="container container-content">
  <h2 class="text-center mb-4">📖 Quản lý sách</h2>

  <!-- ✅ Form thêm sách -->
  <div class="card mb-4">
    <div class="card-header">➕ Thêm sách mới</div>
    <div class="card-body">
      <form method="POST" enctype="multipart/form-data">
        <div class="row g-2 mb-2">
          <div class="col-md-3"><input type="text" name="title" class="form-control" placeholder="Tiêu đề" required></div>
          <div class="col-md-3"><input type="text" name="author" class="form-control" placeholder="Tác giả" required></div>
          <div class="col-md-3"><input type="text" name="category" class="form-control" placeholder="Thể loại" required></div>
          <div class="col-md-2"><input type="number" name="quantity" class="form-control" placeholder="Số lượng" required></div>
          <div class="col-md-4 mt-2"><input type="file" name="image" class="form-control" accept="image/*"></div>
          <div class="col-md-2 mt-2"><button type="submit" name="add" class="btn btn-success w-100">Thêm</button></div>
        </div>
      </form>
    </div>
  </div>

  <!-- ✅ Bảng danh sách sách -->
  <table class="table table-bordered table-striped align-middle">
    <thead>
      <tr>
        <th>Ảnh</th>
        <th>Tiêu đề</th>
        <th>Tác giả</th>
        <th>Thể loại</th>
        <th>Số lượng</th>
        <th style="width: 220px;">Hành động</th>
      </tr>
    </thead>
    <tbody>
      <?php while($book = mysqli_fetch_assoc($books)): ?>
      <tr>
        <form method="POST" enctype="multipart/form-data">
          <td>
            <?php if(!empty($book['image'])): ?>
              <img src="uploads/books/<?= htmlspecialchars($book['image']) ?>" class="thumb">
            <?php else: ?>
              <span class="text-muted">Không có</span>
            <?php endif; ?>
            <input type="file" name="image" class="form-control form-control-sm mt-1" accept="image/*">
          </td>
          <td>
            <input type="hidden" name="id" value="<?= $book['id'] ?>">
            <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($book['title']) ?>">
          </td>
          <td><input type="text" name="author" class="form-control" value="<?= htmlspecialchars($book['author']) ?>"></td>
          <td><input type="text" name="category" class="form-control" value="<?= htmlspecialchars($book['category']) ?>"></td>
          <td><input type="number" name="quantity" class="form-control" value="<?= $book['quantity'] ?>"></td>
          <td>
            <button type="submit" name="edit" class="btn btn-warning btn-sm">💾 Lưu</button>
            <a href="books.php?delete=<?= $book['id'] ?>" onclick="return confirm('Xóa sách này?');" class="btn btn-danger btn-sm">🗑 Xóa</a>
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
