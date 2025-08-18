<?php
include_once '../../lib/database.php';
include_once '../../lib/session.php';
include_once '../../models/statistical.php';

Session::checkSession('admin');
if (Session::get('role_id') != 1) {
    header("Location:../../index.php");
    exit();
}

$stat = new statistical();

// Lấy tham số start và end từ yêu cầu POST
$start = isset($_POST['start']) ? $_POST['start'] : '';
$end = isset($_POST['end']) ? $_POST['end'] : date('Y-m-d', strtotime('2025-08-18'));

// Xác định khoảng thời gian dựa trên tham số range
if (empty($start)) {
    $range = isset($_POST['range']) ? $_POST['range'] : 'month';
    if ($range == 'week') {
        $start = date('Y-m-d', strtotime('-1 week', strtotime('2025-08-18')));
    } elseif ($range == 'month') {
        $start = date('Y-m-d', strtotime('-1 month', strtotime('2025-08-18')));
    } elseif ($range == 'year') {
        $start = date('Y-m-d', strtotime('-1 year', strtotime('2025-08-18')));
    }
}

// Lấy dữ liệu thống kê theo khoảng thời gian
$result = $stat->filterByDate($start, $end);

// Tính tổng số đơn hàng và lợi nhuận
$total_orders = 0;
$total_profit = 0;
if ($result) {
    foreach ($result as $row) {
        $total_orders += (int)$row['total_order'];
        $total_profit += (float)$row['profit'];
    }
}

// Lấy danh sách tất cả các ngày trong khoảng thời gian
$allDates = $stat->getDatesInRange($start, $end);

// Tạo mảng dữ liệu cho biểu đồ
$data = [];
foreach ($allDates as $date) {
    $found = false;
    if ($result) {
        foreach ($result as $row) {
            if ($row['order_date'] == $date) {
                $data[] = [
                    'order_date' => $date,
                    'total_order' => (int)$row['total_order'],
                    'profit' => (float)$row['profit']
                ];
                $found = true;
                break;
            }
        }
    }
    if (!$found) {
        $data[] = [
            'order_date' => $date,
            'total_order' => 0,
            'profit' => 0.0
        ];
    }
}

// Trả về dữ liệu dưới dạng JSON bao gồm cả tổng hợp
$response = [
    'data' => $data,
    'total_orders' => $total_orders,
    'total_profit' => $total_profit
];
echo json_encode($response);
?>