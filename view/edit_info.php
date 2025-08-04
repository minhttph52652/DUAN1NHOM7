<?php
// Gồm model user để lấy thông tin người dùng
include '../models/user.php';

// Khởi tạo đối tượng user
$user = new user();

// Lấy thông tin cá nhân người dùng
$userInfo = $user->get();

// Kiểm tra khi người dùng submit form POST
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    // Gọi hàm update trong model user để cập nhật thông tin
    $result = $user->update($_POST);
    if ($result) {
        // Thông báo thành công
        echo '<script type="text/javascript">alert("Cập nhật thông tin cá nhân thành công!"); history.back();</script>';
        header("Location:./info.php");
    } else {
        // Thông báo thất bại
        echo '<script type="text/javascript">alert("Cập nhật thông tin cá nhân thất bại!"); history.back();</script>';
        header("Location:./info.php");
    }
}
?>

<?php
// Gồm các file session và models
include_once '../lib/session.php';
include_once '../models/product.php';
include_once '../models/cart.php';

// Tạo đối tượng cart và lấy tổng số sản phẩm
$cart = new cart();
$totalQty = $cart->getTotalQtyByUserId();
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
    <title>Cập nhật thông tin cá nhân</title>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
    <script>
        // Tự động chuyển ảnh trong banner
        $(function() {
            $('.fadein img:gt(0)').hide();
            setInterval(function() {
                $('.fadein :first-child').fadeOut().next('img').fadeIn().end().appendTo('.fadein');
            }, 5000);
        });
    </script>
</head>

<body>
    <!-- Thanh menu -->

    <nav>
        <label class="logo"><a href="index.php">IVY Moda</a></label>
        <ul id="dc_mega-menu-orange">
            <li class="li-index"><a href="index.php">Trang chủ</a></li>
            <li class="li-index"><a href="productList.php">Sản phẩm</a></li>
            <li class="li-index"><a href="order.php" id="order">Đơn hàng</a></li>
            <?php if (isset($_SESSION['user']) && $_SESSION['user']) { ?>
                <li class="li-index"><a href="info.php" id="signin">Thông tin cá nhân</a></li>
                <li class="li-index"><a href="logout.php" id="signin">Đăng xuất</a></li>
            <?php } else { ?>
                <li class="li-index"><a href="register.php" id="signup">Đăng ký</a></li>
                <li class="li-index"><a href="login.php" id="signin">Đăng nhập</a></li>
            <?php } ?>
        </ul>

         <!-- Tìm kiếm -->
        <form class="c-search" action="" method="get">
            <div class="header_search">
                <input type="text" class="search_input" name="search" placeholder="Nhập tên sản phẩm">
                <button type="submit"><i class="fas fa-search"></i></button>
            </div>
        </form>
        
    </nav>    


</body>