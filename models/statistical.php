<?php
$filepath = realpath(dirname(__FILE__));
include_once($filepath . '/../lib/database.php');
include_once($filepath . '/../lib/session.php');
include_once($filepath . '/../models/statistical.php');
?>

<?php


class statistical
{
    private $db;
     public function __construct()
    {
        $this->db = new Database();
    }

    public function getStatistical($id, $sale, $quantity) // Lấy thông tin thống kê
    {
        $query = "SELECT receivedDate FROM orders WHERE id = $id ";// câu lệnh lấy ngày nhận hàng từ đơn hàng
        $mysqli_result = $this->db->select($query);// thực hiện truy vấn
        $row_order = $mysqli_result->fetch_assoc();// lấy thông tin đơn hàng
        $order_date = $row_order['receivedDate'];// lấy ngày nhận hàng
        $profit = $sale;// lấy lợi nhuận

        $query1 = "SELECT * FROM statistical WHERE order_date = '$order_date'";// câu lệnh lấy thông tin thống kê theo ngày
        $mysqli_result1 = $this->db->select($query1);// thực hiện truy vấn
        if ($mysqli_result1) {
            $query2 = 
            "UPDATE `statistical` SET `sales` = `sales` + '$sale', `profit` = `profit` + '$profit',
            `quantity` = `quantity` + '$quantity', `total_order` = `total_order` + 1 WHERE `order_date` = '$order_date'";// câu lệnh cập nhật thông tin thống kê
            $mysqli_result2 = $this->db->update($query2);// thực hiện truy vấn
        }
        else
        {
            $query3 = "INSERT INTO statistical VALUES (NULL, '$order_date', '$sale', '$profit', '$quantity', 1 )";// câu lệnh thêm mới thông tin thống kê
            $mysqli_result3 = $this->db->insert($query3);// thực hiện truy vấn
        }
    }

    public function getDatesInRange($startDate, $endDate)// Lấy danh sách ngày trong khoảng thời gian
    {
        $dateArray = array();// Khởi tạo mảng chứa các ngày
        $currentDate = strtotime($startDate);// Lấy thời gian bắt đầu
        while ($currentDate <= strtotime($endDate)) {
            $dateArray[] = date('Y-m-d', $currentDate);// Lấy ngày hiện tại
            $currentDate = strtotime('+1 day', $currentDate);// Lấy thời gian ngày tiếp theo
        }
        return $dateArray;
    }

    public function filterByDate($start, $end)// Lọc thống kê theo khoảng thời gian
    {
        $query = "SELECT * FROM statistical WHERE order_date BETWEEN '$start' AND '$end' ";// câu lệnh lọc thống kê theo khoảng thời gian
        $mysqli_result = $this->db->select($query);// thực hiện truy vấn
        if ($mysqli_result) {
            $result = mysqli_fetch_all($this->db->select($query),  MYSQLI_ASSOC);// lấy tất cả kết quả
            return $result;
        }
        return false;
    }

    public function getSumTotalOrder($start, $end) {// Tính tổng đơn hàng
        $query = "SELECT SUM(total_order) AS sum_order, SUM(sales) AS sum_sale, SUM(profit) AS sum_profit FROM statistical WHERE order_date BETWEEN '$start' AND '$end'";// câu lệnh tính tổng đơn hàng
        $mysqli_result = $this->db->select($query);// thực hiện truy vấn
        if ($mysqli_result) {
            $result = $mysqli_result->fetch_assoc();// lấy kết quả
            return $result;
        }
        return false;
    }

}
?>
