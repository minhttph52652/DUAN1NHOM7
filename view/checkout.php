<?php
/**
 * FILE: checkout.php
 * CHỨC NĂNG: Trang hiển thị giỏ hàng và thông tin đặt hàng
 * LUỒNG XỬ LÝ:
 * 1. Load session và kiểm tra đăng nhập client
 * 2. Lấy thông tin giỏ hàng và tổng tiền
 * 3. Lấy thông tin user để hiển thị
 * 4. Hiển thị giao diện giỏ hàng với chức năng cập nhật số lượng
 */

// Load session và kiểm tra đăng nhập client
include_once '../lib/session.php';
Session::checkSession('client');

// Load các model cần thiết
include_once '../models/cart.php';
include_once '../models/user.php';

// Khởi tạo đối tượng giỏ hàng và lấy dữ liệu
$cart = new cart();
$list = $cart->get(); // Lấy danh sách sản phẩm trong giỏ hàng
$totalPrice = $cart->getTotalPriceByUserId(); // Lấy tổng tiền
$totalQty = $cart->getTotalQtyByUserId(); // Lấy tổng số lượng

// Khởi tạo đối tượng user và lấy thông tin
$user = new user();
$userInfo = $user->get();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://use.fontawesome.com/2145adbb48.js"></script>
    <script src="https://kit.fontawesome.com/a42aeb5b72.js" crossorigin="anonymous"></script>
    <title>Checkout</title>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
    
    <!-- Script xử lý slider banner tự động chuyển ảnh -->
    <script>
        $(function() {
            $('.fadein img:gt(0)').hide();
            setInterval(function() {
                $('.fadein :first-child').fadeOut().next('img').fadeIn().end().appendTo('.fadein');
            }, 5000);
        });
    </script>
</head>

<body>
<!-- Navigation menu chính -->
<nav>
    <label class="logo"><a href="../index.php">IVY Moda</a></label>
    <ul id="dc_mega-menu-orange">
        <li class="li-index"><a href="../index.php">Trang chủ</a></li>
        <li class="li-index"><a href="productList.php">Sản phẩm</a></li>
        <li class="li-index"><a href="order.php" id="order">Đơn hàng</a></li>

        <?php if (isset($_SESSION['user']) && $_SESSION['user']) { ?>
            <!-- Menu khi đã đăng nhập -->
            <li class="li-index"><a href="info.php" id="signin">Thông tin cá nhân</a></li>
            <li class="li-index"><a href="logout.php" id="signin">Đăng xuất</a></li>
        <?php } else { ?>
            <!-- Menu khi chưa đăng nhập -->
            <li class="li-index"><a href="register.php" id="signup">Đăng ký</a></li>
            <li class="li-index"><a href="login.php" id="signin">Đăng nhập</a></li>
        <?php } ?>
    </ul>

    <!-- Form tìm kiếm sản phẩm -->
    <form class="c-search" action="" method="get">
        <div class="header_search">
            <input type="text" class="search_input" name="search" placeholder="Nhập tên sản phẩm">
            <button type="submit"><i class="fas fa-search"></i></button>
        </div>
    </form>

    <!-- Icon giỏ hàng với số lượng sản phẩm -->
    <a class="cart" href="checkout.php">
        <i class="fa fa-shopping-cart"></i>
        <sup class="sumItem" id="totalQtyHeader">
            <?= ($totalQty['total']) ? $totalQty['total'] : "0" ?>
        </sup>
    </a>
