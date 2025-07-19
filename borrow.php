<?php
session_start();
if (!isset($_SESSION['user'])) die("Bạn cần đăng nhập!");
include 'db.php';

if (isset($_POST['book_id'])) {
    $uid = $_SESSION['user']['id'];
    $bid = $_POST['book_id'];
    $today = date('Y-m-d');
    mysqli_query($conn, "INSERT INTO borrow_records (user_id, book_id, borrow_date) VALUES ($uid, $bid, '$today')");
    mysqli_query($conn, "UPDATE books SET quantity = quantity - 1 WHERE id = $bid");
    $msg = "<div class='alert alert-success'>✅ Mượn sách thành công!</div>";
}

$books = mysqli_query($conn, "SELECT * FROM books WHERE quantity > 0");
include 'header.php';
?>

<div class="container-box">
  <h2>✅ Mượn sách</h2>
  <?php if(isset($msg)) echo $msg; ?>
  <form method="post" class="row g-2">
    <div class="col-md-8">
      <select name="book_id" class="form-select">
        <?php while($b = mysqli_fetch_assoc($books)): ?>
        <option value="<?= $b['id'] ?>"><?= $b['title'] ?> - <?= $b['author'] ?> (Còn <?= $b['quantity'] ?>)</option>
        <?php endwhile; ?>
      </select>
    </div>
    <div class="col-md-4">
      <button class="btn btn-primary w-100">Mượn ngay</button>
    </div>
  </form>
  <a href="dashboard.php" class="btn btn-secondary mt-3">⬅ Quay lại Dashboard</a>
</div>

<?php include 'footer.php'; ?>
