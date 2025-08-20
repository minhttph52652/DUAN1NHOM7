<?php
/**
 * FILE: update_cart.php
 * CHỨC NĂNG: API xử lý cập nhật số lượng sản phẩm trong giỏ hàng (AJAX)
 * LUỒNG XỬ LÝ:
 * 1. Kiểm tra session đăng nhập của client
 * 2. Nhận dữ liệu từ AJAX request (productId, qty)
 * 3. Gọi model cart để cập nhật số lượng
 * 4. Trả về JSON response với tổng tiền và số lượng mới
 */

// Load session và kiểm tra đăng nhập client
include_once '../lib/session.php';
Session::checkSession('client');

// Load model cart để xử lý logic cập nhật giỏ hàng
include_once '../models/cart.php';

// Khởi tạo đối tượng giỏ hàng
$cart = new cart();

// Lấy dữ liệu từ Ajax request
$productId = isset($_POST['productId']) ? (int)$_POST['productId'] : 0;
$qty = isset($_POST['qty']) ? (int)$_POST['qty'] : 1;

// Gọi phương thức update trong model cart để cập nhật số lượng
$result = $cart->update($productId, $qty);

// Trường hợp hết hàng: Trả về HTTP status 501
if ($result === 'out of stock') {
    http_response_code(501);
    exit;
}

// Trường hợp lỗi khác: Trả về HTTP status 403
if (!$result) {
    http_response_code(403);
    exit;
}

// Nếu thành công -> Lấy lại tổng tiền và tổng số lượng mới
$totalPrice = $cart->getTotalPriceByUserId();
$totalQty = $cart->getTotalQtyByUserId();

// Trả về JSON response với dữ liệu mới
header('Content-Type: application/json; charset=utf-8');
http_response_code(200);
echo json_encode([$totalPrice, $totalQty]);
exit;
