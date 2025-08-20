<?php
/**
 * FILE: complete_order.php
 * CHỨC NĂNG: Xử lý hoàn thành đơn hàng (xác nhận đã nhận hàng)
 * LUỒNG XỬ LÝ:
 * 1. Kiểm tra session đăng nhập của client
 * 2. Nhận orderId từ URL parameter
 * 3. Gọi model order để cập nhật trạng thái đơn hàng
 * 4. Hiển thị thông báo kết quả
 */

// Load session và kiểm tra đăng nhập client
include_once '../lib/session.php';
Session::checkSession('client');

// Load model order để xử lý logic cập nhật đơn hàng
include_once '../models/order.php';

// Kiểm tra có orderId được truyền qua URL không
if (isset($_GET['orderId'])) {
    // Khởi tạo đối tượng order
    $order = new order();
    
    // Gọi method completeOrder() để cập nhật trạng thái đơn hàng thành "hoàn thành"
    $result = $order->completeOrder($_GET['orderId']);
    
    // Xử lý kết quả cập nhật
    if ($result) {
        // Thành công: Hiển thị thông báo và quay lại trang trước
        echo '<script type="text/javascript">alert("Thành công!"); history.back();</script>';
    } else {
        // Thất bại: Hiển thị thông báo lỗi và quay lại trang trước
        echo '<script type="text/javascript">alert("Thất bại!"); history.back();</script>';
    }
} 
?> 