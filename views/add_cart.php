<?php
// Load session và kiểm tra đăng nhập client
include_once __DIR__ . '/../lib/session.php';
Session::checkSession('client');

// Load model giß╗Å h├áng
include_once __DIR__ . '/../models/cart.php';

// Kiểm tra xem có truyền id sản phẩm trên URL không (vd: add_cart.php?id=5)
if (isset($_GET['id'])) {
    $cart = new cart(); // Tạo một đối tượng từ class cart
    $result = $cart->add($_GET['id']); // Gọi hàm add() để thêm sản phẩm có id vào giỏ hàng

    // Nếu kết quả trả về là 'out of stock' thì thông báo hết hàng
    if ($result === 'out of stock') {
        echo '<script type="text/javascript">alert("Số lượng sản phẩm không đủ!"); history.back();</script>';
        return; // Dừng không chạy tiếp
    }

    // Nếu thêm thành công (true), thông báo thành công
    if ($result) {
        echo '<script type="text/javascript">alert("Thêm sản phẩm vào giỏ hàng thành công!"); history.back();</script>';
    } else {
        // Ngược lại nếu thêm thất bại (false), thông báo lỗi
        echo '<script type="text/javascript">alert("Thêm sản phẩm vào giỏ hàng thất bại!"); history.back();</script>';
    }
}
?>
