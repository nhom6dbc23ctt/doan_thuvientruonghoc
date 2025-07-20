<?php
session_start();
include 'db.php';
mysqli_set_charset($conn, "utf8mb4");

// Lấy ID sách từ URL
if (!isset($_GET['id'])) {
    die("❌ Không tìm thấy sách!");
}
$book_id = intval($_GET['id']);

// Truy vấn sách
$sql = "SELECT * FROM books WHERE id = $book_id";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) == 0) {
    die("❌ Sách không tồn tại!");
}
$book = mysqli_fetch_assoc($result);

// Kiểm tra đăng nhập
$logged_in = isset($_SESSION['user']);
$msg = "";

// Xử lý mượn sách
if (isset($_POST['borrow']) && $logged_in) {
    $user_id = $_SESSION['user']['id'];
    if ($book['quantity'] > 0) {
        mysqli_query($conn, "INSERT INTO borrow_records (user_id, book_id, borrow_date) VALUES ($user_id, $book_id, NOW())");
        mysqli_query($conn, "UPDATE books SET quantity = quantity - 1 WHERE id = $book_id");
        $msg = "<div class='alert alert-success text-center'>✅ Bạn đã mượn sách thành công!</div>";
        // Reload sách mới
        $result = mysqli_query($conn, $sql);
        $book = mysqli_fetch_assoc($result);
    } else {
        $msg = "<div class='alert alert-danger text-center'>❌ Sách đã hết!</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($book['title']) ?> | Chi tiết sách</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background-color: #f4f6f9; font-family: 'Segoe UI', sans-serif; }
    .book-detail { max-width: 900px; margin: 40px auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
    .book-cover { width: 100%; max-width: 250px; border-radius: 10px; }
  </style>
</head>
<body>

<div class="container">
  <div class="book-detail">
    <h2 class="text-center">📖 <?= htmlspecialchars($book['title']) ?></h2>
    <div class="row mt-4">
      <div class="col-md-4 text-center">
        <img src="uploads/covers/<?= $book['cover_url'] ?>" class="book-cover" alt="Bìa sách">
      </div>
      <div class="col-md-8">
        <p><strong>Tác giả:</strong> <?= htmlspecialchars($book['author']) ?></p>
        <p><strong>Thể loại:</strong> <?= htmlspecialchars($book['category']) ?></p>
        <p><strong>Số lượng còn:</strong> <?= $book['quantity'] ?></p>
        <p><strong>Mô tả:</strong> <?= nl2br(htmlspecialchars($book['description'])) ?></p>
        
        <?php if($msg) echo $msg; ?>

        <?php if ($logged_in): ?>
          <form method="POST">
            <button type="submit" name="borrow" class="btn btn-primary" <?= $book['quantity'] == 0 ? 'disabled' : '' ?>>📚 Mượn sách</button>
            <a href="index.php" class="btn btn-secondary">⬅ Quay lại</a>
          </form>
        <?php else: ?>
          <div class="alert alert-warning">🔑 Bạn cần <a href="login.php">đăng nhập</a> để mượn sách</div>
          <a href="index.php" class="btn btn-secondary">⬅ Quay lại</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
