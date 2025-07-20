<?php
header("Content-Type: text/html; charset=UTF-8");
session_start();
include 'db.php';
mysqli_set_charset($conn,"utf8mb4");
error_reporting(E_ALL); ini_set('display_errors',1);

if(!isset($_SESSION['user'])){
    header("Location: login.php"); exit;
}
$user = $_SESSION['user'];
$role = $user['role'];
$user_id = intval($user['id']);
$msg="";

// Nếu thủ thư chọn user khác
if($role=='librarian' && isset($_POST['select_user'])){
    $user_id = intval($_POST['select_user']);
}

// Trả sách
if(isset($_GET['record_id'])){
    $record_id=intval($_GET['record_id']);
    $r=mysqli_query($conn,"SELECT book_id FROM borrow_records WHERE id=$record_id AND return_date IS NULL");
    if($r && mysqli_num_rows($r)>0){
        $row=mysqli_fetch_assoc($r);
        $book_id=intval($row['book_id']);
        // Update return_date
        $u1=mysqli_query($conn,"UPDATE borrow_records SET return_date=CURDATE() WHERE id=$record_id");
        // Update quantity
        $u2=mysqli_query($conn,"UPDATE books SET quantity=quantity+1 WHERE id=$book_id");
        if($u1 && $u2){
            $msg="✅ Đã trả sách thành công!";
        }else{
            $msg="❌ Lỗi SQL: ".mysqli_error($conn);
        }
    } else {
        $msg="⚠️ Không tìm thấy record hoặc đã trả rồi!";
    }
}

// Lấy sách đang mượn
$records=mysqli_query($conn,"
  SELECT br.id,b.title,b.author,br.borrow_date
  FROM borrow_records br
  JOIN books b ON br.book_id=b.id
  WHERE br.user_id=$user_id AND br.return_date IS NULL
");
$members=($role=='librarian')?mysqli_query($conn,"SELECT id,username FROM users WHERE role='user'"):null;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Trả sách</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container mt-5">
  <h2 class="text-center mb-4">↩️ Trả sách</h2>
  <?php if(!empty($msg)):?><div class="alert alert-info text-center"><?=$msg?></div><?php endif;?>

  <?php if($role=='librarian' && $members):?>
  <form method="POST" class="mb-3 d-flex gap-2">
    <select name="select_user" class="form-select" style="width:250px;">
      <?php while($m=mysqli_fetch_assoc($members)):?>
      <option value="<?=$m['id']?>" <?=($user_id==$m['id']?'selected':'')?>><?=htmlspecialchars($m['username'])?></option>
      <?php endwhile;?>
    </select>
    <button class="btn btn-secondary">Xem sách đang mượn</button>
  </form>
  <?php endif;?>

  <table class="table table-hover">
    <thead class="table-primary">
      <tr><th>Tên sách</th><th>Tác giả</th><th>Ngày mượn</th><th>Trả</th></tr>
    </thead>
    <tbody>
      <?php if($records && mysqli_num_rows($records)>0):?>
        <?php while($r=mysqli_fetch_assoc($records)):?>
        <tr>
          <td><?=htmlspecialchars($r['title'])?></td>
          <td><?=htmlspecialchars($r['author'])?></td>
          <td><?=$r['borrow_date']?></td>
          <td><a href="return.php?record_id=<?=$r['id']?>" class="btn btn-warning btn-sm" onclick="return confirm('Xác nhận trả sách?');">↩️ Trả</a></td>
        </tr>
        <?php endwhile;?>
      <?php else:?>
        <tr><td colspan="4" class="text-center text-muted">Không có sách nào đang mượn</td></tr>
      <?php endif;?>
    </tbody>
  </table>
</div>
</body>
</html>
