<?php
header("Content-Type: text/html; charset=UTF-8");
session_start();
include 'db.php';
mysqli_set_charset($conn, "utf8mb4");

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];
$records = mysqli_query($conn,
  "SELECT br.borrow_date, b.title, b.author 
   FROM borrow_records br 
   JOIN books b ON br.book_id = b.id 
   WHERE br.user_id = $user_id 
   ORDER BY br.borrow_date DESC");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Lịch sử mượn sách</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background: #f4f6f9; font-family: 'Segoe UI', sans-serif; }
    .container-content {
      max-width: 1000px;
      margin: 80px auto;
      background: white;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    }
  </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container container-content">
  <h2 class="text-center">📜 Lịch sử mượn sách</h2>
  <table class="table table-striped mt-4">
    <thead class="table-primary">
      <tr>
        <th>📖 Tên sách</th>
        <th>✍️ Tác giả</th>
        <th>📅 Ngày mượn</th>
      </tr>
    </thead>
    <tbody>
      <?php if(mysqli_num_rows($records) > 0): ?>
        <?php while($r = mysqli_fetch_assoc($records)): ?>
          <tr>
            <td><?= htmlspecialchars($r['title']) ?></td>
            <td><?= htmlspecialchars($r['author']) ?></td>
            <td><?= $r['borrow_date'] ?></td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="3" class="text-center text-muted">Bạn chưa mượn sách nào</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
