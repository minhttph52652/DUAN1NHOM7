<?php
/**
 * FILE: orderDetail.php
 * CHỨC NĂNG: Trang hiển thị chi tiết đơn hàng với chức năng in hóa đơn PDF
 * LUỒNG XỬ LÝ:
 * 1. Kiểm tra session đăng nhập của client
 * 2. Load các model cần thiết (cart, orderDetails, order, user)
 * 3. Lấy chi tiết đơn hàng theo orderId từ URL
 * 4. Xử lý in hóa đơn PDF khi user click nút in
 * 5. Hiển thị giao diện chi tiết đơn hàng
 */

// Load session và kiểm tra đăng nhập client
include_once '../lib/session.php';
Session::checkSession('client');

// Load các model cần thiết
include_once '../models/cart.php';
include_once '../models/orderDetails.php';
include_once '../models/order.php';

// Khởi tạo các đối tượng cần thiết
$cart = new cart();
$orderDetails = new orderDetails();

// Lấy thông tin giỏ hàng và chi tiết đơn hàng
$totalQty = $cart->getTotalQtyByUserId();
$totalQty1 = $cart->getTotalQtyByUserId();
$result = $orderDetails->getOrderDetails($_GET['orderId']);

// Load model user và lấy thông tin
include_once '../models/user.php';
$totalPrice = $orderDetails->getTotalPriceByUserId($_GET['orderId']);
$totalQty = $orderDetails->getTotalQtyByUserId($_GET['orderId']);
$user = new user();
$userInfo = $user->get();

// Lấy thông tin đơn hàng
$order = new order();
$order_result = $order->getById($result[0]['orderId']);

// Include thư viện TCPDF để tạo file PDF
require_once '../lib/TCPDF-main/tcpdf.php';

