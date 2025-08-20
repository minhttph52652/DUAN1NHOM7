<?php
/**
 * FILE: detail.php
 * CHỨC NĂNG: Trang hiển thị chi tiết sản phẩm
 * LUỒNG XỬ LÝ:
 * 1. Load các model cần thiết (product, cart, categories)
 * 2. Lấy thông tin sản phẩm theo ID từ URL
 * 3. Xử lý tìm kiếm sản phẩm nếu có
 * 4. Hiển thị giao diện chi tiết sản phẩm với chức năng thêm vào giỏ hàng
 */

// Load các model cần thiết
include '../models/product.php';
include_once '../models/cart.php';
include_once '../models/categories.php';

// Khởi tạo đối tượng giỏ hàng và lấy tổng số lượng
$cart = new cart();
$totalQty = $cart->getTotalQtyByUserId();

// Khởi tạo đối tượng product và lấy dữ liệu
$product = new product();
$list = $product->getProductsByCateId((isset($_GET['page']) ? $_GET['page'] : 1), (isset($_GET['cateId']) ? $_GET['cateId'] : 6));

// Lấy thông tin chi tiết sản phẩm theo ID từ URL
$result = $product->getProductbyId($_GET['id']);
    if (!$result) {
    echo 'Không tìm thấy sản phẩm!';
    die();
    }
    
    // Xử lý tìm kiếm sản phẩm nếu có parameter search
    if (isset($_GET['search'])) {
    $search = addslashes($_GET['search']);
    if (empty($search)) {
        echo '<script type="text/javascript">alert("Yêu cầu dữ liệu không được để trống!");</script>';
    } else {
        // Tìm sản phẩm theo tên
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
        <li class="li-index"><a href="../productList.php">Sản phẩm</a></li>
        <li class="li-index"><a href="../order.php" id="order">Đơn hàng</a></li>

        <?php
        // Hiển thị menu tùy theo trạng thái đăng nhập
        if (isset($_SESSION['user']) && $_SESSION['user']) { ?>
            <!-- Menu khi đã đăng nhập -->
            <li class="li-index"><a href="../info.php" id="signin">Thông tin cá nhân</a></li>
            <li class="li-index"><a href="../logout.php" id="signin">Đăng xuất</a></li>
        <?php } else { ?>
            <!-- Menu khi chưa đăng nhập -->
            <li class="li-index"><a href="../register.php" id="signup">Đăng ký</a></li>
            <li class="li-index"><a href="../login.php" id="signin">Đăng nhập</a></li>
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
    <a class="cart" href="./checkout.php">
        <i class="fa fa-shopping-cart"></i>
        <sup class="sumItem">
            <?= ($totalQty['total']) ? $totalQty['total'] : "0" ?>
        </sup>
    </a>
</nav>

    <hr>
    <div class="featuredProductsDetail">
        <h1>Sản phẩm</h1>
    </div>
    <hr style="
    height: 0px;
    margin: 0 100px;
    border: 2px solid;
    color: black;">
    
    <!-- Container chính hiển thị chi tiết sản phẩm -->
    <div class="container-single">
        <!-- Phần hiển thị hình ảnh sản phẩm -->
        <div class="image-product">
           <img src="../controllers/admin/uploads/<?= $result['image'] ?>" alt="">
        </div>
        
        <!-- Phần hiển thị thông tin sản phẩm -->
        <div class="info">
            <div class="name">
                <h2><?= $result['name'] ?></h2>
            </div>
            
            <!-- Hiển thị giá sản phẩm (giá khuyến mãi và giá gốc) -->
            <div class="detailBothPrice">
            <div class="price-single">
             <b><?= number_format($result['promotionPrice'], 0, '', ',') ?>VND</b>
            </div>
            <?php
            // Hiển thị giá gốc nếu có khuyến mãi
            if ($result['promotionPrice'] < $result['originalPrice']) { ?>
                <div class="price-del">
              <del><?= number_format($result['originalPrice'], 0, '', ',') ?>VND</del>
                </div>
            <?php }
            ?>
            </div>
            
            <!-- Thông tin chi tiết sản phẩm -->
            <div class="des">
                <p>Số lượng còn: <?= $result['qty'] ?></p>
                <p>Đã bán: <?= $result['soldCount'] ?></p>
                <hr>
                <?= $result['des'] ?>
            </div>
            
            <!-- Nút thêm vào giỏ hàng -->
            <div class="add-cart-single">
                <a href="add_cart.php?id=<?= $result['id'] ?>">Thêm vào giỏ</a>
            </div>
        </div>
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
        <p class="copyright">copy by IVYmoda.com 2024</p>
    </footer>
</body>

</html>