<?php
/**
 * FILE: forgot_password.php
 * CHỨC NĂNG: Trang lấy lại mật khẩu cho user
 * LUỒNG XỬ LÝ:
 * 1. Load model user để xử lý logic lấy lại mật khẩu
 * 2. Xử lý form POST với email
 * 3. Gọi method getPassword() để gửi mật khẩu mới
 * 4. Chuyển hướng đến trang đăng nhập nếu thành công
 */

// Load model user để xử lý logic lấy lại mật khẩu
include '../models/user.php';
$user = new user();

// Xử lý form POST khi user submit email
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    
    // Gọi method getPassword() để gửi mật khẩu mới qua email
    $pass_check = $user->getPassword($email);
    
    if ($pass_check === true) {
        // Thành công: Chuyển hướng đến trang đăng nhập
        header("Location:./login.php");
    }
}
?>

<?php
// Load các model cần thiết cho navigation và giỏ hàng
include_once '../lib/session.php';
include_once '../models/product.php';
include_once '../models/cart.php';

// Khởi tạo đối tượng giỏ hàng và lấy tổng số lượng
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
    <title>Đăng nhập</title>
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
        <ul id="dc_mega-menu-orange">
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
                <li><a href="login.php" id="signin" class="active">Đăng nhập</a></li>
            <?php } ?>
        </ul>
    </nav>
      
    <!-- Header trang lấy lại mật khẩu -->
    <div class="featuredProducts">
        <h1>Lấy lại mật khẩu</h1>
    </div>
    
    <!-- Container chính chứa form lấy lại mật khẩu -->
    <div class="container-single">
        <div class="login">
            <!-- Form nhập email để lấy lại mật khẩu -->
            <form action="forgot_password.php" method="post" class="form-login">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Email..." required>

                <!-- Hiển thị thông báo lỗi nếu có -->
                <p style="color: red;"><?= !empty($pass_check) ? $pass_check : '' ?></p>

                <input type="submit" value="Gửi">
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

</html>