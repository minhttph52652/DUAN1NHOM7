 <?php 
  include_once '../../lib/session.php';
  Session::checkSession('admin');
    $role_id = Session::get('role_id');
    if ($role_id != 1) {
      // Code for non-admin users
    } else {
        header("Location:../../index.php");
    }

   include_once '../../models/order.php';
   if( isset($_GET['orderId'])){
      $order = new order();
      $result = $order->deliveringOrder($_GET['orderId']);// cập  nhật trang tháu order đang giao hàng
      if($result){
          echo '<script type="text/javascript">alert("Cập nhật trạng thái đơn hàng thành công!");</script>';
        } else {
          echo '<script type="text/javascript">alert("Cập nhật trạng thái đơn hàng thất bại!");</script>';
   }
}
 ?>