// Xử lý khi user click nút in hóa đơn
if (isset($_POST['print_invoice'])) {
    ob_start();
    
    // Tạo instance PDF mới
    $pdf = new TCPDF();

    // Thêm trang vào PDF
    $pdf->AddPage();

    // Thiết lập font
    $pdf->SetFont('dejavusans', '', 12);

    // Thêm nội dung vào PDF
    $pdf->Write(10, 'Thông tin đơn đặt hàng:');
    $pdf->Ln(); // Thêm dòng mới

    // Thêm thông tin đơn hàng
    $pdf->Write(8, 'Người đặt hàng: ' . $userInfo['fullname']);
    $pdf->Ln();
    $pdf->Write(8, 'Người nhận hàng: ' . $order_result['fullname']);
    $pdf->Ln();
    $pdf->Write(8, 'Số điện thoại nhận hàng: ' . $order_result['phoneNumber']);
    $pdf->Ln();
    $pdf->Write(8, 'Số lượng: ' . $totalQty['total']);
    $pdf->Ln();
    $pdf->Write(8, 'Tổng tiền: ' . number_format($totalPrice['total'], 0, '', ',') . 'VND');
    $pdf->Ln();
    $pdf->Write(8, 'Địa chỉ nhận hàng: ' . $order_result['address']);
    $pdf->Ln();

    // Thêm chi tiết sản phẩm
    $pdf->Write(10, 'Chi tiết sản phẩm:');
    $pdf->Ln(); // Thêm dòng mới

    // Thiết lập màu cho header bảng
    $pdf->SetFillColor(200, 220, 255);
    $pdf->SetTextColor(0);

    // Tạo header bảng
    $pdf->Cell(20, 10, 'STT', 1, 0, 'C', 1);
    $pdf->Cell(120, 10, 'Tên sản phẩm', 1, 0, 'C', 1);
    $pdf->Cell(40, 10, 'Đơn giá', 1, 0, 'C', 1);
    $pdf->Cell(20, 10, 'Số lượng', 1, 0, 'C', 1);
    $pdf->Ln(); // Thêm dòng mới
    
    // Duyệt qua từng sản phẩm để thêm vào PDF
    $count = 1;
    foreach ($result as $key => $value) {
        $pdf->Cell(20, 10, $count++, 1);
        $pdf->Cell(120, 10, $value['productName'], 1);
        $pdf->Cell(40, 10, number_format($value['productPrice'], 0, '', ',') . 'VND', 1);
        $pdf->Cell(20, 10, $value['qty'], 1);
        $pdf->Ln(); // Thêm dòng mới
    }
    
    // Output PDF ra browser (đã comment để không tự động download)
    // $pdf->Output('hoa_don.pdf', 'D');
    // ob_end_flush();
}
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
    <title>Order</title>
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
<nav>
        <label class="logo"><a href="index.php">IVY Moda</a></label>
        <ul id="dc_mega-menu-orange">
            <li class="li-index"><a href="index.php" >Trang chủ</a></li>
            <li class="li-index"><a href="productList.php" >Sản phẩm</a></li>

            <li class="li-index"><a href="order.php" id="order">Đơn hàng</a></li>
                
            <?php
            if (isset($_SESSION['user']) && $_SESSION['user']) { ?>
                <li class="li-index"><a href="info.php" id="signin">Thông tin cá nhân</a></li>
                <li class="li-index"><a href="logout.php" id="signin">Đăng xuất</a></li>
            <?php } else { ?>
                <li class="li-index"><a href="register.php" id="signup"  >Đăng ký</a></li>
                <li class="li-index"><a href="login.php" id="signin">Đăng nhập</a></li>
            <?php } ?>
        </ul>
        <form class="c-search" action="" method="get">
            <div class="header_search">
                <input type="text" class="search_input" name="search" placeholder="Nhập tên sản phẩm">
                <button type="submit"><i class="fas fa-search"></i></button>
            </div>
        </form>
        <a class="cart" href="checkout.php">
                    <i class="fa fa-shopping-cart"></i>
                    <sup class="sumItem">
                        <?= ($totalQty1['total']) ? $totalQty1['total'] : "0" ?>
                    </sup>
        </a>
    </nav>
    <hr style="margin: 122px 177px -102px 177px;color: black;border: 1px solid;">
    <div class="orderdetailsfeature">
        <h1>Chi tiết đơn hàng <?= $_GET['orderId'] ?></h1>
    </div>
    <hr style="margin: 0px 177px 0 177px;color: black;border: 1px solid;">
    <div class="infor_order">
        <h3>Thông tin đơn đặt hàng</h3>
        <div>
            Người đặt hàng: <b><?= $userInfo['fullname'] ?></b>
        </div>
        <div>
            Người nhận hàng: <b><?= $order_result['fullname'] ?></b>
        </div>
        <div>
            Số điện thoại nhận hàng: <b><?= $order_result['phoneNumber'] ?></b>
        </div>
        <div>
            Số lượng: <b id="qtycart"><?= $totalQty['total'] ?></b>
        </div>
        <div>
            Tổng tiền: <b id="totalcart"><?= number_format($totalPrice['total'], 0, '', ',') ?>VND</b>
        </div>
        <div>
            Địa chỉ nhận hàng: <b><?= $order_result['address'] ?></b>
        </div>    
    </div>

    <div class="container-orderdetail">
        <table class="orderdetailTable">
            <tr>
                <th>STT</th>
                <th>Tên sản phẩm</th>
                <th>Hình ảnh</th>
                <th>Đơn giá</th>
                <th>Số lượng</th>
            </tr>
            <?php $count = 1;
            foreach ($result as $key => $value) { ?>
                <tr>
                    <td><?= $count++ ?></td>
                    <td><?= $value['productName'] ?></td>
                    <td><img class="image-cart" src="../controllers/admin/uploads/<?= $value['productImage'] ?>" alt=""></td>
                    <td><?= number_format($value['productPrice'], 0, '', ',') ?>VND</td>
                    <td><?= $value['qty'] ?></td>
                </tr>
            <?php }
            ?>
        </table>
	<form method="post" action="">
    <!-- <input id="btnInHoaDon" type="submit" name="print_invoice" value="In hóa đơn"> -->
</form>
    </div>
    </div>
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

</html> 