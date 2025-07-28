<?php
// Nạp session để sử dụng kiểm tra đăng nhập
include_once '../lib/session.php';
Session::checkSession('client'); // Kiểm tra xem người dùng đã đăng nhập hay chưa, nếu chưa thì chuyển hướng

// Nạp các model cần thiết
include '../models/order.php';
include_once '../models/cart.php';

// Tạo đối tượng cart và lấy tổng số lượng sản phẩm trong giỏ hàng của người dùng
$cart = new cart();
$totalQty = $cart->getTotalQtyByUserId();

// Tạo đối tượng order và lấy danh sách đơn hàng của người dùng
$order = new order();
$result = $order->getOrderByUser();
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
    <!-- Cho phép tương thích với IE -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Đảm bảo trang hiển thị tốt trên mọi thiết bị -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Link CSS riêng của bạn -->
    <link rel="stylesheet" href="../css/style.css">

    <!-- Các thư viện icon từ Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://use.fontawesome.com/2145adbb48.js"></script>
    <script src="https://kit.fontawesome.com/a42aeb5b72.js" crossorigin="anonymous"></script>

    <!-- Tiêu đề trang -->
    <title>Checkout</title>

    <!-- Thư viện jQuery để dùng cho slide ảnh -->
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>

</head>
<body>
    
</body>
</html>