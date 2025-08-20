<?php
/**
 * FILE: delivery_address.php
 * CHỨC NĂNG: Trang nhập địa chỉ giao hàng cho đơn hàng
 * LUỒNG XỬ LÝ:
 * 1. Load session và kiểm tra đăng nhập client
 * 2. Lấy thông tin giỏ hàng và đơn hàng của user
 * 3. Hiển thị form nhập thông tin giao hàng
 * 4. Xử lý validation số điện thoại
 */

// Load session và kiểm tra đăng nhập client
include_once '../lib/session.php';
Session::checkSession('client');

// Load các model cần thiết
include '../models/order.php';
include_once '../models/cart.php';

// Khởi tạo đối tượng giỏ hàng và lấy tổng số lượng
$cart = new cart();
$totalQty = $cart->getTotalQtyByUserId();

// Khởi tạo đối tượng order và lấy thông tin đơn hàng của user
$order = new order();
$result = $order->getOrderByUser();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://use.fontawesome.com/2145adbb48.js"></script>
    <script src="https://kit.fontawesome.com/a42aeb5b72.js" crossorigin="anonymous"></script>
    <title>Checkout</title>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
    
    <!-- Script xử lý slider banner tự động chuyển ảnh -->
    <script>
        $(function() {
            $('.fadein img:gt(0)').hide();
            setInterval(function() {
                $('.fadein :first-child').fadeOut().next('img').fadeIn().end().appendTo('.fadein');
            }, 5000);
        });
    </script>
</head>

<body>
<!-- Navigation menu chính -->
<nav>
        <label class="logo"><a href="index.php">IVY Moda</a></label>
        <ul>
            <li><a href="index.php">Trang chủ</a></li>
            <li><a href="productList.php">Sản phẩm</a></li>

            <li><a href="order.php" id="order">Đơn hàng</a></li>
            <li>
                <!-- Icon giỏ hàng với số lượng sản phẩm -->
                <a href="checkout.php">
                    Giỏ hàng
                    <i class="fa fa-shopping-bag"></i>
                    <sup class="sumItem">
                        <?= ($totalQty['total']) ? $totalQty['total'] : "0" ?>
                    </sup>
                </a>
            </li>
            <?php
            // Hiển thị menu tùy theo trạng thái đăng nhập
            if (isset($_SESSION['user']) && $_SESSION['user']) { ?>
                <!-- Menu khi đã đăng nhập -->
                <li><a href="info.php" id="signin">Thông tin cá nhân</a></li>
                <li><a href="logout.php" id="signin">Đăng xuất</a></li>
            <?php } else { ?>
                <!-- Menu khi chưa đăng nhập -->
                <li><a href="register.php" id="signup">Đăng ký</a></li>
                <li><a href="login.php" id="signin">Đăng nhập</a></li>
            <?php } ?>
        </ul>
    </nav>
    
    <!-- Container chính chứa form địa chỉ giao hàng -->
    <div class="deliveryContainer">
    <div class="nhanHang">
        <h1>Địa chỉ nhận hàng</h1>
    </div>
    <div class="container-single">
    <div class="infor_man">
            <!-- Form nhập thông tin giao hàng -->
            <form action="add_order.php" method="post" class="form-login">
                <label for="fullName">Họ tên người nhận</label>
                <input type="text" id="fullName" name="fullName" placeholder="Họ tên..." required>

                <label for="numberPhone">Số điện thoại</label>
                <input type="tel" id="phoneNumber" name="phoneNumber" placeholder="Số điện thoại..." required oninput="validatePhoneNumber(this)">
                
                <label for="address">Địa chỉ</label>
                <textarea name="address" id="address" cols="30" rows="5" required></textarea>

                <input type="submit" value="Gửi" name="submit">
            </form>
    </div>
    </div>
    </div>

    
    <!-- Footer -->
    <footer>
        <div class="social">
            <a href="#"><i class="fa fa-facebook" aria-hidden="true"></i></a>
            <a href="#"><i class="fa fa-twitter" aria-hidden="true"></i></a>
            <a href="#"><i class="fa fa-instagram" aria-hidden="true"></i></a>
        </div>
        <ul class="list">
            <li>
                <a href="./">Trang Chủ</a>
            </li>
            <li>
                <a href="productList.php">Sản Phẩm</a>
            </li>
        </ul>
        <p class="copyright">copy by IVYmoda.com 2025</p>
    </footer>
    </body>

    <!-- Script validation số điện thoại -->
    <script>
    /**
     * Hàm kiểm tra định dạng số điện thoại
     * Yêu cầu: 10 chữ số
     */
    function validatePhoneNumber(input) {
        var phoneNumber = input.value;
        var regex = /^[0-9]{10}$/;

        if (!regex.test(phoneNumber)) {
            input.setCustomValidity('Số điện thoại không đúng định dạng!');
        } else {
            input.setCustomValidity('');
        }
    }
    </script>

</html> 