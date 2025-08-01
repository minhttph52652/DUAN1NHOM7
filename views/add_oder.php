<?php
// Gọi file session.php để xử lý phiên đăng nhập
include_once __DIR__ . '/../lib/session.php';

// Kiểm tra người dùng đã đăng nhập với vai trò "client" chưa
Session::checkSession('client');

// Gọi model order để xử lý các chức năng liên quan đến đơn hàng
include_once __DIR__ . '/../models/order.php';

// Kiểm tra nếu request được gửi bằng phương thức POST (tức là khi người dùng gửi form đặt hàng)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order = new order();           // Tạo một đối tượng từ class order
    $result = $order->add($_POST);  // Gọi phương thức add() và truyền dữ liệu form để tạo đơn hàng mới

   
}
?>