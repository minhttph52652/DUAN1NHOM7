<?php
   include_once '../../lib/session.php';
   Session::checkSession('admin');
    $role_id = Session::get('role_id');
     if($role_id == 1){
        #code thêm vào đây
     } else{
         header("Location:../../index.php");
     }
       include_once '../../models/order.php';
       include_once '../../models/product.php';
       include_once '../../models/user.php';
       include_once '../../models/orderDetails.php';
       include_once '../../models/statistical.php';
        if(isset($_GET['orderId'])){
            $orderDetails = new orderDetails();
            $totalPrice = $orderDetails->getTotalPriceByOrderId($_GET['orderId']);// tính tổng tiền và số lượng
            $totaQty = $orderDetails->getTotalQtyByOrderId($_GET['orderId']);// cập nhật  trạng thái đơn hàng -> đã giao

            $order = new order();
            $result = $order->deliveredOrder($_GET['orderId']);// cập nhật sản phẩm đã bán
            $result= $order -> get($_GET['orderId']);// lấy thông tin đơn hàng

            $product = new product();
             foreach( $listProduct as $key => $value){
                $check = $product ->updateSold ($value['product_id'], $value['qty']);// cập nhật số lượng sản phẩm đã bán
             }
               
             $statistical = new statistical(); // thêm dữ liệu vào bảng thống kê
             $addst = $statistical -> getStatistical($_GET['orderId'], $totalPrice['total'], $totaQty['total']);

             $user = new user();
               $id = $user-> getUserByOrder($_GET['orderId']);// lấy thông tin người dùng theo đơn hàng
                $usId = $id['id'];
                $mess = $order -> messComplete($usId);// gửi thông báo cho người dùng về việc đơn hàng đã hoàn thành
              if($result && $mess){
                echo '<script type="text/javascript">alert("Cập nhật trạng thái đơn hàng thành công!");</script>';
                
              } else {
                echo '<script type="text/javascript">alert("Cập nhật trạng thái đơn hàng thất bại!");</script>';
              
              }
            }
?>