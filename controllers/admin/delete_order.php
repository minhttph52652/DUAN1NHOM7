<?php
include_once('../../lib/session.php');
include_once('../../models/product.php');

Session::checkSession('admin');
$role_id = Session::get('role_id');
if ($role_id != 1){
    header("Location:../../index.php");
    exit();
}

$productObj = new product();

// 1. Lấy dữ liệu sản phẩm theo productId
if(isset($_GET['productId']) && is_numeric($_GET['productId'])){
    $productId = (int)$_GET['productId'];
    $productData = $productObj->getProductbyIdAdmin($productId);
    if($productData){
        $product = mysqli_fetch_assoc($productData);
    } else {
        echo '<script>alert("Sản phẩm không tồn tại!"); window.location.href="productlist.php";</script>';
        exit();
    }
} else {
    echo '<script>alert("Mã sản phẩm không hợp lệ!"); window.location.href="productlist.php";</script>';
    exit();
}

// 2. Xử lý submit form
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    // Thêm id vào mảng $_POST để hàm update biết update sản phẩm nào
    $_POST['id'] = $productId;

    $result = $productObj->update($_POST, $_FILES);

    if($result){
        echo '<script>alert("Cập nhật sản phẩm thành công!"); window.location.href="productlist.php";</script>';
    } else {
        echo '<script>alert("Cập nhật sản phẩm thất bại!"); window.location.href="productlist.php";</script>';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sửa sản phẩm</title>
</head>
<body>
<h2>Sửa sản phẩm</h2>
<form action="" method="post" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?= $product['id'] ?>">
    Tên: <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>"><br>
    Giá gốc: <input type="number" name="originalPrice" value="<?= $product['originalPrice'] ?>"><br>
    Giá khuyến mãi: <input type="number" name="promotionPrice" value="<?= $product['promotionPrice'] ?>"><br>
    Số lượng: <input type="number" name="qty" value="<?= $product['qty'] ?>"><br>
    Mô tả: <textarea name="des"><?= htmlspecialchars($product['des']) ?></textarea><br>
    Ảnh hiện tại: <img src="<?= $product['image'] ?>" width="100"><br>
    Ảnh mới: <input type="file" name="image"><br>
    <button type="submit">Cập nhật</button>
</form>
</body>
</html>
