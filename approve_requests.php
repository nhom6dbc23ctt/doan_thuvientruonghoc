<?php
session_start();
include 'db.php';
mysqli_set_charset($conn,"utf8mb4");
if(!isset($_SESSION['user']) || ($_SESSION['user']['role']!='admin' && $_SESSION['user']['role']!='librarian')){
    die("❌ Bạn không có quyền!");
}

// Duyệt yêu cầu
if(isset($_GET['approve'])){
    $id=intval($_GET['approve']);
    $req=mysqli_query($conn,"SELECT book_id,status FROM borrow_records WHERE id=$id");
    if($req && mysqli_num_rows($req)>0){
        $row=mysqli_fetch_assoc($req);
        $book_id=$row['book_id'];

        if($row['status']=='pending'){
            // Duyệt mượn
            mysqli_query($conn,"UPDATE borrow_records SET status='borrowed' WHERE id=$id");
        } elseif($row['status']=='return_pending'){
            // Duyệt trả
            mysqli_query($conn,"UPDATE borrow_records SET status='returned', return_date=CURDATE() WHERE id=$id");
            mysqli_query($conn,"UPDATE books SET quantity=quantity+1 WHERE id=$book_id");
        }
    }
    header("Location: approve_requests.php");
    exit;
}

// Từ chối yêu cầu
if(isset($_GET['reject'])){
    $id=intval($_GET['reject']);
    $req=mysqli_query($conn,"SELECT book_id,status FROM borrow_records WHERE id=$id");
    if($req && mysqli_num_rows($req)>0){
        $row=mysqli_fetch_assoc($req);
        $book_id=$row['book_id'];

        if($row['status']=='pending'){
            // Hủy mượn
            mysqli_query($conn,"UPDATE borrow_records SET status='rejected' WHERE id=$id");
        } elseif($row['status']=='return_pending'){
            // Hủy trả => revert về borrowed
            mysqli_query($conn,"UPDATE borrow_records SET status='borrowed' WHERE id=$id");
        }
    }
    header("Location: approve_requests.php");
    exit;
}

// Lấy danh sách yêu cầu (mượn & trả)
$sql="
SELECT br.id,u.username,b.title,br.borrow_date,br.status 
FROM borrow_records br
JOIN users u ON br.user_id=u.id
JOIN books b ON br.book_id=b.id
WHERE br.status IN('pending','return_pending')
ORDER BY br.borrow_date ASC
";
$requests=mysqli_query($conn,$sql);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>📋 Duyệt yêu cầu</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container mt-5">
    <h2 class="text-center mb-4">📋 Duyệt yêu cầu mượn / trả sách</h2>
    <table class="table table-bordered">
        <thead class="table-primary">
            <tr>
                <th>Người dùng</th>
                <th>Tựa sách</th>
                <th>Ngày yêu cầu</th>
                <th>Loại yêu cầu</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php if(mysqli_num_rows($requests)>0): ?>
            <?php while($r=mysqli_fetch_assoc($requests)): ?>
            <tr>
                <td><?=htmlspecialchars($r['username'])?></td>
                <td><?=htmlspecialchars($r['title'])?></td>
                <td><?=$r['borrow_date']?></td>
                <td>
                    <?php if($r['status']=='pending'): ?>
                        <span class="badge bg-info">Mượn</span>
                    <?php elseif($r['status']=='return_pending'): ?>
                        <span class="badge bg-warning">Trả</span>
                    <?php endif; ?>
                </td>
                <td>
                    <a href="approve_requests.php?approve=<?=$r['id']?>" class="btn btn-success btn-sm">✅ Duyệt</a>
                    <a href="approve_requests.php?reject=<?=$r['id']?>" class="btn btn-danger btn-sm">❌ Từ chối</a>
                </td>
            </tr>
            <?php endwhile; ?>
            <?php else: ?>
            <tr><td colspan="5" class="text-center text-muted">Không có yêu cầu nào</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
