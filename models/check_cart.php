<?php 
include_once '../lib/session.php';
include_once '../models/user.php';
include_once '../models/order.php';
include_once '../models/cart.php';
include_once '../models/product.php';
$oder = new oder();
$product = new product();
$cart = new cart();
$listProduct = $product->getAll();

foreach ($listProduct as $key => $value) {
    $listProduct[$key]['image'] = '../controllers/admin/uploads/' . $value['image'];
    $ts = $value['productId'];
    $checkproduct = $product->checkQty($ts);
    if($checkproduct == false){
        $check = 1;
    }
}

if (isset($_GET['status']) && $_GET['status'] == 2) {
    $currentDate = date('Y-m-d');
    $mysqli_result = $order->getDate($_GET['userId']);
    $row = $mysqli_result->fetch_assoc();
    $targetDate = $row['receivedDate'];
    if($currentDate >= $targetDate) {
        $user = new user();
        $result = $user->updateStatus($_GET['userId']);
        $_GET['status'] = 1;
        $toOrderCancel = $order->toOrderCancel($_GET['userId']);
    }
    if($_GET['status'] == 2) {
        echo '<script type="text/javascript">alert("Tài khoản của bạn không thể đặt hàng trong 10 ngày do phát hiện có đơn spam!"); history.back();</script>';
    }
    else {
        if($check == 1){
            echo '<script type="text/javascript">alert("Số lượng hàng không đủ!"); history.back();</script>';
        }
        else{
            header("Location: delivery_address.php");
        }
        
    
    }
}
else {
    if($check == 1){
        echo '<script type="text/javascript">alert("Số lượng hàng không đủ!"); history.back();</script>';
    }
    else {
        header("Location: delivery_address.php");
    }
}
?>