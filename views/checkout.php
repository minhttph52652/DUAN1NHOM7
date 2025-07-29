<?php
// Load file session và kiểm tra xem user có đăng nhập chưa (dành cho client)
include_once '../lib/session.php';
Session::checkSession('client');

// Load các model cần thiết
include_once '../models/cart.php';
include_once '../models/user.php';

// Tạo đối tượng cart để thao tác với giỏ hàng
$cart = new cart();
$list = $cart->get(); // Lấy danh sách sản phẩm trong giỏ hàng
$totalPrice = $cart->getTotalPriceByUserId(); // Lấy tổng tiền giỏ hàng
$totalQty = $cart->getTotalQtyByUserId(); // Lấy tổng số lượng sản phẩm trong giỏ

// Tạo đối tượng user để lấy thông tin người dùng
$user = new user();
$userInfo = $user->get(); // Lấy thông tin người dùng đang đăng nhập
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Thiết lập bảng mã ký tự là UTF-8 -->
    <meta charset="UTF-8">
    
    <!-- Tối ưu hiển thị cho trình duyệt Internet Explorer -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <!-- Cho phép giao diện responsive trên các thiết bị khác nhau -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Link tới file CSS chính của website -->
    <link rel="stylesheet" href="../css/style.css">

    <!-- Font Awesome: bộ icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://use.fontawesome.com/2145adbb48.js"></script>
    <script src="https://kit.fontawesome.com/a42aeb5b72.js" crossorigin="anonymous"></script>

    <!-- Tiêu đề trang -->
    <title>Checkout</title>

    <!-- Load jQuery từ CDN -->
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>

    <!-- Script để tạo hiệu ứng chuyển đổi hình ảnh (nếu có) -->
    <script>
        $(function () {
            $('.fadein img:gt(0)').hide(); // Ẩn tất cả ảnh trừ ảnh đầu tiên
            setInterval(function () {
                $('.fadein :first-child').fadeOut().next('img').fadeIn().end().appendTo('.fadein');
            }, 5000); // Lặp lại mỗi 5 giây
        });
    </script>
</head>

<nav>
    <!-- Logo trang, click để về trang chủ -->
    <label class="logo"><a href="../index.php">IVY Moda</a></label>

    <!-- Menu chính -->
    <ul id="dc_mega-menu-orange">
        <li class="li-index"><a href="../index.php">Trang chủ</a></li>
        <li class="li-index"><a href="productList.php">Sản phẩm</a></li>
        <li class="li-index"><a href="order.php" id="order">Đơn hàng</a></li>

        <!-- Hiển thị tùy chọn nếu đã đăng nhập -->
        <?php if (isset($_SESSION['user']) && $_SESSION['user']) { ?>
            <li class="li-index"><a href="info.php" id="signin">Thông tin cá nhân</a></li>
            <li class="li-index"><a href="logout.php" id="signin">Đăng xuất</a></li>
        <?php } else { ?>
            <!-- Nếu chưa đăng nhập -->
            <li class="li-index"><a href="register.php" id="signup">Đăng ký</a></li>
            <li class="li-index"><a href="login.php" id="signin">Đăng nhập</a></li>
        <?php } ?>
    </ul>

    <!-- Form tìm kiếm sản phẩm -->
    <form class="c-search" action="" method="get">
        <div class="header_search">
            <!-- Ô nhập từ khóa tìm kiếm -->
            <input type="text" class="search_input" name="search" placeholder="Nhập tên sản phẩm">
            <!-- Nút tìm kiếm -->
            <button type="submit"><i class="fas fa-search"></i></button>
        </div>
    </form>

    <!-- Biểu tượng giỏ hàng -->
    <a class="cart" href="checkout.php">
        <i class="fa fa-shopping-cart"></i>
        <!-- Số lượng sản phẩm hiện có trong giỏ -->
        <sup class="sumItem">
            <?= ($totalQty['total']) ? $totalQty['total'] : "0" ?>
        </sup>
    </a>
</nav>

<hr style="margin: 122px 177px -102px 177px;color: black;border: 1px solid;">

<!-- Tiêu đề phần giỏ hàng -->
<div class="featurecheckout">
    <h1>Giỏ hàng</h1>
</div>

<!-- Dòng kẻ dưới tiêu đề -->
<hr style="margin: 0px 177px 0 177px;color: black;border: 1px solid;">

        <!-- Phần chính hiển thị giỏ hàng -->
