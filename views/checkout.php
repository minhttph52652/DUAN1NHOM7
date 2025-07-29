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
