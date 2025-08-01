<?php
// Gọi file session.php để xử lý phiên đăng nhập
include_once __DIR__ . '/../lib/session.php';

// Kiểm tra người dùng đã đăng nhập với vai trò "client" chưa
Session::checkSession('client');

// Gọi model order để xử lý các chức năng liên quan đến đơn hàng
include_once __DIR__ . '/../models/order.php';
