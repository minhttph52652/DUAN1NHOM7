<?php
/**
 * FILE: edit_info.php
 * CHỨC NĂNG: Trang chỉnh sửa thông tin cá nhân của user
 * LUỒNG XỬ LÝ:
 * 1. Load model user và lấy thông tin hiện tại
 * 2. Xử lý form POST để cập nhật thông tin
 * 3. Gọi method update() để lưu thay đổi
 * 4. Chuyển hướng và hiển thị thông báo kết quả
 */

// Load model user và lấy thông tin hiện tại của user
include '../models/user.php';
$user = new user();
$userInfo = $user->get();

// Xử lý form POST khi user submit để cập nhật thông tin
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    // Gọi method update() để cập nhật thông tin cá nhân
    $result = $user->update($_POST);
    
    if ($result) {
        // Thành công: Hiển thị thông báo và chuyển hướng
        echo '<script type="text/javascript">alert("Cập nhật thông tin cá nhân thành công!"); history.back();</script>';
        header("Location:./info.php");
    } else {
        // Thất bại: Hiển thị thông báo lỗi và chuyển hướng
        echo '<script type="text/javascript">alert("Cập nhật thông tin cá nhân thất bại!"); history.back();</script>';
        header("Location:./info.php");
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
    <title>Cập nhật thông tin cá nhân</title>
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
            <li class="li-index"><a href="index.php" >Trang chủ</a></li>
            <li class="li-index"><a href="productList.php" >Sản phẩm</a></li>

            <li class="li-index"><a href="order.php" id="order">Đơn hàng</a></li>
                
            <?php
            // Hiển thị menu tùy theo trạng thái đăng nhập
            if (isset($_SESSION['user']) && $_SESSION['user']) { ?>
                <!-- Menu khi đã đăng nhập -->
                <li class="li-index"><a href="info.php" id="signin">Thông tin cá nhân</a></li>
                <li class="li-index"><a href="logout.php" id="signin">Đăng xuất</a></li>
            <?php } else { ?>
                <!-- Menu khi chưa đăng nhập -->
                <li class="li-index"><a href="register.php" id="signup">Đăng ký</a></li>
                <li class="li-index"><a href="login.php" id="signin">Đăng nhập</a></li>
            <?php } ?>
        </ul>
        
        <!-- Form tìm kiếm sản phẩm -->
        <form class="c-search" action="" method="get">
            <div class="header_search">
                <input type="text" class="search_input" name="search" placeholder="Nhập tên sản phẩm">
                <button type="submit"><i class="fas fa-search"></i></button>
            </div>
        </form>
        
        <!-- Icon giỏ hàng với số lượng sản phẩm -->
        <a class="cart" href="checkout.php">
                    <i class="fa fa-shopping-cart"></i>
                    <sup class="sumItem">
                        <?= ($totalQty['total']) ? $totalQty['total'] : "0" ?>
                    </sup>
        </a>
    </nav>
    
    <!-- Section banner slider hiển thị ảnh từ thư mục slider -->
    <section class="banner">
        <div class="fadein">
            <?php
            // Hiển thị ảnh từ thư mục slider
            // Đường dẫn thư mục
            $dir = "../images/slider/";

            $scan_dir = scandir($dir);
            foreach ($scan_dir as $img) :
                if (in_array($img, array('.', '..')))
                    continue;
            ?> 
                <img src="<?php echo $dir . $img ?>" alt="<?php echo $img ?>">
            <?php endforeach; ?>
        </div>
    </section>
    
    <!-- Header trang chỉnh sửa thông tin cá nhân -->
    <div class="featuredProducts">
        <h1>Chỉnh sửa thông tin cá nhân</b></h1>
    </div>
    
    <!-- Container chính chứa form chỉnh sửa thông tin -->
    <div class="container-single">
        
        <div class="login">
            <form action="edit_info.php" method="post" class="form-login">
                <label for="fullName">Họ tên</label>
                <input type="text" id="fullName" value="<?= $userInfo['fullname'] ?>" name="fullName" placeholder="Họ tên..." required>

                <label for="email">Email</label>
                <p class="error"><?= !empty($result) ? $result : '' ?></p>
                <input type="email" id="email" value="<?= $userInfo['email'] ?>" name="email" placeholder="Email..." required pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$">

                <label for="password">Mật khẩu</label>
                <input type="password" id="password" name="password" placeholder="Mật khẩu..." required>

                <label for="repassword">Nhập lại mật khẩu</label>
                <input type="password" id="repassword" name="repassword" required placeholder="Nhập lại mật khẩu..." oninput="check(this)">

                <label for="address">Địa chỉ</label>
                <textarea name="address" id="address" cols="30" rows="5" required><?= $userInfo['address'] ?></textarea>

                <label for="dob">Ngày sinh</label>
                <input type="date" value="<?= $userInfo['dob'] ?>" name="dob" id="dob" required>

                <div>
                    <input type="submit" value="Cập nhật" name="submit">
                    <button style="background:wheat"><a  href="./info.php">Hủy bỏ</a></button>
                </div>
            </form>
        </div>
    </div>
    </div>
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
        <p class="copyright">copy by IVYmoda.com 2024</p>
    </footer>
</body>
<script language='javascript' type='text/javascript'>
    function check(input) {
        if (input.value != document.getElementById('password').value) {
            input.setCustomValidity('Password Must be Matching.');
        } else {
            input.setCustomValidity('');
        }
    }
</script>

</html>