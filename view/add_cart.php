<?php
/**
 * FILE: add_cart.php
 * CHỨC NĂNG: Xử lý thêm sản phẩm vào giỏ hàng
 * LUỒNG XỬ LÝ:
 * 1. Kiểm tra session đăng nhập của client
 * 2. Nhận ID sản phẩm từ URL parameter
 * 3. Gọi model cart để thêm sản phẩm
 * 4. Xử lý kết quả và hiển thị thông báo
 */

// Load session và kiểm tra đăng nhập client
include_once __DIR__ . '/../lib/session.php';
Session::checkSession('client');

// Load model giỏ hàng để xử lý logic thêm sản phẩm
include_once __DIR__ . '/../models/cart.php';

// Kiểm tra có ID sản phẩm được truyền qua URL không
if (isset($_GET['id'])) {
    // Khởi tạo đối tượng giỏ hàng
    $cart = new cart();
    
    // Gọi method add() để thêm sản phẩm vào giỏ hàng
    $result = $cart->add($_GET['id']);
    
    // Xử lý trường hợp hết hàng
    if ($result === 'out of stock') {
        echo '<script type="text/javascript">alert("Số lượng sản phẩm không đủ!"); history.back();</script>';
        return;
    }

    // Xử lý kết quả thêm sản phẩm
    if ($result) {
        // Thành công: Hiển thị thông báo và quay lại trang trước
        echo '<script type="text/javascript">alert("Thêm sản phẩm vào giỏ hàng thành công!"); history.back();</script>';
    } else {
        // Thất bại: Hiển thị thông báo lỗi và quay lại trang trước
        echo '<script type="text/javascript">alert("Thêm sản phẩm vào giỏ hàng thất bại!"); history.back();</script>';
    }
}
?>
