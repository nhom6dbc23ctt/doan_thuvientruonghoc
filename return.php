<?php
session_start();
if (!isset($_SESSION['user'])) die("Bạn cần đăng nhập!");
include 'db.php';

if (isset($_POST['record_id'])) {
    $rid = $_POST['record_id'];
    $today = date('Y-m-d');
    $record = mysqli_fetch_assoc(mysqli_query($conn, "SELECT book_id FROM borrow_records WHERE id = $rid"));
    mysqli_query($conn, "UPDATE borrow_records SET return_date = '$today' WHERE id = $rid");
    mysqli_query($conn, "UPDATE books SET quantity = quantity + 1 WHERE id = ".$record['book_id']);
    $msg = "<div class='alert alert-success'>✅ Đã trả sách thành công!</div>";
}

$uid = $_SESSION['user']['id'];
$records = mysqli_query($conn, "SELECT br.id, b.title, br.borrow_date 
                                FROM borrow_records br 
                                JOIN books b ON br.book_id = b.id 
                                WHERE br.user_id=$uid AND return_date IS NULL");

include 'header.php';
?>

<div class="container-box">
  <h2>↩️ Trả sách</h2>
  <?php if(isset($msg)) echo $msg; ?>
  <form method="post" class="row g-2">
    <div class="col-md-8">
      <select name="record_id" class="form-select">
        <?php while($r = mysqli_fetch_assoc($records)): ?>
        <option value="<?= $r['id'] ?>"><?= $r['title'] ?> - mượn ngày <?= $r['borrow_date'] ?></option>
        <?php endwhile; ?>
      </select>
    </div>
    <div class="col-md-4">
      <button class="btn btn-warning w-100">Trả sách</button>
    </div>
  </form>
  <a href="dashboard.php" class="btn btn-secondary mt-3">⬅ Quay lại Dashboard</a>
</div>

<?php include 'footer.php'; ?>
