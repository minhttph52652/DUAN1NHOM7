<?php
  include_once '/../lib/session.php';
    include_once '/../models/order.php';
    include_once '/../models/user.php';
    Session::checkSession('admin');
    $role_id = Session::get('role_id');
    if ($role_id == 1){
          # code thêm vào đây nếu cần
    } else{
         header("location:../../index.php");
    }
    if(isset($_GET['orderId'])){
         $user = new user();
         $id = $user->getUserByOrder($_GET['orderId']);// Lấy id người dùng từ đơn hàng
         $result1 = $user->updateStatus($id['id']);// cập nhật trạng thái người dùng
         $order = new order();
            $result = $order->deleteOrder($_GET['orderId']);// Xoá đơn hàng theo orderId
             if($result && $result1){
                echo '<script type="text/javascript">alert("Xoá đơn hàng thành công!");</script>';
             } else{
                echo '<script type="text/javascript">alert("Xoá đơn hàng thất bại!");</script>';
             }
    }