</nav>

    <hr style="margin: 122px 177px -102px 177px;color: black;border: 1px solid;">
    <div class="featurecheckout">
        <h1>Giỏ hàng</h1>
    </div>
    <hr style="margin: 0px 177px 0 177px;color: black;border: 1px solid;">
    
    <!-- Container chính hiển thị giỏ hàng -->
    <div class="container-checkout">
        <?php
        // Kiểm tra có sản phẩm trong giỏ hàng không
        if ($list) { ?>
            <!-- Bảng hiển thị danh sách sản phẩm trong giỏ hàng -->
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
                <?php
                $count = 1;
                // Duyệt qua từng sản phẩm trong giỏ hàng
                foreach ($list as $key => $value) { ?>
                    <tr>
                        <td><?= $count++ ?></td>
                        <td><?= $value['productName'] ?></td>
                        <td><img class="image-cart" src="../controllers/admin/uploads/<?= $value['productImage'] ?>"></td>
                        <td><?= number_format($value['productPrice'], 0, '', ',') ?>VND </td>
                        <td>
                            <!-- Input số lượng với chức năng cập nhật real-time -->
                            <input id="<?= $value['productId'] ?>" type="number" name="qty" class="qty" value="<?= $value['qty'] ?>" onchange="update(this)" min="1">
                        </td>
                        <td>
                            <!-- Link xóa sản phẩm khỏi giỏ hàng -->
                            <a style="color: black;" href="delete_cart.php?id=<?= $value['id'] ?>"><i class="fa fa-trash" aria-hidden="true"></i></a>
                        </td>
                    </tr>
                <?php }
                ?>
            </table>
            </div>
            
            <!-- Panel thông tin đơn hàng bên phải -->
            <div class="orderinfo">
                <div class="buy">
                    <h3>Thông tin đơn đặt hàng</h3>
                    <div>
                        Người đặt hàng: <b><?= $userInfo['fullname'] ?></b>
                    </div>
                    <div>
                        Số lượng: <b id="qtycart"><?= $totalQty['total'] ?></b>
                    </div>
                    <div>
                        Tổng tiền: <b id="totalcart"><?= number_format($totalPrice['total'], 0, '', ',') ?>VND</b>
                    </div>
                    <div>
                        Địa chỉ nhận hàng: <b><?= $userInfo['address'] ?></b>
                    </div>
                    <div class="buy-btn">
                        <!-- Nút tiến hành đặt hàng -->
                        <a href="check_cart.php?status=<?= $userInfo['status'] ?> &userId=<?= $userInfo['id'] ?>">Tiến hành đặt hàng</a>
                    </div>
                </div>
            </div>
        <?php } else { ?>
            <!-- Thông báo khi giỏ hàng rỗng -->
            <h3>Giỏ hàng hiện đang rỗng</h3>
        <?php }
        ?>
    </div>
    </div>
    
    <!-- Footer -->
    <footer>
        <div class="social">
            <a href="#"><i class="fa fa-facebook" aria-hidden="true"></i></a>
            <a href="#"><i class="fa fa-twitter" aria-hidden="true"></i></a>
            <a href="#"><i class="fa fa-instagram" aria-hidden="true"></i></a>
        </div>
        <ul class="list">
            <li>
                <a href="./">Trang Chủ</a>
            </li>
            <li>
                <a href="productList.php">Sản Phẩm</a>
            </li>
        </ul>
        <p class="copyright">copy by IVYmoda.com 2025</p>
    </footer>
</body>

<!-- Script xử lý cập nhật số lượng sản phẩm real-time -->
<script type="text/javascript">
    /**
     * Hàm cập nhật số lượng sản phẩm trong giỏ hàng
     * Sử dụng AJAX để cập nhật không reload trang
     */
    function update(e) {
        var http = new XMLHttpRequest();
        var url = 'update_cart.php';
        var params = "productId=" + e.id + "&qty=" + e.value;
        http.open('POST', url, true);

        http.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

        http.onreadystatechange = function() {
            if (http.readyState === XMLHttpRequest.DONE) {
                var status = http.status;
                if (status === 200) {
                    // Xử lý response JSON từ server
                    var arr = http.responseText;
                    var b = false;
                    var result = "";
                    for (let index = 0; index < arr.length; index++) {
                        if (arr[index] == "[") {
                            b = true;
                        }
                        if (b) {
                            result += arr[index];
                        }
                    }
                    var arrResult = JSON.parse(result.replace("undefined", ""));
                    console.log(arrResult);
                    
                    // Cập nhật UI với dữ liệu mới
                    document.getElementById("totalQtyHeader").innerHTML = arrResult[1]['total'];
                    document.getElementById("qtycart").innerHTML = arrResult[1]['total'];
                    document.getElementById("totalcart").innerHTML = arrResult[0]['total'].replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,") + "VND";

                    //alert('Đã cập nhật giỏ hàng!');
                } else if (status === 501) {
                    // Xử lý trường hợp hết hàng
                    alert('Số lượng sản phẩm không đủ để thêm vào giỏ hàng!');
                    e.value = parseInt(e.value) - 1;
                } else {
                    // Xử lý lỗi khác
                    alert('Cập nhật giỏ hàng thất bại!');
                    window.location.reload();
                }
            }
        }
        http.send(params);
    }

    // Ngăn chặn nhập ký tự không phải số vào input số lượng
    var list = document.getElementsByClassName("qty");
    for (let item of list) {
        item.addEventListener("keypress", function(evt) {
            evt.preventDefault();
        });
    }
</script>

</html>