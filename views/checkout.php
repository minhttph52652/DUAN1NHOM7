<?php
// Load file session và kiểm tra xem user có đăng nhập chưa (dành cho client)
include_once '../lib/session.php';
Session::checkSession('client');

// Load các model cần thiết
include_once '../models/cart.php';
include_once '../models/user.php';

// Tạo đối tượng cart để thao tác với giỏ hàng
$cart = new cart();
$list = $cart->get(); // Lấy danh sách sản phẩm trong giỏ hàng
$totalPrice = $cart->getTotalPriceByUserId(); // Lấy tổng tiền giỏ hàng
$totalQty = $cart->getTotalQtyByUserId(); // Lấy tổng số lượng sản phẩm trong giỏ

// Tạo đối tượng user để lấy thông tin người dùng
$user = new user();
$userInfo = $user->get(); // Lấy thông tin người dùng đang đăng nhập
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Thiết lập bảng mã ký tự là UTF-8 -->
    <meta charset="UTF-8">
    
    <!-- Tối ưu hiển thị cho trình duyệt Internet Explorer -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <!-- Cho phép giao diện responsive trên các thiết bị khác nhau -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Link tới file CSS chính của website -->
    <link rel="stylesheet" href="../css/style.css">

    <!-- Font Awesome: bộ icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://use.fontawesome.com/2145adbb48.js"></script>
    <script src="https://kit.fontawesome.com/a42aeb5b72.js" crossorigin="anonymous"></script>

    <!-- Tiêu đề trang -->
    <title>Checkout</title>

    <!-- Load jQuery từ CDN -->
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>

    <!-- Script để tạo hiệu ứng chuyển đổi hình ảnh (nếu có) -->
    <script>
        $(function () {
            $('.fadein img:gt(0)').hide(); // Ẩn tất cả ảnh trừ ảnh đầu tiên
            setInterval(function () {
                $('.fadein :first-child').fadeOut().next('img').fadeIn().end().appendTo('.fadein');
            }, 5000); // Lặp lại mỗi 5 giây
        });
    </script>
</head>

<nav>
    <!-- Logo trang, click để về trang chủ -->
    <label class="logo"><a href="../index.php">IVY Moda</a></label>

    <!-- Menu chính -->
    <ul id="dc_mega-menu-orange">
        <li class="li-index"><a href="../index.php">Trang chủ</a></li>
        <li class="li-index"><a href="productList.php">Sản phẩm</a></li>
        <li class="li-index"><a href="order.php" id="order">Đơn hàng</a></li>

        <!-- Hiển thị tùy chọn nếu đã đăng nhập -->
        <?php if (isset($_SESSION['user']) && $_SESSION['user']) { ?>
            <li class="li-index"><a href="info.php" id="signin">Thông tin cá nhân</a></li>
            <li class="li-index"><a href="logout.php" id="signin">Đăng xuất</a></li>
        <?php } else { ?>
            <!-- Nếu chưa đăng nhập -->
            <li class="li-index"><a href="register.php" id="signup">Đăng ký</a></li>
            <li class="li-index"><a href="login.php" id="signin">Đăng nhập</a></li>
        <?php } ?>
    </ul>

    <!-- Form tìm kiếm sản phẩm -->
    <form class="c-search" action="" method="get">
        <div class="header_search">
            <!-- Ô nhập từ khóa tìm kiếm -->
            <input type="text" class="search_input" name="search" placeholder="Nhập tên sản phẩm">
            <!-- Nút tìm kiếm -->
            <button type="submit"><i class="fas fa-search"></i></button>
        </div>
    </form>

    <!-- Biểu tượng giỏ hàng -->
    <a class="cart" href="checkout.php">
        <i class="fa fa-shopping-cart"></i>
        <!-- Số lượng sản phẩm hiện có trong giỏ -->
        <sup class="sumItem">
            <?= ($totalQty['total']) ? $totalQty['total'] : "0" ?>
        </sup>
    </a>
</nav>

<hr style="margin: 122px 177px -102px 177px;color: black;border: 1px solid;">

<!-- Tiêu đề phần giỏ hàng -->
<div class="featurecheckout">
    <h1>Giỏ hàng</h1>
</div>

<!-- Dòng kẻ dưới tiêu đề -->
<hr style="margin: 0px 177px 0 177px;color: black;border: 1px solid;">

        <!-- Phần chính hiển thị giỏ hàng -->
<div class="container-checkout">
    <?php if ($list) { ?>
    
        <!-- Bảng hiển thị sản phẩm trong giỏ -->
        <div class="tableContainer">
        <table class="order">
            <tr>
                <th>STT</th>
                <th>Tên sản phẩm</th>
                <th>Hình ảnh</th>
                <th>Đơn giá</th>
                <th>Số lượng</th>
                <th>Thao tác</th>
            </tr>
            <!-- Lặp qua từng sản phẩm trong giỏ -->
            <?php
            $count = 1;
            foreach ($list as $key => $value) { ?>
                <tr>
                    <td><?= $count++ ?></td>
                    <td><?= $value['productName'] ?></td>
                    <td><img class="image-cart" src="admin/uploads/<?= $value['productImage'] ?>"></td>
                    <td><?= number_format($value['productPrice'], 0, '', ',') ?>VND </td>
                    <td>
                        <!-- Input thay đổi số lượng -->
                        <input id="<?= $value['productId'] ?>" type="number" name="qty" class="qty" value="<?= $value['qty'] ?>" onchange="update(this)" min="1">
                    </td>
                    <td>
                        <!-- Nút xóa sản phẩm -->
                        <a style="color: black;" href="delete_cart.php?id=<?= $value['id'] ?>"><i class="fa fa-trash" aria-hidden="true"></i></a>
                    </td>
                </tr>
            <?php } ?>
        </table>
        </div>
        
                    <!-- Thông tin đơn hàng bên phải -->
        <div class="orderinfo">
            <div class="buy">
                <h3>Thông tin đơn đặt hàng</h3>
                <!-- Tên người đặt hàng -->
                <div>Người đặt hàng: <b><?= $userInfo['fullname'] ?></b></div>
                <!-- Tổng số lượng sản phẩm -->
                <div>Số lượng: <b id="qtycart"><?= $totalQty['total'] ?></b></div>
                <!-- Tổng giá trị đơn hàng -->
                <div>Tổng tiền: <b id="totalcart"><?= number_format($totalPrice['total'], 0, '', ',') ?>VND</b></div>
                <!-- Địa chỉ nhận hàng -->
                <div>Địa chỉ nhận hàng: <b><?= $userInfo['address'] ?></b></div>
                
                <!-- Nút tiến hành đặt hàng -->
                <div class="buy-btn">
                    <a href="check_cart.php?status=<?= $userInfo['status'] ?>&userId=<?= $userInfo['id'] ?>">Tiến hành đặt hàng</a>
                </div>
            </div>
        </div>
    <?php } else { ?>
        <!-- Nếu giỏ rỗng thì thông báo -->
        <h3>Giỏ hàng hiện đang rỗng</h3>
    <?php } ?>
</div>