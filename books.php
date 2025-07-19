<?php
session_start();
include 'db.php';

// Kiểm tra quyền truy cập
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] == 'user') {
    die("❌ Bạn không có quyền truy cập trang này!");
}

// Xử lý thêm sách
if (isset($_POST['title'])) {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $qty = $_POST['quantity'];

    mysqli_query($conn, "INSERT INTO books (title, author, quantity) VALUES ('$title','$author',$qty)");
    $msg = "<div class='alert alert-success'>✅ Đã thêm sách mới thành công!</div>";
}

// Xử lý xóa sách
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM books WHERE id=$id");
    $msg = "<div class='alert alert-warning'>⚠️ Đã xóa sách!</div>";
}

// Lấy danh sách sách
$books = mysqli_query($conn, "SELECT * FROM books ORDER BY id DESC");
?>

<?php include 'header.php'; ?>

<div class="container-box">
  <h2 class="mb-4">📚 Quản lý sách</h2>

  <?php if(isset($msg)) echo $msg; ?>

  <!-- Form thêm sách -->
  <form method="post" class="row g-2 mb-4">
    <div class="col-md-4">
      <input name="title" class="form-control" placeholder="Tên sách" required>
    </div>
    <div class="col-md-4">
      <input name="author" class="form-control" placeholder="Tác giả" required>
    </div>
    <div class="col-md-2">
      <input name="quantity" type="number" class="form-control" value="1" required>
    </div>
    <div class="col-md-2">
      <button class="btn btn-success w-100">➕ Thêm sách</button>
    </div>
  </form>

  <!-- Bảng danh sách sách -->
  <table class="table table-striped table-hover">
    <thead class="table-primary">
      <tr>
        <th>#</th>
        <th>Tên sách</th>
        <th>Tác giả</th>
        <th>Số lượng</th>
        <th>Hành động</th>
      </tr>
    </thead>
    <tbody>
      <?php 
      $stt = 1;
      while($b = mysqli_fetch_assoc($books)): ?>
        <tr>
          <td><?= $stt++ ?></td>
          <td><?= htmlspecialchars($b['title']) ?></td>
          <td><?= htmlspecialchars($b['author']) ?></td>
          <td>
            <?php if($b['quantity'] <= 0): ?>
              <span class="badge bg-danger">Hết sách</span>
            <?php else: ?>
              <span class="badge bg-success"><?= $b['quantity'] ?></span>
            <?php endif; ?>
          </td>
          <td>
            <a href="?delete=<?= $b['id'] ?>" 
               onclick="return confirm('Bạn chắc chắn muốn xóa sách này?')"
               class="btn btn-sm btn-outline-danger">
               ❌ Xóa
            </a>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>

  <a href="dashboard.php" class="btn btn-secondary">⬅ Quay lại Dashboard</a>
</div>

<?php include 'footer.php'; ?>
