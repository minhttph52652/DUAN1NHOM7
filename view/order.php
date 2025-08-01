<?php
include_once __DIR__. '/../lib/session.php';
Session::checkSession('client');
include_once __DIR__. '/../models/order.php';
include_once __DIR__. '/../models/cart.php';


$cart = new cart();
$totalQty = $cart ->getTotalQtyByUserId();

$order = new order();
$result = $order->getOrderByUser();
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
         <link rel="stylesheet" href="../css/style.css">
    <script src="https://use.fontawesome.com/2145adbb48.js"></script>
    <script src="https://kit.fontawesome.com/a42aeb5b72.js" crossorigin="anonymous"></script>
    
    <title>Đăng nhập</title>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
    <script>
        $(function() {
            $('.fadein img:gt(0)').hide();
            setInterval(function(){
                $('.fadein :first-child').fadeOut().next('img').fadeIn().end().appendTo('.fadein');
            }, 5000)
        });

    </script>
</head>
<body>
     <nav>
        <label class="logo"><a href="index.php">IVY Moda</a></label>
        <ul id="dc_mega-menu-orange">
            <li class="li-index"><a href="index.php">IVY Moda</a></li>
              <li class="li-index"><a href="productList.php">Sản phẩm</a></li>
                <li class="li-index"><a href="order.php">Đơn hàng</a></li>
                <?php if (Session::get('user')) {?>
                    
                 <li class="li-index"><a href="info.php">Thông tin cá nhân</a></li>
                  <li class="li-index"><a href="logout.php" class="active">Đăng xuất</a></li>

                <?php } else {?>
                
                     <li class="li-index"><a href="register.php">Đăng ký</a></li>
                      <li class="li-index"><a href="login.php">Đăng nhập</a></li>
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
            <sup class="sumItem"><?=($totalQty['total']) ? $totalQty['total'] : "0"?></sup>
        </a>
    </nav>

    <hr style="margin: 122px 177px -102px 177px; color: black; border: 1px solid;">
    <div class="orderFeature">
                    <h1>Đơn hàng</h1>
    </div>
    <hr style="margin: 0px 177px 0 177px; color: black; border: 1px solid">

    <div class="container-single">
        <?php if ($result) { ?> 
            <table  class="order orderDetail">
            <tr>
                <th>STT</th>
                <th>Mã đơn hàng</th>
                <th>Ngày đặt</th>
                <th>Ngày giao</th>
                <th>Tình trạng</th>
                <th>Thao tác</th>
            </tr>
            <?php $count =1;
            foreach ($result as $value) { ?> 
            <tr>
                <td><?= $count++ ?></td>
                <td><?= $value['id'] ?></td>
                 <td><?= $value['createdDate'] ?></td>

                 <td>
                    <?= ($value['status'] == "Cancel" || $value['status'] == "Spam") ? "" : (($value['status'] != "Processcing") ? $value['receivedDate'] : "Dự kiến 3 ngày sau đơn hàng sẽ được xử lý") ?>
                     <?= ($value['status'] != "Processed" ) ? "" : "Dự kiến" ?>
                 </td>

                 <?php if ($value['status'] == 'Delivered') {?> 
                    <td>
                        <a href="..//complete_order.php?orderId=<? $value['id'] ?>">Đã giao(Click để xác nhận)</a>
                    </td>

                     <td>
                        <a href="..//orderdetail.php?orderId=<? $value['id'] ?>">Chi tiết</a>
                    </td>
                    <?php }else {?>
                        <td><?= $value['status'] ?></td>
                        <td>
                            <a href="../orderdetail.php?orderId=<?= $value['id'] ?>">Chi tiết</a>
                        </td>


                    <?php } ?>


                 </tr>   
             <?php } ?>
            </table>

       <?php } else {?>
        <h3>Đơn hàng đang rỗng</h3>
    <?php } ?>
    </div>

      <footer>
    <div class="social">
        <a href="#"><i class="fa fa-facebook"></i></a>
        <a href="#"><i class="fa fa-twitter"></i></a>
        <a href="#"><i class="fa fa-instagram"></i></a>
    </div>
    <ul class="list">
        <li><a href="./">Trang Chủ</a></li>
        <li><a href="productList.php">Sản Phẩm</a></li>
    </ul>
    <p class="copyright">copy by IVYmoda.com 2025</p>
</footer>


</body>
</html>