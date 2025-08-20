<?php
/**
 * FILE: login.php
 * CHỨC NĂNG: Trang đăng nhập tài khoản cho user
 * LUỒNG XỬ LÝ:
 * 1. Khởi tạo session và load các model cần thiết
 * 2. Xử lý form POST với email và password
 * 3. Gọi method login() để xác thực thông tin đăng nhập
 * 4. Chuyển hướng về trang chủ nếu thành công hoặc hiển thị lỗi
 */

// Load session và khởi tạo
include_once '../lib/session.php';
Session::init();

// Load các model cần thiết
include_once '../models/user.php';
include_once '../models/cart.php';

// Khởi tạo các đối tượng cần thiết
$user = new user();
$cart = new cart();
$totalQty = $cart->getTotalQtyByUserId();

$error = ''; // Biến báo lỗi

// Xử lý form POST khi user submit thông tin đăng nhập
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Gọi method login() để xác thực thông tin đăng nhập
    $loginResult = $user->login($email, $password);

    if ($loginResult === true) {
        // Thành công: Chuyển hướng về trang chủ
        header("Location: ../index.php");
        exit;
    } else {
        // Thất bại: Lưu thông báo lỗi
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
<!-- Navigation menu chính -->
<nav>
    <label class="logo"><a href="index.php">IVY Moda</a></label>
    <ul id="dc_mega-menu-orange">
        <li class="li-index"><a href="index.php">Trang chủ</a></li>
        <li class="li-index"><a href="productList.php">Sản phẩm</a></li>
        <li class="li-index"><a href="order.php">Đơn hàng</a></li>
        
        <?php if (Session::get('user')) { ?>
            <!-- Menu khi đã đăng nhập -->
            <li class="li-index"><a href="info.php">Thông tin cá nhân</a></li>
            <li class="li-index"><a href="logout.php">Đăng xuất</a></li>
        <?php } else { ?>
            <!-- Menu khi chưa đăng nhập -->
            <li class="li-index"><a href="register.php">Đăng ký</a></li>
            <li class="li-index"><a href="login.php" class="active">Đăng nhập</a></li>
        <?php } ?>
    </ul>
    
    <!-- Icon giỏ hàng với số lượng sản phẩm -->
    <a class="cart" href="checkout.php">
        <i class="fa fa-shopping-cart"></i>
        <sup class="sumItem"><?= ($totalQty['total']) ? $totalQty['total'] : "0" ?></sup>
    </a>
</nav>

<!-- Container chính chứa form đăng nhập -->
<div class="dangNhapContainer">
    <div class="dangNhap"><h1>Đăng nhập</h1></div>
    <div class="container-single">
        <div class="login">
            <!-- Form đăng nhập -->
            <form action="login.php" method="post" class="form-login">
                <label for="email">Email</label>
                <input type="email" name="email" placeholder="Email..." required>

                <label for="password">Mật khẩu</label>
                <input type="password" name="password" placeholder="Mật khẩu..." required>

                <!-- Hiển thị thông báo lỗi nếu có -->
                <?php if (!empty($error)): ?>
                    <p style="color: red;"><?= $error ?></p>
                <?php endif; ?>

                <!-- Link quên mật khẩu -->
                <a href="forgot_password.php">Quên mật khẩu?</a>   
                <input type="submit" value="Đăng nhập" class="dangNhapbtn">
            </form>
        </div>
    </div>
</div>

<!-- Footer -->
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
