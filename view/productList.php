<?php
/**
 * FILE: productList.php
 * CHỨC NĂNG: Trang hiển thị danh sách sản phẩm với phân trang và tìm kiếm
 * LUỒNG XỬ LÝ:
 * 1. Load các model cần thiết (session, product, categories, cart)
 * 2. Lấy danh sách sản phẩm theo danh mục và phân trang
 * 3. Xử lý tìm kiếm sản phẩm theo tên
 * 4. Hiển thị giao diện danh sách sản phẩm với slider banner
 */

// Load các model cần thiết
include_once '../lib/session.php';
include_once '../models/product.php';
include_once '../models/categories.php';
include_once '../models/cart.php';

// Khởi tạo đối tượng giỏ hàng và lấy tổng số lượng
$cart = new cart();
$totalQty = $cart->getTotalQtyByUserId();

// Khởi tạo đối tượng product và lấy dữ liệu
$product = new product();
// Lấy danh sách sản phẩm theo danh mục và trang hiện tại
$list = $product->getProductsByCateId((isset($_GET['page']) ? $_GET['page'] : 1), (isset($_GET['cateId']) ? $_GET['cateId'] : 6));
// Lấy tổng số trang để phân trang
$pageCount = $product->getCountPagingClient((isset($_GET['cateId']) ? $_GET['cateId'] : 6));

// Khởi tạo đối tượng categories và lấy tất cả danh mục
$categories = new categories();
$categoriesList = $categories->getAll();

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
    <title>Danh sách sản phẩm</title>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
    
    <!-- Script xử lý slider banner tự động chuyển ảnh -->
    <script>
        $(function() {
            $('.fadein img:gt(0)').hide();
            setInterval(function() {
                $('.fadein :first-child').fadeOut().next('img').fadeIn().end().appendTo('.fadein');
            }, 1500);
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
        <sup class="sumItem">
            <?= ($totalQty['total']) ? $totalQty['total'] : "0" ?>
        </sup>
    </a>
</nav>

    <!-- Section banner slider hiển thị ảnh từ thư mục slider -->
    <section class="banner">
        <div class="fadein">
            <?php
            // Hiển thị ảnh từ thư mục slider
            // Đường dẫn thư mục
            $dir = "../images/slider/";

            $scan_dir = scandir($dir);
            foreach ($scan_dir as $img) :
                if (in_array($img, array('.', '..')))
                    continue;
            ?>
                <img src="<?php echo $dir . $img ?>" alt="<?php echo $img ?>">
            <?php endforeach; ?>
        </div>
    </section>
    <hr style="margin: 25px 177px 0 177px;color: black;border: 1px solid;">
    <div class="featuredProducts">
        <h1>GALLERY</h1>    
    </div>
    <hr style="margin: 0px 177px 0 177px;color: black;border: 1px solid;">
    <div class="category">
        Danh mục: <select onchange="location = this.value;">
            <?php
            foreach ($categoriesList as $key => $value) {
                if ($value['id'] == $_GET['cateId']) { ?>
                    <option selected value="productList.php?cateId=<?= $value['id'] ?>"><?= $value['name'] ?></option>
                <?php } else { ?>
                    <option value="productList.php?cateId=<?= $value['id'] ?>"><?= $value['name'] ?></option>
                <?php } ?>
            <?php }
            ?>
        </select>
    </div>
    <div class="container" style="grid-template-columns: auto auto auto auto;">
        <?php if ($list) {
            foreach ($list as $key => $value) { ?>
                <div class="card">
                    <div class="imgBx">
                 <a href="../detail.php?id=<?= $value['id'] ?>" title="<?= $value['name'] ?>">
    <img src="../controllers/admin/uploads/<?= $value['image'] ?>" alt="">
</a>

                    </div>
                    <div class="content">
                        <div class="productName">
                            <a href="detail.php?id=<?= $value['id'] ?>" title="<?= $value['name'] ?>">
                                <h3><?= $value['name'] ?></h3>
                            </a>
                        </div>
                        <div>
                            Đã bán: <?= $value['soldCount'] ?>
                        </div>
                        <div class="bothPrice">
                    <div class="price">
                        <?= number_format($value['promotionPrice'], 0, '', ',') ?>đ
                    </div>
                    <div class="original-price">
                        <?php
                        if ($value['promotionPrice'] < $value['originalPrice']) { ?>
                             <del><?= number_format($value['originalPrice'], 0, '', ',') ?>đ</del>
                        <?php } else { ?>
                            <p>...</p>
                        <?php } ?>
                    </div>
                    </div>
                    <div class="action">
                        <a class="add-cart" href="add_cart.php?id=<?= $value['id'] ?>"><i class="fa fa-shopping-bag"></i></a>
                        <!-- <a class="detail" href="detail.php?id=<?= $value['id'] ?>">Xem chi tiết</a> -->
                    </div>
                    </div>
                </div>
            <?php }
        } else { ?>
            <h3>Không có sản phẩm nào...</h3>
        <?php  }
        ?>
    </div>
    <div class="pagination">
        <a href="productList.php?page=<?= (isset($_GET['page'])) ? (($_GET['page'] <= 1) ? 1 : $_GET['page'] - 1) : 1 ?>&cateId=<?= (isset($_GET['cateId'])) ? $_GET['cateId'] : 2 ?>">&laquo;</a>
        <?php
        // echo  $pageCount;
        for ($i = 1; $i <= $pageCount; $i++) {
            if (isset($_GET['page'])) {
                if ($i == $_GET['page']) { ?>
                    <a class="active" href="productList.php?page=<?= $i ?>&cateId=<?= (isset($_GET['cateId'])) ? $_GET['cateId'] : 2 ?>"><?= $i ?></a>
                <?php } else { ?>
                    <a href="productList.php?page=<?= $i ?>&cateId=<?= (isset($_GET['cateId'])) ? $_GET['cateId'] : 2 ?>"><?= $i ?></a>
                <?php  }
            } else { ?>
                <a href="productList.php?page=<?= $i ?>&cateId=<?= (isset($_GET['cateId'])) ? $_GET['cateId'] : 1 ?>"><?= $i ?></a>
            <?php  } ?>
        <?php }
        ?>
        <a href="productList.php?page=<?= (isset($_GET['page'])) ? $_GET['page'] + 1 : 2 ?>&cateId=<?= (isset($_GET['cateId'])) ? $_GET['cateId'] : 2 ?>">&raquo;</a>
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