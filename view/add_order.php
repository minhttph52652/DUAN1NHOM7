<?php
/**
 * FILE: add_order.php
 * CHỨC NĂNG: Xử lý tạo đơn hàng mới từ form đặt hàng
 * LUỒNG XỬ LÝ:
 * 1. Kiểm tra session đăng nhập của client
 * 2. Kiểm tra method POST từ form
 * 3. Gọi model order để tạo đơn hàng
 * 4. Xử lý kết quả và chuyển hướng
 */

// Load session và kiểm tra đăng nhập client
include_once __DIR__ . '/../lib/session.php';
Session::checkSession('client');

// Load model order để xử lý logic tạo đơn hàng
include_once __DIR__ . '/../models/order.php';

// Kiểm tra form được submit bằng method POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Khởi tạo đối tượng order
    $order = new order();
    
    // Gọi method add() để tạo đơn hàng với dữ liệu từ form
    $result = $order->add($_POST);

    // Xử lý kết quả tạo đơn hàng
    if ($result) {
        // Thành công: Hiển thị thông báo và chuyển đến trang đơn hàng
        echo '<script type="text/javascript">alert("Đặt hàng thành công!"); window.location.href = "order.php";</script>';
    } else {
        // Thất bại: Hiển thị thông báo lỗi và quay lại trang trước
        echo '<script type="text/javascript">alert("Đặt hàng thất bại!"); history.back();</script>';
    }
}
?>
