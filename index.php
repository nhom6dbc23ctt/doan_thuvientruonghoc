<?php
header("Content-Type: text/html; charset=UTF-8");
include 'db.php';
mysqli_set_charset($conn, "utf8mb4");

// Lấy danh mục thể loại
$categories = mysqli_query($conn, "SELECT DISTINCT category FROM books");

// Nếu chọn filter theo category
$filter = isset($_GET['category']) ? mysqli_real_escape_string($conn, $_GET['category']) : '';
if ($filter) {
    $books = mysqli_query($conn, "SELECT * FROM books WHERE category='$filter' ORDER BY id DESC");
} else {
    $books = mysqli_query($conn, "SELECT * FROM books ORDER BY id DESC");
}

// ✅ Thư mục chứa ảnh bìa
$upload_path = "uploads/books/";
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Thư viện Trường học số</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { font-family: 'Segoe UI', sans-serif; background-color: #f4f6f9; }
    .hero { background: linear-gradient(135deg, #007bff, #00c6ff); color: white; text-align: center; padding: 60px 20px; }
    .hero h1 { font-size: 2.5rem; font-weight: bold; }
    .category-menu { text-align: center; margin: 20px auto; }
    .category-menu a { margin: 5px; text-decoration: none; }
    .book-card { box-shadow: 0 5px 15px rgba(0,0,0,0.1); border-radius: 10px; background: white; padding: 15px; text-align: center; }
    .book-card img { width: 100%; border-radius: 10px; height: 220px; object-fit: cover; }
    .book-card h5 { color: #007bff; font-weight: bold; margin-top: 10px; }
    footer { background: #222; color: #ccc; text-align: center; padding: 15px; margin-top: 40px; }
  </style>
</head>
<body>

<!-- ✅ NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container">
    <a class="navbar-brand" href="index.php">📚 Thư viện Số</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link active" href="index.php">🏠 Trang chủ</a></li>
        <li class="nav-item"><a class="nav-link" href="login.php">🔑 Đăng nhập</a></li>
        <li class="nav-item"><a class="nav-link" href="register.php">📝 Đăng ký</a></li>
      </ul>
    </div>
  </div>
</nav>

<!-- ✅ BANNER GIỚI THIỆU -->
<div class="hero">
  <h1>📖 Chào mừng đến Thư viện Trường học số</h1>
  <p>Kho sách điện tử dành cho học sinh, sinh viên và giáo viên – Mượn sách mọi lúc, mọi nơi</p>
  <a href="login.php" class="btn btn-light btn-lg mt-3">🔑 Đăng nhập để bắt đầu</a>
</div>

<!-- ✅ MENU PHÂN LOẠI SÁCH -->
<div class="container">
  <h2 class="text-center mt-4">📌 Phân loại sách</h2>
  <div class="category-menu">
    <a href="index.php" class="btn btn-outline-secondary btn-sm <?= $filter==''?'active':'' ?>">Tất cả</a>
    <?php while($cat = mysqli_fetch_assoc($categories)): ?>
      <a href="index.php?category=<?= urlencode($cat['category']) ?>" 
         class="btn btn-outline-primary btn-sm <?= $filter==$cat['category']?'active':'' ?>">
        <?= htmlspecialchars($cat['category']) ?>
      </a>
    <?php endwhile; ?>
  </div>
</div>

<!-- ✅ DANH SÁCH SÁCH -->
<div class="container mt-4">
  <h2 class="text-center mb-4">📚 Sách <?= $filter ? htmlspecialchars($filter) : 'mới nhất' ?></h2>
  <div class="row g-4">
    <?php while($book = mysqli_fetch_assoc($books)): ?>
      <div class="col-md-3">
        <div class="book-card">
          <?php
            // ✅ Nếu có ảnh bìa thật trong uploads/books/
            if(!empty($book['image']) && file_exists($upload_path.$book['image'])) {
                $cover = $upload_path . htmlspecialchars($book['image']);
            } else if(!empty($book['cover_url'])) {
                // Nếu không có image thì dùng cover_url cũ
                $cover = $book['cover_url'];
            } else {
                // Nếu không có gì thì dùng ảnh mặc định
                $cover = "default-cover.jpg";
            }
          ?>
          <img src="<?= $cover ?>" alt="Bìa sách">
          
          <h5><?= htmlspecialchars($book['title']) ?></h5>
          <p><small>Tác giả: <?= htmlspecialchars($book['author']) ?></small></p>
          <?php if($book['quantity'] > 0): ?>
            <span class="badge bg-success">Còn <?= $book['quantity'] ?> cuốn</span>
          <?php else: ?>
            <span class="badge bg-danger">Hết sách</span>
          <?php endif; ?>
        </div>
      </div>
    <?php endwhile; ?>
  </div>
</div>

<!-- ✅ FOOTER -->
<footer>
  <p>© <?= date("Y") ?> Thư viện Trường học số | PHP + MySQL + Bootstrap</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
