<?php
/**
 * FILE: register.php
 * CHỨC NĂNG: Trang đăng ký tài khoản mới cho user
 * LUỒNG XỬ LÝ:
 * 1. Load model user để xử lý logic đăng ký
 * 2. Xử lý form POST với thông tin đăng ký
 * 3. Gọi method insert() để tạo tài khoản mới
 * 4. Chuyển hướng đến trang đăng nhập nếu thành công
 */

// Load model user để xử lý logic đăng ký
include '../models/user.php'; // hoặc classes/user.php nếu bạn dùng tên đó

// Xử lý form POST khi user submit thông tin đăng ký
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Khởi tạo đối tượng user
    $user = new user();
    
    // Gọi method insert() để tạo tài khoản mới
    $result = $user->insert($_POST);

    if ($result === true) {
        // Thành công: Hiển thị thông báo và chuyển đến trang đăng nhập
        echo "<script>alert('Đăng ký thành công!'); window.location.href = 'login.php';</script>";
        exit();
    } else {
        // Thất bại: Hiển thị thông báo lỗi
        echo "<script>alert('$result');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng nhập</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    
<!-- Navigation menu chính -->
<nav>
    <label class="logo"><a href="index.php">IVY Moda</a></label>
    <ul id="dc_mega-menu-orange">
        <li class="li-index"><a href="index.php">Trang chủ</a></li>
        <li class="li-index"><a href="productList.php">Sản phẩm</a></li>
        <li class="li-index"><a href="order.php">Đơn hàng</a></li>
        
        <?php if (Session::get('user')) { ?>
            <!-- Menu khi đã đăng nhập -->
            <li class="li-index"><a href="info.php">Thông tin cá nhân</a></li>
            <li class="li-index"><a href="logout.php">Đăng xuất</a></li>
        <?php } else { ?>
            <!-- Menu khi chưa đăng nhập -->
            <li class="li-index"><a href="register.php">Đăng ký</a></li>
            <li class="li-index"><a href="login.php" class="active">Đăng nhập</a></li>
        <?php } ?>
    </ul>
    
    <!-- Form đăng ký tài khoản -->
    <form method="POST" action="">
        <input type="text" name="fullName" placeholder="Họ tên" required>
        <input type="email" name="email" placeholder="Email" required>
        <!-- <input type="date" name="dob" placeholder="Ngày sinh" required> -->
        <input type="text" name="address" placeholder="Địa chỉ" required>
        <input type="password" name="password" placeholder="Mật khẩu" required>
        <button type="submit">Đăng ký</button>
    </form>
    
</nav>

</form>
</body>
</html> 