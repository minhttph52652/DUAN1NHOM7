<?php
include '../models/product.php';
include_once '../models/cart.php';
include_once '../models/categories.php';
$cart = new cart();
$totalQty = $cart->getTotalQtyByUserId();

$product = new product();
$list = $product->getProductsByCateId((isset($_GET['page']) ? $_GET['page'] : 1), (isset($_GET['cateId']) ? $_GET['cateId'] : 6));
$result = $product->getProductbyId($_GET['id']);
    if (!$result) {
    echo 'Kh├┤ng t├¼m thß║Ñy sß║ún phß║⌐m!';
    die();
    }
    if (isset($_GET['search'])) {
    $search = addslashes($_GET['search']);
    if (empty($search)) {
        echo '<script type="text/javascript">alert("Y├¬u cß║ºu dß╗» liß╗çu kh├┤ng ─æ╞░ß╗úc ─æß╗â trß╗æng!");</script>';
    } else {
        $list = $product->getProductByName($search);
       
    }
} 
?>
<?php

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
    <title><?= $result['name'] ?></title>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
    <script>
        $(function(){
            $('.fadein img:gt(0)').hide();
            setInterval(function(){
                $('.fadein :first-child').fadeOut().next('img').fadeIn().end().appendTo('.fadein');
            },5000);
        });
    </script>
</head>
<body>
    <nav>
        <label class="logo"><a href="../index.php">IVY Moda</a></label>
        <ul id="dc_mega-menu-orange">
            <li class="li-index"><a href="../index.php">Trang chß╗º</a></li>
            <li class="li-index"><a href="../productList.php">Sß║ún phß║⌐m</a></li>
            <li class="li-index"><a href="../order.php" id="order">─É╞ín h├áng</a></li>

            <?php
            if (isset($_SESION['user']) && $_SESSION['user'])  { ?>
                <li class="li-index"><a href="../info.php" id="signin">Th├┤ng tin c├í nh├ón</a></li>
                <li class="li-index"><a href="../logout.php" id="signin">─É─âng xuß║Ñt</a></li>
            <?php } else { ?>
                <li class="li-index"><a href="../register.php" id="signup">─É─âng k├╜</a></li>
                <li class="li-index"><a href="../login.php" id="signin">─É─âng nhß║¡p</a></li>
            <?php } ?>
        </ul>
        
        <form class="c-search" action="" method="get">
        <div class="header_search">
            <input type="text" class="search_input" name="search" placeholder="Nhß║¡p t├¬n sß║ún phß║⌐m">
            <button type="submit"><i class="fas fa-search"></i></button>
        </div>
        </form>  
     
        <a class="cart" href="./checkout.php">
            <i class="fa fa-shopping-cart"></i>
            <sup class="sumItem">
                <?= ($totalQty['total']) ? $totalQty['total'] : "0" ?>
            </sup>
        </a>
    </nav>
    <hr>
    <div class="featuredProductsDetail">
        <h1>Sß║ún phß║⌐m</h1>
    </div>
    <hr style="
    height: 0px;
    margin: 0 100px;
    border: 2px solid;
    color: black;">
    <div class="container-single">
        <div class="image-product">
           <img src="../controllers/admin/uploads/<?= $result['image'] ?>" alt="">

        </div>
        <div class="info">
            <div class="name">
                <h2><?= $result['name'] ?></h2>
            </div>
            <div class="detailBothPrice">
            <div class="price-single">
             <b><?= number_format($result['promotionPrice'], 0, '', ',') ?>VND</b>
            </div>
            <?php
            if ($result['promotionPrice'] < $result['originalPrice']) { ?>
                <div class="price-del">
              <del><?= number_format($result['originalPrice'], 0, '', ',') ?>VND</del>
                </div>
            <?php }
            ?>
            </div>
            <div class="des">
                <p>Sß╗æ l╞░ß╗úng c├▓n: <?= $result['qty'] ?></p>
                <p>─É├ú b├ín: <?= $result['soldCount'] ?></p>
                <hr>
                <?= $result['des'] ?>
            </div>
            <div class="add-cart-single">
                <a href="add_cart.php?id=<?= $result['id'] ?>">Th├¬m v├áo giß╗Å</a>
            </div>
        </div>
    </div>
    </div>
    <footer>
        <div class="social">
            <a href="#"><i class="fa fa-facebook" aria-hidden="true"></i> </a>
            <a href="#"><i class="fa fa-teitter" aria-hidden="true"></i> </a>
            <a href="#"><i class="fa fa-instagram" aria-hidden="true"></i> </a>
        </div>
        <ul class="list">
            <li>
                <a href="./">Trang chß╗º</a>
            </li>
            <li>
                <a href="productList.php">Sß║ún phß║⌐m</a>
            </li>
        </ul>
        <p class="coppyright">copy by IVYmoda.com 2025</p>
    </footer>

</body>
</html>
