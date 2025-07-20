<?php
header("Content-Type: text/html; charset=UTF-8");
session_start(); // ✅ vẫn giữ session để nhận diện user
include 'db.php';
mysqli_set_charset($conn,"utf8mb4");

// ✅ Nếu đã login sẽ có $_SESSION['user']
$current_user = $_SESSION['user'] ?? null;
$role = $current_user['role'] ?? 'guest';

// ✅ Lấy danh sách sách
$books = mysqli_query($conn,"SELECT * FROM books ORDER BY title ASC");

// ✅ Thư mục ảnh bìa
$upload_path = "uploads/books/";
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>🌐 Trang Public - Danh sách sách</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background:#f4f6f9; font-family:'Segoe UI',sans-serif; }
    .container-content {
      max-width: 1000px; margin: 80px auto;
      background: white; padding: 30px;
      border-radius: 12px; box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    }
    .badge-out { background:red; color:white; padding:4px 6px; border-radius:6px; }
    .thumb { width:60px; height:auto; border-radius:4px; }
  </style>
</head>
<body>

<div class="container container-content">
  <h2 class="text-center mb-4">🌐 Danh sách sách (Public)</h2>

  <!-- ✅ Nhận diện trạng thái -->
  <?php if($current_user): ?>
    <div class="alert alert-info text-center">
      Bạn đang đăng nhập: <b><?= htmlspecialchars($current_user['username']) ?></b> (<?= $role ?>)
      <br><i>Trang Public chỉ cho xem sách, không thao tác mượn/trả.</i>
    </div>
  <?php else: ?>
    <div class="alert alert-secondary text-center">
      Bạn đang xem ở chế độ <b>Khách</b> (chưa đăng nhập)
    </div>
  <?php endif; ?>

  <!-- ✅ Hiển thị danh sách sách -->
  <table class="table table-hover table-bordered align-middle">
    <thead class="table-primary">
      <tr>
        <th style="width:10%;">Ảnh</th>
        <th style="width:30%;">Tên sách</th>
        <th style="width:20%;">Tác giả</th>
        <th style="width:20%;">Thể loại</th>
        <th style="width:20%;">Số lượng</th>
      </tr>
    </thead>
    <tbody>
      <?php while($b=mysqli_fetch_assoc($books)): ?>
        <tr>
          <!-- Ảnh bìa -->
          <td>
            <?php if(!empty($b['image']) && file_exists($upload_path.$b['image'])): ?>
              <img src="<?= $upload_path . htmlspecialchars($b['image']) ?>" class="thumb">
            <?php else: ?>
              <span class="text-muted">Không có</span>
            <?php endif; ?>
          </td>

          <!-- Thông tin sách -->
          <td><?= htmlspecialchars($b['title']) ?></td>
          <td><?= htmlspecialchars($b['author']) ?></td>
          <td><?= htmlspecialchars($b['category']) ?></td>
          <td>
            <?php if($b['quantity']>0): ?>
              <?= $b['quantity'] ?>
            <?php else: ?>
              <span class="badge-out">Hết</span>
            <?php endif; ?>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>

  <!-- ✅ Gợi ý quay lại -->
  <div class="text-center mt-3">
    <?php if($current_user): ?>
      <a href="dashboard.php" class="btn btn-primary">⬅️ Quay lại Dashboard</a>
    <?php else: ?>
      <a href="login.php" class="btn btn-success">🔑 Đăng nhập để mượn sách</a>
    <?php endif; ?>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
