<?php
// Nạp file session để sử dụng phiên đăng nhập
include_once '../lib/session.php';

// Kiểm tra xem người dùng đã đăng nhập với quyền "client" chưa
Session::checkSession('client');

// Nạp model giỏ hàng để sử dụng các hàm liên quan đến giỏ hàng
include_once '../models/cart.php';

if (isset($_GET['id'])) {
    // Tạo đối tượng cart (giỏ hàng)
    $cart = new cart();

    // Gọi hàm delete để xóa sản phẩm có id tương ứng khỏi giỏ hàng
    $result = $cart->delete($_GET['id']);

    // Nếu xóa thành công thì hiển thị thông báo và quay về trang trước
    if ($result) {
        echo '<script type="text/javascript">alert("Xóa sản phẩm khỏi giỏ hàng thành công!"); history.back();</script>';
    } 
    // Nếu xóa thất bại thì hiển thị lỗi và quay về trang trước
    else {
        echo '<script type="text/javascript">alert("Xóa sản phẩm khỏi giỏ hàng thất bại!"); history.back();</script>';
    }
}