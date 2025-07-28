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