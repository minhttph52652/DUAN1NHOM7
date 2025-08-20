<?php
include_once '../../lib/session.php';
include_once '../../models/order.php';

Session::checkSession('admin');
$role_id = Session::get('role_id');
if ($role_id != 1) {
    header("Location:../../index.php");
    exit();
}

if (isset($_GET['productId']) && is_numeric($_GET['productId'])) {
    $productId = (int)$_GET['productId'];
    $order = new order(); // hoặc $product nếu xử lý sản phẩm
    $result = $order->processedOrder($productId);
    if ($result) {
        echo '<script>
                alert("Cập nhật sản phẩm thành công!");
                window.location.href = "productlist.php";
              </script>';
    } else {
        echo '<script>
                alert("Cập nhật sản phẩm thất bại!");
                window.location.href = "productlist.php";
              </script>';
    }
} else {
    echo '<script>
            alert("⚠️ Mã sản phẩm không hợp lệ!");
            window.location.href = "productlist.php";
          </script>';
}

?>
