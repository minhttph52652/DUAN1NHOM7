<?php 
    include_once __DIR__ . '/../../lib/session.php';
    include_once __DIR__ . '/../../models/product.php';
    include_once __DIR__ . '/../../models/categories.php';

  Session::checkSession('admin');
  $role_id = Session::get('role_id');

  if (isset($_GET['id'])) {
      $product = new product();
      $result = $product->delete($_GET['id']);
      if ($result) {
          echo '<script type="text/javascript">alert("Xóa sản phẩm thành công!"); history.back();</script>';
      } else {
          echo '<script type="text/javascript">alert("Xóa sản phẩm thất bại!"); history.back();</script>';
      }
  }
?>
