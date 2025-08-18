<?php 
  include '../../lib/session.php';
  include '../../models/product.php';
     Session::checkSession('admin');
  include '../../models/categories.php';
    Session::checkSession('admin');// Kiểm tra quyền truy cập
    $role_id = Session::get('role_id');// Lấy ID vai trò
     if( isset($_GET['id'])){
           $c = new categories();
           $result = $c->delete($_GET['id']);// hiện lên sản phẩm theo id
           if($result){
                echo '<script type="text/javascript">alert("Xóa sản phẩm thành công!"); history.back();</script>';

           } else {
                echo '<script type="text/javascript">alert("Xóa sản phẩm thất bại!"); history.back();</script>';
           }
     }
?>
