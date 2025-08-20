<!-- 
/**
 * FILE: check_cart.php
 * CHỨC NĂNG: Kiểm tra giỏ hàng trước khi cho phép đặt hàng
 * LUỒNG XỬ LÝ:
 * 1. Load các model cần thiết (user, order, cart, product)
 * 2. Kiểm tra số lượng tồn kho của tất cả sản phẩm trong giỏ
 * 3. Kiểm tra trạng thái tài khoản (có bị spam không)
 * 4. Chuyển hướng đến trang địa chỉ giao hàng nếu hợp lệ
 */

<?php
// Load các model cần thiết để xử lý logic kiểm tra
include_once '../lib/session.php';
include_once '../models/user.php';
include_once '../models/order.php';
include_once '../models/cart.php';
include_once '../models/product.php';

// Khởi tạo các đối tượng cần thiết
$order = new order();
$product = new product();
$cart = new cart();

// Lấy danh sách ID sản phẩm trong giỏ hàng của user hiện tại
$listProductId = $cart->getListProductIdInCartByUserId();

// Kiểm tra số lượng tồn kho của từng sản phẩm trong giỏ hàng
foreach ($listProductId as $key => $value) {
    $ts = $value['productId'];
    $checkProduct = $product->checkQty($ts);
    if($checkProduct == false){
        $check = 1; // Đánh dấu có sản phẩm không đủ số lượng
    }
}

// Xử lý trường hợp kiểm tra trạng thái đơn hàng
if (isset($_GET['status']) && $_GET['status'] == 2) {
    // Lấy ngày hiện tại
    $currentDate = date('Y-m-d');
    
    // Lấy ngày nhận hàng từ đơn hàng của user
    $mysqli_result = $order->getDate($_GET['userId']);
    $row = $mysqli_result->fetch_assoc();
    $targetDate = $row['receivedDate'];
    
    // Kiểm tra xem đã hết thời gian cấm đặt hàng chưa (10 ngày)
    if($currentDate >= $targetDate) {
        // Cập nhật trạng thái user về bình thường
        $user = new user();
        $result = $user->updateStatus($_GET['userId']);
        $_GET['status'] = 1;
        
        // Chuyển đơn hàng về trạng thái hủy
        $toOrderCancel = $order->toOrderCancel($_GET['userId']);
    }
    
    // Kiểm tra trạng thái sau khi xử lý
    if($_GET['status'] == 2) {
        // Vẫn còn trong thời gian cấm: Hiển thị thông báo
        echo '<script type="text/javascript">alert("Tài khoản của bạn không thể đặt hàng trong 10 ngày do phát hiện có đơn spam!"); history.back();</script>';
    }
    else {
        // Hết thời gian cấm: Kiểm tra số lượng hàng
        if($check == 1){
            echo '<script type="text/javascript">alert("Số lượng hàng không đủ!"); history.back();</script>';
        }
        else{
            // Mọi thứ hợp lệ: Chuyển đến trang địa chỉ giao hàng
            header("Location: delivery_address.php");
        }
    }
}
else {
    // Trường hợp không có trạng thái đặc biệt: Chỉ kiểm tra số lượng hàng
    if($check == 1){
        echo '<script type="text/javascript">alert("Số lượng hàng không đủ!"); history.back();</script>';
    }
    else{
        // Số lượng hàng đủ: Chuyển đến trang địa chỉ giao hàng
        header("Location: delivery_address.php");
    }
}  
?>  
