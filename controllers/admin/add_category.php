<?php
       include '../../lib/session.php';
       include '../../models/categories.php';// kiểm tra file có tồn tại hay ko
        Session ::checkSession('admin');
        $role_id = Session::get('role_id');// kiểm tra người dùng đã đăng nhập với vai trò admin chưa. Nếu chưa, có thể tự động chuyển hướng hoặc báo lỗi.
       if ($role_id == 1) {//Nếu $role_id == 1 → nghĩa là admin → cho phép tiếp tục.
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {//Form gửi bằng phương thức POST.  //Có tồn tại $_POST['submit'] (nút submit được nhấn).
        $category = new categories();
        $result = $category->insert($_POST['name']);
    }
} else {
    header("Location:../../index.php");
}
  
  ?>
 


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://use.fontawesome.com/2145adbb48.js"></script>
    <script src="https://kit.fontawesome.com/a42aeb5b72.js" crossorigin="anonymous"></script>
    <title>Thêm mới danh mục</title>
</head>

<body>
    <nav>
        <input type="checkbox" id="check">
        <label for="check" class="checkbtn">
            <i class="fas fa-bars"></i>
        </label>
        <label class="logo"><a href="index.php">ADMIN</a></label>
        <ul>
            <li><a href="productlist.php" >Quản lý Sản phẩm</a></li>
            <li><a href="categoriesList.php" class="active" >Quản lý danh mục</a></li>
            <li><a href="orderlist.php">Quản lý Đơn hàng</a></li>
            <li><a href="userlist.php">Quản lý Người dùng</a></li>
            <li><a href="chartms.php">Thống kê</a></li>
            <li><a href="transfer.php">Chuyển về trang chủ</a></li>
        </ul>
    </nav>
    <div class="title">
        <h1>Thêm mới danh mục</h1>
    </div>
    <div class="container">
        <p style="color: green;"><?= !empty($result) ? $result : '' ?></p>
        <div class="form-add">
            <form action="add_category.php" method="post">
                <label for="name">Tên danh mục</label>
                <input type="text" id="name" name="name" placeholder="Tên danh mục.." required>

                <input type="submit" value="Lưu" name="submit">
            </form>
        </div>
    </div>
    </div>
    <footer>
        <p class="copyright">copy by IVYmoda.com 2025</p>
    </footer>
</body>

</html>