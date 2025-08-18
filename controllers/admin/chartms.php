<?php
include '../../models/statistical.php';
include_once '../../lib/session.php';
Session::checkSession('admin');
if(Session::get('role_id') != 1){
    header("Location:../../index.php");
}

$stat = new statistical();
$today = date('Y-m-d');
$monthAgo = date('Y-m-d', strtotime('-1 month'));
$weekAgo = date('Y-m-d', strtotime('-1 week'));
$yearAgo = date('Y-m-d', strtotime('-1 year'));

// Mặc định hiển thị tháng này
$currentSum = $stat->getSumTotalOrder($monthAgo, $today);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Thống kê chi tiết</title>
<link rel="stylesheet" href="./css/style.css">
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css">
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>
<style>
body{font-family:Arial;margin:20px;background:#f9f9f9;}
nav ul{list-style:none;padding:0;display:flex;gap:15px;}
nav ul li a{text-decoration:none;color:#333;}
nav ul li a.active{font-weight:bold;}
.title h1{margin-top:20px;}
.container1{margin-top:20px;}
.box{display:flex;gap:20px;margin-bottom:20px;}
.part{flex:1;background:#fff;padding:15px;border-radius:10px;text-align:center;box-shadow:0 2px 5px rgba(0,0,0,0.1);}
.button-group{margin-bottom:20px;}
.button-group button{padding:10px 20px;margin-right:10px;cursor:pointer;border:none;border-radius:5px;background:#2196F3;color:#fff;}
.button-group button.active{background:#4CAF50;}
#chart{height:300px;background:#fff;padding:10px;border-radius:10px;}
</style>
</head>
<body>

<nav>
    <ul>
        <li><a href="productlist.php">Quản lý Sản phẩm</a></li>
        <li><a href="categoriesList.php">Quản lý Danh mục</a></li>
        <li><a href="orderlist.php">Quản lý Đơn hàng</a></li>
        <li><a href="userlist.php">Quản lý Người dùng</a></li>
        <li><a href="orderlist.php" class="active">Thống kê</a></li>
        <li><a href="transfer.php">Chuyển về trang chủ</a></li>
    </ul>
</nav>

<div class="title"><h1>Thống kê số liệu</h1></div>

<div class="container1">

    <div class="button-group">
        <button data-range="week">Tuần này</button>
        <button class="active" data-range="month">Tháng này</button>
        <button data-range="year">365 ngày qua</button>
    </div>

    <div class="box" id="summary">
        <div class="part">
            <span>Số đơn hàng</span>
            <h3><?=$currentSum['sum_order']?></h3>
        </div>
        <div class="part">
            <span>Doanh thu</span>
            <h3><?=number_format($currentSum['sum_sale'])?> VND</h3>
        </div>
        <div class="part">
            <span>Lợi nhuận</span>
            <h3><?=number_format($currentSum['sum_profit'])?> VND</h3>
        </div>
    </div>

    <div id="chart"></div>
</div>

<footer><p class="copyright">copy by IVYmoda.com 2025</p></footer>

<script>
var chart = new Morris.Bar({
    element: 'chart',
    hideHover: 'auto',
    parseTime: false,
    xkey: 'order_date',
    ykeys: ['total_order', 'profit'],
    labels: ['Số đơn hàng', 'Lợi nhuận'],
    barColors: ['#4CAF50', '#FF9800']
});

function loadData(range) {
    var start;
    var end = "<?=$today?>";
    if (range == "week") start = "<?=$weekAgo?>";
    else if (range == "month") start = "<?=$monthAgo?>";
    else if (range == "year") start = "<?=$yearAgo?>";

    $.post('ajax_chart.php', { start: start, end: end, range: range }, function(res) {
        var response = JSON.parse(res);
        if (response.data.length > 0) {
            chart.setData(response.data);

            // Cập nhật box số liệu
            $('#summary').html(
                '<div class="part"><span>Số đơn hàng</span><h3>' + response.total_orders + '</h3></div>' +
                '<div class="part"><span>Lợi nhuận</span><h3>' + number_format(response.total_profit) + ' VND</h3></div>'
            );
        } else {
            chart.setData([]);
            $('#summary').html('<p>Không có dữ liệu</p>');
        }
    });
}

// Hàm format số
function number_format(number) {
    return number.toLocaleString('vi-VN');
}

// Mặc định tháng này
loadData('month');

// Nút preset
$('.button-group button').click(function() {
    $('.button-group button').removeClass('active');
    $(this).addClass('active');
    var range = $(this).data('range');
    loadData(range);
});
</script>

</body>
</html>
