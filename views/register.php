<?php
include '../models/user.php'; // hoặc classes/user.php nếu bạn dùng tên đó

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = new user();
    $result = $user->insert($_POST);

    if ($result === true) {
        echo "<script>alert('Đăng ký thành công!'); window.location.href = 'login.php';</script>";
        exit();
    } else {
        echo "<script>alert('$result');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ĐĂNG KÍ</title>
</head>
<body>
    <form method="POST" action="">
    <input type="text" name="fullName" placeholder="Họ tên" required>
    <input type="email" name="email" placeholder="Email" required>
    <!-- <input type="date" name="dob" placeholder="Ngày sinh" required> -->
    <input type="text" name="address" placeholder="Địa chỉ" required>
    <input type="password" name="password" placeholder="Mật khẩu" required>
    <button type="submit">Đăng ký</button>
</form>
</body>
</html> 