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

    // Nếu thành công, hiển thị thông báo và quay lại trang trước
    if ($result) {
        echo '<script type="text/javascript">alert("Thành công!"); history.back();</script>';
    } 
    // Nếu thất bại, hiển thị thông báo lỗi và quay lại trang trước
    else {
        echo '<script type="text/javascript">alert("Thất bại!"); history.back();</script>';
    }

}