<?php
header("Content-Type: text/html; charset=UTF-8");
session_start();
include 'db.php';
mysqli_set_charset($conn,"utf8mb4");
error_reporting(E_ALL); ini_set('display_errors',1);

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}
$user_id = intval($_SESSION['user']['id']);
$msg = "";

// Khi bấm mượn
if(isset($_POST['borrow_book'])){
    $book_id = intval($_POST['book_id']);
    $bk = mysqli_query($conn,"SELECT quantity,title FROM books WHERE id=$book_id");
    if($bk && mysqli_num_rows($bk)>0){
        $b = mysqli_fetch_assoc($bk);
        if($b['quantity']>0){
            mysqli_query($conn,"INSERT INTO borrow_records(user_id,book_id,borrow_date,return_date) VALUES($user_id,$book_id,CURDATE(),NULL)");
            if(mysqli_error($conn)) $msg .= mysqli_error($conn);
            mysqli_query($conn,"UPDATE books SET quantity=quantity-1 WHERE id=$book_id");
            if(mysqli_error($conn)) $msg .= mysqli_error($conn);
            $msg = "✅ Mượn thành công: <b>".htmlspecialchars($b['title'])."</b>";
        }else{
            $msg="⚠️ Sách đã hết!";
        }
    } else {
        $msg="❌ Không tìm thấy sách!";
    }
}

$result = mysqli_query($conn,"SELECT * FROM books ORDER BY title");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Mượn sách</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body{background:#f4f6f9;}
.container-content{max-width:1000px;margin:80px auto;background:white;padding:30px;border-radius:12px;}
.badge-out{background:red;color:white;padding:4px 6px;border-radius:4px;}
</style>
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container container-content">
  <h2 class="mb-3 text-center">📚 Danh sách sách</h2>
  <?php if(!empty($msg)):?><div class="alert alert-info text-center"><?=$msg?></div><?php endif;?>

  <table class="table table-striped">
    <thead><tr><th>Tựa sách</th><th>Tác giả</th><th>Còn</th><th>Hành động</th></tr></thead>
    <tbody>
    <?php while($row=mysqli_fetch_assoc($result)): ?>
      <tr>
        <td><?=htmlspecialchars($row['title'])?></td>
        <td><?=htmlspecialchars($row['author'])?></td>
        <td><?=($row['quantity']>0)?$row['quantity']:"<span class='badge-out'>Hết</span>"?></td>
        <td>
          <?php if($row['quantity']>0): ?>
          <form method="POST">
            <input type="hidden" name="book_id" value="<?=$row['id']?>">
            <button type="submit" name="borrow_book" class="btn btn-success btn-sm">+ Mượn</button>
          </form>
          <?php else: ?>
          <button class="btn btn-secondary btn-sm" disabled>Không khả dụng</button>
          <?php endif;?>
        </td>
      </tr>
    <?php endwhile;?>
    </tbody>
  </table>
</div>
</body>
</html>
