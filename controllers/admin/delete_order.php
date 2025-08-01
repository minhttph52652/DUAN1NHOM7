<?php 
   include_once '/../../lib/session.php';
     Session::checkSession('admin');
     $role_id = Session::get('role_id');
        if ($role_id == 1) {
            # code...
        } else {
            header("Location:../../index.php");
        }
        include_once '/../../models/order.php';
        include_once '/../../models/user.php';

if (isset($_GET['orderId'])) {// ktra có truyền orderId qua URL
    $user = new user();
    $id = $user->getUserByOrder($_GET['orderId']);// lấy id người dùng từ đơn hàng
    $result1 = $user->updateStatus($id['id']);// cập nhật 

    $order = new order();
    $result = $order->deleteOrder($_GET['orderId']);// xóa đơn hàng theo orderId
    
    if ($result && $result1) {
        echo '<script type="text/javascript">alert("Thành công!"); history.back();</script>';
    } else {
        echo '<script type="text/javascript">alert("Thất bại!"); history.back();</script>';
    }
}
?>