<?php
// Gồm model user để lấy thông tin người dùng
include '../models/user.php';

// Khởi tạo đối tượng user
$user = new user();

// Lấy thông tin cá nhân người dùng
$userInfo = $user->get();

// Kiểm tra khi người dùng submit form POST
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    // Gọi hàm update trong model user để cập nhật thông tin
    $result = $user->update($_POST);
    if ($result) {
        // Thông báo thành công
        echo '<script type="text/javascript">alert("Cập nhật thông tin cá nhân thành công!"); history.back();</script>';
        header("Location:./info.php");
    } else {
        // Thông báo thất bại
        echo '<script type="text/javascript">alert("Cập nhật thông tin cá nhân thất bại!"); history.back();</script>';
        header("Location:./info.php");
    }
}
?>