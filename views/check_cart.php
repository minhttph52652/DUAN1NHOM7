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
        echo '<script type="text/javascript">alert("T├ái khoß║ún cß╗ºa bß║ín kh├┤ng thß╗â ─æß║╖t h├áng trong 10 ng├áy do ph├ít hiß╗çn c├│ ─æ╞ín spam!"); history.back();</script>';
    }
    else {
        if($check == 1){
            echo '<script type="text/javascript">alert("Sß╗æ l╞░ß╗úng h├áng kh├┤ng ─æß╗º!"); history.back();</script>';
        }
        else{
            header("Location: delivery_address.php");
        }
        
    
    }
}
else {
    if($check == 1){
        echo '<script type="text/javascript">alert("Sß╗æ l╞░ß╗úng h├áng kh├┤ng ─æß╗º!"); history.back();</script>';
    }
    else {
        header("Location: delivery_address.php");
    }
}
?>
