<?php
// Nạp file session để sử dụng phiên đăng nhập
include_once '../lib/session.php';

// Kiểm tra xem người dùng đã đăng nhập với quyền "client" hay chưa
Session::checkSession('client');

// Nạp model xử lý đơn hàng
include_once '../models/order.php';