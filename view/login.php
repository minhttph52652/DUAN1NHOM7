<?php
include_once '../lib/session.php';
Session::init();

include_once '../models/user.php';
include_once '../models/cart.php';

$user = new user();
$cart = new cart();
$totalQty = $cart->getTotalQtyByUserId();

$error = ''; // Biến báo lỗi

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $loginResult = $user->login($email, $password);

    if ($loginResult === true) {
        // Redirect về trang chủ nếu thành công
        header("Location: ../index.php");
        exit;
    } else {
        $error = $loginResult;
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
    <a class="cart" href="checkout.php">
        <i class="fa fa-shopping-cart"></i>
        <sup class="sumItem"><?= ($totalQty['total']) ? $totalQty['total'] : "0" ?></sup>
    </a>
</nav>

<div class="dangNhapContainer">
    <div class="dangNhap"><h1>Đăng nhập</h1></div>
    <div class="container-single">
        <div class="login">
            <form action="login.php" method="post" class="form-login">
                <label for="email">Email</label>
                <input type="email" name="email" placeholder="Email..." required>

                <label for="password">Mật khẩu</label>
                <input type="password" name="password" placeholder="Mật khẩu..." required>

                <?php if (!empty($error)): ?>
                    <p style="color: red;"><?= $error ?></p>
                <?php endif; ?>

                <a href="forgot_password.php">Quên mật khẩu?</a>   
                <input type="submit" value="Đăng nhập" class="dangNhapbtn">
            </form>
        </div>
    </div>
</div>

<footer>
    <div class="social">
        <a href="#"><i class="fa fa-facebook"></i></a>
        <a href="#"><i class="fa fa-twitter"></i></a>
        <a href="#"><i class="fa fa-instagram"></i></a>
    </div>
    <ul class="list">
        <li><a href="./">Trang Chủ</a></li>
        <li><a href="productList.php">Sản Phẩm</a></li>
    </ul>
    <p class="copyright">copy by IVYmoda.com 2025</p>
</footer>
</body>
</html>
