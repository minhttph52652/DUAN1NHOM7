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
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng nhập</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    
<nav>
    <label class="logo"><a href="index.php">IVY Moda</a></label>
    <ul id="dc_mega-menu-orange">
        <li class="li-index"><a href="index.php">Trang chủ</a></li>
        <li class="li-index"><a href="productList.php">Sản phẩm</a></li>
        <li class="li-index"><a href="order.php">Đơn hàng</a></li>
        <?php if (Session::get('user')) { ?>
            <li class="li-index"><a href="info.php">Thông tin cá nhân</a></li>
            <li class="li-index"><a href="logout.php">Đăng xuất</a></li>
        <?php } else { ?>
            <li class="li-index"><a href="register.php">Đăng ký</a></li>
            <li class="li-index"><a href="login.php" class="active">Đăng nhập</a></li>
        <?php } ?>
    </ul>
    <form method="POST" action="">
    <input type="text" name="fullName" placeholder="Họ tên" required>
    <input type="email" name="email" placeholder="Email" required>
    <!-- <input type="date" name="dob" placeholder="Ngày sinh" required> -->
    <input type="text" name="address" placeholder="Địa chỉ" required>
    <input type="password" name="password" placeholder="Mật khẩu" required>
    <button type="submit">Đăng ký</button>
</form>
    
</nav>



</form>
</body>
</html> 