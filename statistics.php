<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include 'db.php';
include 'header.php';

// 🔐 Phân quyền: chỉ admin hoặc librarian được truy cập
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['admin', 'librarian'])) {
    echo "<div style='padding:30px; text-align:center; color:red; font-weight:bold; font-size:18px'>
            🚫 Bạn không có quyền truy cập chức năng thống kê!
          </div>";
    include 'footer.php';
    exit();
}

// Thiết lập UTF-8
mysqli_set_charset($conn, "utf8mb4");

// Lấy danh sách người dùng
$users = [];
$userQuery = mysqli_query($conn, "SELECT id, username AS name FROM users");
while ($u = mysqli_fetch_assoc($userQuery)) {
    $users[$u['id']] = $u['name'];
}

// Xử lý bộ lọc
$conditions = "";
if (!empty($_GET['from_date'])) {
    $from = mysqli_real_escape_string($conn, $_GET['from_date']);
    $conditions .= " AND br.borrow_date >= '$from'";
}
if (!empty($_GET['to_date'])) {
    $to = mysqli_real_escape_string($conn, $_GET['to_date']);
    $conditions .= " AND br.borrow_date <= '$to'";
}
if (!empty($_GET['user_id'])) {
    $user_id = (int)$_GET['user_id'];
    $conditions .= " AND br.user_id = $user_id";
}

// Truy vấn Top sách
$query = "
    SELECT b.title, COUNT(*) AS total
    FROM borrow_records br
    JOIN books b ON br.book_id = b.id
    WHERE 1 $conditions
    GROUP BY br.book_id
    ORDER BY total DESC
    LIMIT 10
";
$result = mysqli_query($conn, $query);

// Chuẩn bị dữ liệu biểu đồ
$labels = [];
$data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $title = trim($row['title']);
    if ($title !== '' && $row['total'] > 0) {
        $labels[] = htmlspecialchars(mb_substr($title, 0, 50), ENT_QUOTES, 'UTF-8');
        $data[] = (int)$row['total'];
    }
}
?>

<!-- Bộ lọc -->
<div class="container container-box mt-4">
    <h5 class="mb-3">📅 Lọc thống kê mượn sách</h5>
    <form method="GET" action="statistics.php" class="row g-3">
        <div class="col-md-4">
            <label class="form-label">Từ ngày:</label>
            <input type="date" name="from_date" class="form-control" value="<?= $_GET['from_date'] ?? '' ?>">
        </div>
        <div class="col-md-4">
            <label class="form-label">Đến ngày:</label>
            <input type="date" name="to_date" class="form-control" value="<?= $_GET['to_date'] ?? '' ?>">
        </div>
        <div class="col-md-4">
            <label class="form-label">Người dùng:</label>
            <select name="user_id" class="form-select">
                <option value="">-- Tất cả --</option>
                <?php foreach ($users as $id => $name): ?>
                    <option value="<?= $id ?>" <?= (isset($_GET['user_id']) && $_GET['user_id'] == $id) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($name) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-12 d-flex justify-content-end">
            <button type="submit" class="btn btn-primary">🔍 Lọc dữ liệu</button>
        </div>
    </form>
</div>

<!-- Biểu đồ -->
<?php if (empty($labels)): ?>
    <div class="container container-box text-center mt-5 mb-5 text-muted">
        <h5>⚠️ Không có dữ liệu thống kê phù hợp với bộ lọc.</h5>
    </div>
<?php else: ?>
    <div class="container container-box mt-5">
        <h2 class="text-center mb-4">📊 Thống kê Mượn Sách – Top 10</h2>
        <canvas id="bookChart" width="400" height="300"></canvas>
    </div>

    <!-- Debug -->
    <script>
        console.log("Labels: ", <?= json_encode($labels) ?>);
        console.log("Data: ", <?= json_encode($data) ?>);
    </script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('bookChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?= json_encode($labels) ?>,
                datasets: [{
                    label: 'Số lượt mượn',
                    data: <?= json_encode($data) ?>,
                    backgroundColor: 'rgba(0, 123, 255, 0.6)',
                    borderColor: 'rgba(0, 123, 255, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                indexAxis: 'y',
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: { precision: 0 }
                    },
                    y: {
                        ticks: { font: { size: 12 } }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                }
            }
        });
    </script>
<?php endif; ?>

<?php include 'footer.php'; ?>
