<?php
header("Content-Type: text/html; charset=UTF-8");
session_start();
include 'db.php';
mysqli_set_charset($conn, "utf8mb4");
error_reporting(E_ALL); ini_set('display_errors',1);

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}
$user_id = intval($_SESSION['user']['id']);

// Đảm bảo giỏ sách tồn tại
if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

$msg = "";

// Khi bấm "Thêm vào giỏ"
if (isset($_POST['add_to_cart'])) {
    $book_id = intval($_POST['book_id']);
    if ($book_id > 0) {
        // Kiểm tra sách còn không
        $bk = mysqli_query($conn, "SELECT quantity,title FROM books WHERE id=$book_id");
        if ($bk && mysqli_num_rows($bk) > 0) {
            $b = mysqli_fetch_assoc($bk);
            if ($b['quantity'] > 0) {
                if (!in_array($book_id, $_SESSION['cart'])) {
                    $_SESSION['cart'][] = $book_id;
                    $msg = "✅ Đã thêm sách <b>" . htmlspecialchars($b['title']) . "</b> vào giỏ!";
                } else {
                    $msg = "⚠️ Sách này đã có trong giỏ!";
                }
            } else {
                $msg = "⚠️ Sách đã hết!";
            }
        } else {
            $msg = "❌ Không tìm thấy sách!";
        }
    }
}

// Lấy danh sách sách
$result = mysqli_query($conn, "SELECT * FROM books ORDER BY title");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>📚 Mượn sách</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { background:#f4f6f9; }
.container-content { max-width:1000px; margin:80px auto; background:white; padding:30px; border-radius:12px; }
.badge-out { background:red; color:white; padding:4px 6px; border-radius:4px; }
</style>
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container container-content">
  <h2 class="mb-3 text-center">📚 Danh sách sách có thể mượn</h2>
  
  <?php if (!empty($msg)): ?>
    <div class="alert alert-info text-center"><?= $msg ?></div>
  <?php endif; ?>

  <div class="text-end mb-3">
    <a href="cart.php" class="btn btn-success">🛒 Xem giỏ (<?= count($_SESSION['cart']) ?>)</a>
  </div>

  <table class="table table-striped">
    <thead>
      <tr>
        <th>Tựa sách</th>
        <th>Tác giả</th>
        <th>Còn</th>
        <th>Hành động</th>
      </tr>
    </thead>
    <tbody>
    <?php while ($row = mysqli_fetch_assoc($result)): ?>
      <tr>
        <td><?= htmlspecialchars($row['title']) ?></td>
        <td><?= htmlspecialchars($row['author']) ?></td>
        <td><?= ($row['quantity'] > 0) ? $row['quantity'] : "<span class='badge-out'>Hết</span>" ?></td>
        <td>
          <?php if ($row['quantity'] > 0): ?>
            <form method="POST" style="display:inline;">
              <input type="hidden" name="book_id" value="<?= $row['id'] ?>">
              <button type="submit" name="add_to_cart" class="btn btn-primary btn-sm">➕ Thêm giỏ</button>
            </form>
          <?php else: ?>
            <button class="btn btn-secondary btn-sm" disabled>Không khả dụng</button>
          <?php endif; ?>
        </td>
      </tr>
    <?php endwhile; ?>
    </tbody>
  </table>
</div>
</body>
</html>
