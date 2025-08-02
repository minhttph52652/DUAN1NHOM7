<?php

include_once '../../lib/session.php';
Session::checkSession('admin');
$role_id = Session::get('role_id');
if ($role_id == 1) {
    # code...
} else {
    header("Location:../../index.php");
}
include_once '../../models/order.php';

if (isset($_GET['orderId']) && is_numeric($_GET['orderId'])) {// lấy mã đơn hàng từ URL và kiểm tra xem nó có phải là số hay không
    $order = new order();// tao đối tượng order
    $result = $order->processedOrder($_GET['orderId']);// cập nhật trạng thái đơn hàng thành 'đã xử lý'
    if ($result) {
        echo '<script type="text/javascript">alert("Thành công!"); window.location.href = "productlist.php";</script>';
    } else {
        echo '<script type="text/javascript">alert("Thất bại!"); history.back();</script>';
    }
} else {
    echo '<script>alert("⚠️ Mã đơn hàng không hợp lệ!"); window.location.href = "orderlist.php";</script>';
}
?>
