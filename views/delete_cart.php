<?php
// Nạp file session để sử dụng phiên đăng nhập
include_once '../lib/session.php';

// Kiểm tra xem người dùng đã đăng nhập với quyền "client" chưa
Session::checkSession('client');

// Nạp model giỏ hàng để sử dụng các hàm liên quan đến giỏ hàng
include_once '../models/cart.php';