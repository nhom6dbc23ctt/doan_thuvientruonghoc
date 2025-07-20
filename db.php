<?php
$host = "sql209.infinityfree.com";   // DB Host trong InfinityFree
$user = "if0_39508447";                // DB User
$pass = "Tronglen93sh";          // DB Password
$db   = "if0_39508447_thuvientruonghocso";        // DB Name

$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
    die("❌ Kết nối thất bại: " . mysqli_connect_error());
}
?>