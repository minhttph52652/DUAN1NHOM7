<?php
// Load session và kiểm tra đăng nhập client
include_once __DIR__ . '/../lib/session.php';
Session::checkSession('client');

// Load model giỏ hàng
include_once __DIR__ . '/../models/cart.php';

if (isset($_GET['id'])) {
    $cart = new cart();
    $result = $cart->add($_GET['id']);
}
if ($result === 'out of stock') {
        echo '<script type="text/javascript">alert("Số lượng sản phẩm không đủ!"); history.back();</script>';
        return;
    }

    if ($result) {
        echo '<script type="text/javascript">alert("Thêm sản phẩm vào giỏ hàng thành công!"); history.back();</script>';
    } else {
        echo '<script type="text/javascript">alert("Thêm sản phẩm vào giỏ hàng thất bại!"); history.back();</script>';
}
?>