<?php
/**
 * FILE: delete_cart.php
 * CHỨC NĂNG: Xử lý xóa sản phẩm khỏi giỏ hàng
 * LUỒNG XỬ LÝ:
 * 1. Kiểm tra session đăng nhập của client
 * 2. Nhận ID sản phẩm trong giỏ hàng từ URL parameter
 * 3. Gọi model cart để xóa sản phẩm
 * 4. Hiển thị thông báo kết quả
 */

// Load session và kiểm tra đăng nhập client
include_once '../lib/session.php';
Session::checkSession('client');

// Load model cart để xử lý logic xóa sản phẩm
include_once '../models/cart.php';

// Kiểm tra có ID sản phẩm trong giỏ hàng được truyền qua URL không
if (isset($_GET['id'])) {
    // Khởi tạo đối tượng giỏ hàng
    $cart = new cart();
    
    // Gọi method delete() để xóa sản phẩm khỏi giỏ hàng
    $result = $cart->delete($_GET['id']);
    
    // Xử lý kết quả xóa sản phẩm
    if ($result) {
        // Thành công: Hiển thị thông báo và quay lại trang trước
        echo '<script type="text/javascript">alert("Xóa sản phẩm khỏi giỏ hàng thành công!"); history.back();</script>';
    } else {
        // Thất bại: Hiển thị thông báo lỗi và quay lại trang trước
        echo '<script type="text/javascript">alert("Xóa sản phẩm khỏi giỏ hàng thất bại!"); history.back();</script>';
    }
}
?>
