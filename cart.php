<?php
session_start();
include 'db.php';
mysqli_set_charset($conn,"utf8mb4");
error_reporting(E_ALL); ini_set('display_errors',1);

if(!isset($_SESSION['user'])) { header("Location: login.php"); exit; }
$user_id = intval($_SESSION['user']['id']);
if(!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

$msg="";

// Thêm vào giỏ
if(isset($_POST['add_to_cart'])){
    $book_id=intval($_POST['book_id']);
    if(!in_array($book_id,$_SESSION['cart'])){
        $_SESSION['cart'][]=$book_id;
    }
}

// Xóa khỏi giỏ
if(isset($_GET['remove'])){
    $remove=intval($_GET['remove']);
    $_SESSION['cart']=array_diff($_SESSION['cart'],[$remove]);
}

// Xác nhận mượn tất cả
if(isset($_POST['checkout'])){
    if(empty($_SESSION['cart'])){
        $msg="⚠️ Giỏ trống!";
    }else{
        foreach($_SESSION['cart'] as $bid){
            $bk=mysqli_query($conn,"SELECT title,quantity FROM books WHERE id=$bid");
            if($bk && mysqli_num_rows($bk)>0){
                $b=mysqli_fetch_assoc($bk);
                if($b['quantity']>0){
                    mysqli_query($conn,"INSERT INTO borrow_records(user_id,book_id,borrow_date,return_date) VALUES($user_id,$bid,CURDATE(),NULL)");
                    mysqli_query($conn,"UPDATE books SET quantity=quantity-1 WHERE id=$bid");
                } else {
                    $msg.="⚠️ ".$b['title']." đã hết<br>";
                }
            }
        }
        $_SESSION['cart']=[];
        $msg.="✅ Đã mượn các sách còn đủ";
    }
}

// Lấy info sách trong giỏ
$books=[];
if(!empty($_SESSION['cart'])){
    $ids=implode(",",$_SESSION['cart']);
    $books=mysqli_query($conn,"SELECT * FROM books WHERE id IN($ids)");
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Giỏ sách</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container mt-5">
  <h2 class="text-center">🛒 Giỏ sách</h2>
  <?php if(!empty($msg)):?><div class="alert alert-info"><?=$msg?></div><?php endif;?>

  <?php if(empty($_SESSION['cart'])):?>
    <p>Giỏ sách trống. <a href="borrow.php">Chọn sách</a></p>
  <?php else:?>
  <form method="POST">
  <table class="table table-bordered">
    <thead><tr><th>Tựa sách</th><th>Tác giả</th><th>Còn</th><th>Xóa</th></tr></thead>
    <tbody>
      <?php while($row=mysqli_fetch_assoc($books)):?>
      <tr>
        <td><?=htmlspecialchars($row['title'])?></td>
        <td><?=htmlspecialchars($row['author'])?></td>
        <td><?=$row['quantity']?></td>
        <td><a href="cart.php?remove=<?=$row['id']?>" class="btn btn-danger btn-sm">❌</a></td>
      </tr>
      <?php endwhile;?>
    </tbody>
  </table>
  <button type="submit" name="checkout" class="btn btn-success">✅ Xác nhận mượn tất cả</button>
  </form>
  <?php endif;?>
</div>
</body>
</html>
