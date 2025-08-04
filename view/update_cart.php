<?php
include_once '../lib/session.php';
Session::checkSession('client');
include_once '../models/cart.php';

$cart = new cart();

// Lấy dữ liệu từ Ajax
$productId = isset($_POST['productId']) ? (int)$_POST['productId'] : 0;
$qty = isset($_POST['qty']) ? (int)$_POST['qty'] : 1;

// Gọi phương thức update trong model cart
$result = $cart->update($productId, $qty);

// Trường hợp hết hàng
if ($result === 'out of stock') {
    http_response_code(501);
    exit;
}

// Trường hợp lỗi khác
if (!$result) {
    http_response_code(403);
    exit;
}

// Nếu thành công -> Lấy lại tổng tiền và tổng số lượng
$totalPrice = $cart->getTotalPriceByUserId();
$totalQty = $cart->getTotalQtyByUserId();

// Trả về JSON
header('Content-Type: application/json; charset=utf-8');
http_response_code(200);
echo json_encode([$totalPrice, $totalQty]);
exit;
