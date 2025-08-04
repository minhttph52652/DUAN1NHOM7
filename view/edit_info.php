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