<div class="container-checkout">
    <?php if ($list) { ?>
    
        <!-- Bảng hiển thị sản phẩm trong giỏ -->
        <div class="tableContainer">
        <table class="order">
            <tr>
                <th>STT</th>
                <th>Tên sản phẩm</th>
                <th>Hình ảnh</th>
                <th>Đơn giá</th>
                <th>Số lượng</th>
                <th>Thao tác</th>
            </tr>
            <!-- Lặp qua từng sản phẩm trong giỏ -->
            <?php
            $count = 1;
            foreach ($list as $key => $value) { ?>
                <tr>
                    <td><?= $count++ ?></td>
                    <td><?= $value['productName'] ?></td>
                    <td><img class="image-cart" src="admin/uploads/<?= $value['productImage'] ?>"></td>
                    <td><?= number_format($value['productPrice'], 0, '', ',') ?>VND </td>
                    <td>
                        <!-- Input thay đổi số lượng -->
                        <input id="<?= $value['productId'] ?>" type="number" name="qty" class="qty" value="<?= $value['qty'] ?>" onchange="update(this)" min="1">
                    </td>
                    <td>
                        <!-- Nút xóa sản phẩm -->
                        <a style="color: black;" href="delete_cart.php?id=<?= $value['id'] ?>"><i class="fa fa-trash" aria-hidden="true"></i></a>
                    </td>
                </tr>
            <?php } ?>
        </table>
        </div>
        
                    <!-- Thông tin đơn hàng bên phải -->
        <div class="orderinfo">
            <div class="buy">
                <h3>Thông tin đơn đặt hàng</h3>
                <!-- Tên người đặt hàng -->
                <div>Người đặt hàng: <b><?= $userInfo['fullname'] ?></b></div>
                <!-- Tổng số lượng sản phẩm -->
                <div>Số lượng: <b id="qtycart"><?= $totalQty['total'] ?></b></div>
                <!-- Tổng giá trị đơn hàng -->
                <div>Tổng tiền: <b id="totalcart"><?= number_format($totalPrice['total'], 0, '', ',') ?>VND</b></div>
                <!-- Địa chỉ nhận hàng -->
                <div>Địa chỉ nhận hàng: <b><?= $userInfo['address'] ?></b></div>
                
                <!-- Nút tiến hành đặt hàng -->
                <div class="buy-btn">
                    <a href="check_cart.php?status=<?= $userInfo['status'] ?>&userId=<?= $userInfo['id'] ?>">Tiến hành đặt hàng</a>
                </div>
            </div>
        </div>
    <?php } else { ?>
        <!-- Nếu giỏ rỗng thì thông báo -->
        <h3>Giỏ hàng hiện đang rỗng</h3>
    <?php } ?>
</div>


    <!-- Phần footer -->
<footer>
    <!-- Các icon mạng xã hội -->
    <div class="social">
        <a href="#"><i class="fa fa-facebook" aria-hidden="true"></i></a>
        <a href="#"><i class="fa fa-twitter" aria-hidden="true"></i></a>
        <a href="#"><i class="fa fa-instagram" aria-hidden="true"></i></a>
    </div>

    <!-- Các liên kết -->
    <ul class="list">
        <li><a href="./">Trang Chủ</a></li>
        <li><a href="productList.php">Sản Phẩm</a></li>
    </ul>

    <!-- Bản quyền -->
    <p class="copyright">copy by IVYmoda.com 2024</p>
</footer>
</body>

<script type="text/javascript">
    // Hàm cập nhật giỏ hàng khi người dùng thay đổi số lượng sản phẩm
    function update(e) {
        // Tạo đối tượng XMLHttpRequest để gửi request đến server
        var http = new XMLHttpRequest();

        // URL của file xử lý cập nhật giỏ hàng
        var url = 'update_cart.php';

        // Tạo chuỗi tham số cần gửi: gồm id sản phẩm và số lượng mới
        var params = "productId=" + e.id + "&qty=" + e.value;

        // Mở kết nối theo phương thức POST
        http.open('POST', url, true);

        // Thiết lập header để gửi dữ liệu dưới dạng form (x-www-form-urlencoded)
        http.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

        // Gọi hàm khi có sự thay đổi trạng thái của request
        http.onreadystatechange = function () {
            // Khi request hoàn tất
            if (http.readyState === XMLHttpRequest.DONE) {
                var status = http.status;

                // Nếu status là 200 (thành công)
                if (status === 200) {
                    var arr = http.responseText;
                    var b = false;
                    var result = "";

                    // Lấy ra phần JSON từ chuỗi phản hồi (cắt bỏ phần không cần thiết)
                    for (let index = 0; index < arr.length; index++) {
                        if (arr[index] == "[") {
                            b = true;
                        }
                        if (b) {
                            result += arr[index];
                        }
                    }

                    // Parse chuỗi JSON thành mảng object
                    var arrResult = JSON.parse(result.replace("undefined", ""));

                    console.log(arrResult); // Debug ra console để xem kết quả

                    // Cập nhật tổng số lượng sản phẩm ở phần header
                    document.getElementById("totalQtyHeader").innerHTML = arrResult[1]['total'];

                    // Cập nhật tổng số lượng trong phần thông tin đơn hàng
                    document.getElementById("qtycart").innerHTML = arrResult[1]['total'];

                    // Cập nhật tổng tiền, định dạng lại số tiền có dấu phẩy
                    document.getElementById("totalcart").innerHTML = arrResult[0]['total'].replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,") + "VND";

                    // Có thể mở alert thông báo cập nhật thành công nếu muốn
                    // alert('Đã cập nhật giỏ hàng!');
                }

                // Nếu status 501: lỗi do vượt quá số lượng tồn kho
                else if (status === 501) {
                    alert('Số lượng sản phẩm không đủ để thêm vào giỏ hàng!');
                    // Trừ đi 1 đơn vị để khớp với số tối đa có thể
                    e.value = parseInt(e.value) - 1;
                }

                // Các lỗi khác
                else {
                    alert('Cập nhật giỏ hàng thất bại!');
                    // Tải lại trang
                    window.location.reload();
                }
            }
        }

        // Gửi request với tham số đã chuẩn bị
        http.send(params);
    }

    // Cấm người dùng nhập số lượng bằng bàn phím, chỉ được bấm nút tăng/giảm
    var list = document.getElementsByClassName("qty");
    for (let item of list) {
        item.addEventListener("keypress", function (evt) {
            evt.preventDefault(); // Ngăn không cho gõ bàn phím vào ô số lượng
        });
    }
</script>
</html>    