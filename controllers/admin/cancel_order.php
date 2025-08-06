<?php
include_once __DIR__ . '/../../lib/session.php';
include_once __DIR__ . '/../../models/order.php';
 Session :: checkSession('admin');
 $role_id = Session :: get('role_id');
 if($role_id == 1){
     #code thêm vào đây nếu cần kiểm tra quyền admin
 } else{
      header("location: ../../index.php");
 }
        if( isset($_GET['orderId'])){
              $order = new order();
              $result = $order->cancelOrder($_GET['orderId']);
              if($result){
        echo '<script type="text/javascript">alert("Thành công!"); window.location.href = "orderlist.php";</script>';
        } else{
                echo '<script type="text/javascript">alert("Thất bại!"); history.back();</script>';
        
 }
 }