<?php
$filepath = realpath(dirname(__FILE__));
include_once($filepath . '/../lib/database.php');
include_once($filepath . '/../lib/session.php');
include_once($filepath . '/../models/product.php');
?> 


<?php

class cart
{
     private $db;// kết nối database
    public function __construct()
    {
         $this->db = new Database();
    }

    public function add($productId)// thêm sản phẩm vào giỏ hàng 
    {
        $userId = Session::get('userId');// lấy id của user từ session
        $query = "SELECT * FROM products WHERE id = '$productId' ";// lấy thông tin từ bảng products theo product id
        $result = $this->db->select($query)->fetch_assoc();// lấy thông tin sản phẩm
        $productName = $result["name"];// lưu tên
        $productPrice = $result["promotionPrice"];// lưu giá khuyến mãi
        $image = $result["image"];// lưu hình ảnh
        $checkcart = "SELECT qty FROM cart WHERE productId = '$productId' AND userId = '$userId' ";// kiểm tra sản phẩm này có trong giỏ hàng hay chưa
        $check_cart = $this->db->select($checkcart);// thực hiện truy vấn 
        if($check_cart)
        {
            $qtyInCart = mysqli_fetch_row($check_cart)[0];// nếu đã có thì lấy số lượng hiện tại trong giỏ hàng
            $product = new product();// tạo product để lấy sl tồn kho trong sp
            $productCheck = $product->getProductbyId($productId);//Nếu số lượng trong giỏ >= số lượng tồn kho → trả 'out of stock' (hết hàng).
            if (intval($qtyInCart) >= intval($productCheck['qty'])) {
                return 'out of stock';
            }
            
            $query_insert = "UPDATE cart SET qty = qty + 1 WHERE productId = $productId AND userId = '$userId' ";//Nếu vẫn còn hàng → tăng số lượng qty trong giỏ lên 1.
            $insert_cart = $this->db->update($query_insert);
            if ($insert_cart) {//Trả về kết quả thành công hay thất bại.
                return true;
            }
            else {
                return false;
            }
        } else {// nếu sp chưa có trong giỏ hàng
            $query_insert = "INSERT INTO cart VALUES(NULL, '$userId', '$productId', 1, '$productName', '$productPrice', '$image')";// Thêm bản ghi mới (qty = 1) với thông tin sản phẩm.
            $insert_cart = $this->db->insert($query_insert);
            if ($insert_cart) {
                return true;//Trả về true nếu thành công.
            } else {
                return false;//Trả về false nếu thất bại.
            }
        }
    }

    public function update($productId, $qty)// cập nhật số lượng 
    {
        $userId = Session::get('userId');//Lấy userId, kiểm tra tồn kho sản phẩm.
        $product = new product();
        $productCheck = $product->getProductbyId($productId);
        if (intval($qty) > intval($productCheck['qty'])) {// Nếu số lượng yêu cầu > số lượng tồn kho → trả 'out of stock'.
            return 'out of stock';
        }

        $query_insert = "UPDATE cart SET qty = $qty WHERE productId = $productId AND userId = $userId";//Nếu còn hàng → cập nhật số lượng trong giỏ hàng
        $insert_cart = $this->db->update($query_insert);
        if ($insert_cart) {
            return true;
        } else {
            return false;
        }
    }

    public function get() //Lấy giỏ hàng của user
    {
        $userId = Session::get('userId');// lấy userId, kiểm tra sản phẩm
        $query = "SELECT * FROM cart WHERE userId = '$userId' ";//Lấy tất cả sản phẩm trong giỏ của user.
        $mysqli_result = $this->db->select($query);
        if ($mysqli_result) {// Nếu có dữ liệu → trả về mảng chứa toàn bộ sản phẩm.
            $result = mysqli_fetch_all($mysqli_result, MYSQLI_ASSOC);
            return $result;
        }
        return false;// Nếu không → trả false.
    }

    public function delete($cartId) // xóa sản phẩm khỏi giỏ hàng
    {
        $userId = Session::get('userId');//lấy userId, kiểm tra sản phẩm
        $query = " DELETE FROM cart WHERE userId = '$userId' AND id = '$cartId' ";// xóa sp theo cartId và userId   
        $row = $this->db->delete($query);
        if ($row) {

            return true;// xóa thành công
        } else {
            return false;// xóa thất bại
        }
    }

     public function getTotalPriceByUserId() // tính tổng tiền giỏ hàng
        {$userId = Session::get('userId');//Lấy userId
            $query = "SELECT SUM(productPrice*qty) as total FROM cart WHERE userId = '$userId'";//Lấy tổng giá trị giỏ hàng của user
            $mysqli_result = $this->db->select($query);
            if ($mysqli_result) {
                $result = mysqli_fetch_all($mysqli_result, MYSQLI_ASSOC)[0];//Lấy tổng số lượng (qty) sản phẩm trong giỏ.
                return $result;// trả về số lượng
            }
            return false;// Nếu không → trả false.
        }

        public function getTotalQtyByUserId() // tính tổng số lượng sản phẩm trong giỏ hàng
        {
            $userId = Session::get('userId');//Lấy userId
            $query = "SELECT SUM(qty) as total FROM cart WHERE userId = '$userId'";//Lấy tổng số lượng sản phẩm trong giỏ hàng của user
            $mysqli_result = $this->db->select($query);
            if ($mysqli_result) {
                $result = mysqli_fetch_all($mysqli_result, MYSQLI_ASSOC)[0];//Lấy tổng số lượng (qty) sản phẩm trong giỏ.
                return $result;// trả về số lượng
            }
            return false;// Nếu không → trả false.
        }

        public function getListProductIdInCartByUserId() // lấy danh sách productId trong giỏ hàng của user
        {
            $userId = Session::get('userId');//Lấy userId
            $query = "SELECT productId FROM cart WHERE userId = '$userId'";//Lấy danh sách productId trong giỏ hàng của user
            $mysqli_result = $this->db->select($query);//Lấy kết quả truy vấn
            if ($mysqli_result) {//Nếu có kết quả
                $result = mysqli_fetch_all($mysqli_result, MYSQLI_ASSOC);//Lấy tất cả productId trong giỏ hàng của user
                return $result;// trả về danh sách productId
            }
            return false;// Nếu không → trả false.
        }
}
?>