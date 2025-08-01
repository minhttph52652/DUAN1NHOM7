<?php
$filepath = realpath(dirname(__FILE__));
include_once($filepath . '/../lib/database.php');
include_once($filepath . '/../lib/session.php');
include_once($filepath . '/../models/cart.php');
?>

<?php

class orderDetails
{
    private $db;
    public function __construct()
    {
        $this->db = new Database();
    }
 //Lấy toàn bộ chi tiết đơn hàng (các sản phẩm trong đơn) dựa theo orderId
    public function getOrderDetails($orderId)
    {
        $query = "SELECT * FROM order_details WHERE orderId = $orderId ";
        $mysqli_result = $this->db->select($query);
        if ($mysqli_result) {
            $result = mysqli_fetch_all($this->db->select($query), MYSQLI_ASSOC);
            return $result;
        }
        return false;
    }
//Tính tổng tiền của đơn hàng bằng cách lấy tổng (productPrice * qty) từ bảng order_details.
    public function getTotalPriceByUserId($orderId)
    {
        $query = "SELECT SUM(productPrice*qty) as total FROM order_details WHERE orderId = '$orderId' ";
        $mysqli_result = $this->db->select($query);
        if ($mysqli_result) {
            $result = mysqli_fetch_all($this->db->select($query), MYSQLI_ASSOC)[0];
            return $result;
        }
        return false;
    }
//Tính tổng số lượng sản phẩm trong đơn hàng bằng cách cộng dồn các giá trị qty
    public function getTotalQtyByUserId($orderId)
    {
        $query = "SELECT SUM(qty) as total FROM order_details WHERE orderId = '$orderId' ";
        $mysqli_result = $this->db->select($query);
        if ($mysqli_result) {
            $result = mysqli_fetch_all($this->db->select($query), MYSQLI_ASSOC)[0];
            return $result;
        }
        return false;
    }
}
?>