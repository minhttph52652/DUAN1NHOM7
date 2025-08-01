<?php
// Nạp file session để sử dụng phiên đăng nhập
include_once '../lib/session.php';

// Kiểm tra xem người dùng đã đăng nhập với quyền "client" hay chưa
Session::checkSession('client');

// Nạp model xử lý đơn hàng
include_once '../models/order.php';

// Kiểm tra xem trên URL có truyền tham số orderId (ID đơn hàng) hay không
if (isset($_GET['orderId'])) {

    // Tạo đối tượng order
    $order = new order();

    // Gọi hàm completeOrder để đánh dấu đơn hàng là đã hoàn tất
    $result = $order->completeOrder($_GET['orderId']);



}