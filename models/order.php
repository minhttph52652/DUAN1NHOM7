<?php
$filepath = realpath(dirname(__FILE__));
include_once($filepath . '/../lib/database.php');
include_once($filepath . '/../lib/session.php');
include_once($filepath . '/../models/cart.php');
include_once($filepath . '/../models/product.php');
include_once($filepath . '/../lib/PHPMailer.php');
include_once($filepath . '/../lib/SMTP.php');
include_once($filepath . '/../lib/Exception.php');

use PHPMailer\PHPMailer\PHPMailer;
?>

<?php

class order
{
    private $db;// kết nối đến database
    public function __construct()
    {
        $this->db = new Database();
    }

    public function add($data)// thêm đơn hàng
    {
        $fullName = $data['fullName'];// tên
        $phoneNumber = $data['phoneNumber'];// số điện thoại
        $address = $data['address'];// địa chỉ
        $userId = Session::get('userId');// ID người dùng lấy bằng Session

        //Thêm đơn hàng vào bảng orders
        $sql_insert_cart = "INSERT INTO orders VALUES (NULL, '$userId', '" . date('y/m/d') . "',NULL, 'Processing', '$fullName', '$phoneNumber', '$address')";
        $insert_cart = $this->db->insert($sql_insert_cart);// thực hiện truy vấn
        if (!$insert_cart) {// nếu thêm đơn hàng thất bại
            return false;// trả về false
        }
        
           // Lấy danh sách sản phẩm trong giỏ hàng của người dùng
           $cart = new cart();// tạo đối tượng giỏ hàng
           $cart_user = $cart->get();// lấy danh sách sản phẩm trong giỏ hàng bằng get
           // Lấy ID của đơn hàng vừa được thêm (dựa vào ID lớn nhất)
           $sql_get_cart_last_id = "SELECT id FROM orders ORDER BY id DESC LIMIT 1";// câu lệnh lấy ID đơn hàng mới nhất
           $get_cart_last_id = $this->db->select($sql_get_cart_last_id);// thực hiện truy vấn
           if ($get_cart_last_id) {// nếu có kết quả
            $orderId = mysqli_fetch_row($get_cart_last_id)[0];// Lấy ID đơn hàng mới nhất
           }
            // Tạo đối tượng sản phẩm để cập nhật số lượng
            $product = new product();
            // Duyệt qua từng sản phẩm trong giỏ hàng
            foreach ($cart_user as $key => $value) {
                // Thêm thông tin từng sản phẩm vào bảng order_details
                $sql_insert_order_details = "INSERT INTO order_details  VALUES(NULL,'$orderId'," . $value['productId'] . "," . $value['qty'] . "," . $value['productPrice'] . ",'" . $value['productName'] . "','" . $value['productImage'] . "')";
                $insert_order_details = $this->db->insert($sql_insert_order_details);// thực hiện truy vấn
                if (!$insert_order_details) {// ko lấy đc thông tin sản phẩm
                    return false;// trả về false
                }   
                // Cập nhật số lượng sản phẩm trong kho
                $product->updateQty($value['productId'], $value['qty']);// thực hiện cập nhật số lượng sản phẩm
                if (!$product) {// ko cập nhật đc thì
                    return false;// trả về false
                }
            }
                // Xóa toàn bộ sản phẩm trong giỏ hàng của người dùng sau khi đặt hàng
                $sql_delete_cart = "DELETE FROM cart WHERE userId = $userId";// câu lệnh xóa sản phẩm trong giỏ hàng
                $delete_cart = $this->db->delete($sql_delete_cart);// thực hiện câu lệnh xóa
                if ($delete_cart) {
                    return true;// xóa thành công
                }
                return false;// xóa thất bại
            }

            public function updateReceivedDateOrder($id)// cập nhật ngày nhận hàng
            {
                $query = "UPDATE orders SET receivedDate = '" . Date('y/m/d', strtotime('+3 days')) . "' WHERE id = $id";// câu lệnh cập nhật ngày nhận hàng
                $mysqli_result = $this->db->update($query);// thực hiện câu lệnh và lưu trong db 
                if ($mysqli_result) {
                    return true;// cập nhật thành công
                } 
                return false;// cập nhật thất bại
            }

            public function getOrderByUser() {// lấy đơn hàng theo người dùng
                $userId = Session::get('userId');// lấy ID người dùng từ session
                $query = "SELECT * FROM orders WHERE userId = '$userId'";// câu lệnh lấy đơn hàng theo người dùng
                $mysqli_result = $this->db->select($query);// thực hiện truy vấn
                if ($mysqli_result) {
                    $result = mysqli_fetch_all($this->db->select($query), MYSQLI_ASSOC);// lấy tất cả kết quả
                    return $result;// trả về kết quả
                }
                return false;// không tìm thấy đơn hàng
            }

            public function getById($id) // lấy đơn hàng theo ID
            {
                $query = "SELECT * FROM orders WHERE id = '$id' ";// câu lệnh lấy đơn hàng theo ID
                $mysqli_result = $this->db->select($query);// thực hiện truy vấn
                if ($mysqli_result) {
                    $result = mysqli_fetch_all($this->db->select($query), MYSQLI_ASSOC)[0];// lấy kết quả đầu tiên
                    return $result;// trả về kết quả
                }
                return false;// không tìm thấy đơn hàng
            }

            public function getProcessingOrder()// lấy đơn hàng đang xử lý
            {
                $query = "SELECT * FROM orders WHERE status = 'Processing'";// câu lệnh lấy đơn hàng đang xử lý
                $mysqli_result = $this->db->select($query);// thực hiện truy vấn
                if ($mysqli_result) {
                    $result = mysqli_fetch_all($this->db->select($query), MYSQLI_ASSOC);// lấy tất cả kết quả
                    return $result;// trả về kết quả
                }
                return false;// không tìm thấy đơn hàng
            }

            public function getProcessedOrder() // lấy đơn hàng đã xử lý
            {
                $query = "SELECT * FROM orders WHERE status = 'Processed'";// câu lệnh lấy đơn hàng đã xử lý
                $mysqli_result = $this->db->select($query);// thực hiện truy vấn
                if ($mysqli_result) {
                    $result = mysqli_fetch_all($this->db->select($query), MYSQLI_ASSOC);// lấy tất cả kết quả
                    return $result;// trả về kết quả
                }
                return false;// không tìm thấy đơn hàng
            }

            public function getDeliveringOrder() // lấy đơn hàng đang giao
            {
                $query = "SELECT * FROM orders WHERE status = 'Delivering'";// câu lệnh lấy đơn hàng đang giao
                $mysqli_result = $this->db->select($query);// thực hiện truy vấn
                if ($mysqli_result) {
                    $result = mysqli_fetch_all($this->db->select($query), MYSQLI_ASSOC);// lấy tất cả kết quả
                    return $result;// trả về kết quả
                }
                return false;// không tìm thấy đơn hàng
            }

            public function getCompleteOrder() // lấy đơn hàng đã hoàn thành
            {
                $query = "SELECT * FROM orders WHERE status = 'Complete' OR status = 'Delivered'";// câu lệnh lấy đơn hàng đã hoàn thành
                $mysqli_result = $this->db->select($query);// thực hiện truy vấn
                if ($mysqli_result) {
                    $result = mysqli_fetch_all($this->db->select($query), MYSQLI_ASSOC);// lấy tất cả kết quả
                    return $result;// trả về kết quả    
                }
                return false;// không tìm thấy đơn hàng     
            }

            public function getCancelOrder()// lấy đơn hàng đã hủy
            {
                $query = "SELECT * FROM orders WHERE status = 'Cancel' OR status = 'Spam'";// câu lệnh lấy đơn hàng đã hủy
                $mysqli_result = $this->db->select($query);// thực hiện truy vấn
                if ($mysqli_result) {
                    $result = mysqli_fetch_all($this->db->select($query), MYSQLI_ASSOC);// lấy tất cả kết quả
                    return $result;// trả về kết quả
                }
                return false;// không tìm thấy đơn hàng
            }

            public function processedOrder($id)// lấy đơn hàng đã xử lý
            {
                $query = "UPDATE orders SET status = 'Processed' WHERE id = $id";// câu lệnh cập nhật đơn hàng đã xử lý
                $mysqli_result = $this->db->update($query);// thực hiện truy vấn
                if ($mysqli_result) {
                    if ($this->updateReceivedDateOrder($id)) {// cập nhật ngày nhận hàng
                        return true;//thành công
                    }
                }
                return false;// thất bại
            }

            public function spamOrder($id) {// lấy đơn hàng spam
                $query = "SELECT userId FROM orders WHERE id = $id";// câu lệnh lấy userId từ đơn hàng
                $mysqli_result = $this->db->select($query);// thực hiện truy vấn
                if ($mysqli_result)
                {
                    $row = $mysqli_result->fetch_assoc();// lấy kết quả
                    $userId = $row['userId'];// lấy userId từ đơn hàng
                    $query1 = "UPDATE orders SET status = 'Spam' WHERE id = $id";// câu lệnh cập nhật trạng thái đơn hàng thành spam
                    $mysqli_result1 = $this->db->update($query1);// thực hiện truy vấn

                    $query2 = "UPDATE users SET status = 2 WHERE id = $userId";// câu lệnh cập nhật trạng thái người dùng thành spam
                    $mysqli_result2 = $this->db->update($query2);// thực hiện truy vấn

                    if ($mysqli_result1 && $mysqli_result2) {// kiểm tra kết quả truy vấn
                        $targetDate = date('Y-m-d', strtotime('+10 days'));// ngày nhận hàng dự kiến
                        $query3 = "UPDATE orders SET receivedDate = '$targetDate' WHERE id = $id";// câu lệnh cập nhật ngày nhận hàng
                        $mysqli_result3  = $this->db->update($query3);// thực hiện truy vấn
                        return true;// trả về true nếu cập nhật thành công
                    }
                    return false;// trả về false nếu cập nhật thất bại
                }
                return false;// không tìm thấy đơn hàng
            }

            public function cancelOrder($id)// lấy đơn hàng đã hủy
            {
                $query = "UPDATE orders SET status = 'Cancel' WHERE id = $id";// câu lệnh cập nhật trạng thái đơn hàng thành hủy
                $mysqli_result = $this->db->update($query);// thực hiện truy vấn
                if ($mysqli_result) {
                    return true;// trả về true nếu cập nhật thành công
                }
                return false;// trả về false nếu cập nhật thất bại
            }


            public function deliveringOrder($id)// lấy đơn hàng đang giao
            {
                $query = "UPDATE orders SET status = 'Delivering' WHERE id = $id";// câu lệnh cập nhật trạng thái đơn hàng thành đang giao
                $mysqli_result = $this->db->update($query);// thực hiện truy vấn
                if ($mysqli_result) {
                    return true;// trả về true nếu cập nhật thành công
                }
                return false;// trả về false nếu cập nhật thất bại
            }

            public function completeOrder($id)// lấy đơn hàng đã hoàn thành
            {
                $query = "UPDATE orders SET status = 'Complete', receivedDate = '" . Date('y/m/d') . "' WHERE id = $id";// câu lệnh cập nhật trạng thái đơn hàng thành hoàn thành
                $mysqli_result = $this->db->update($query);// thực hiện truy vấn
                if ($mysqli_result) {
                    return true;// trả về true nếu cập nhật thành công
                }
                return false;// trả về false nếu cập nhật thất bại
            }

            public function deliveredOrder($id)// lấy đơn hàng đã giao
            {
                $query = "UPDATE orders SET status = 'Delivered' WHERE id = $id";// câu lệnh cập nhật trạng thái đơn hàng thành đã giao
                $mysqli_result = $this->db->update($query);// thực hiện truy vấn
                if ($mysqli_result) 
                {
                    return true;// trả về true nếu cập nhật thành công
                }
                return false;// trả về false nếu cập nhật thất bại
            }

            public function  deleteOrder($id)// lấy đơn hàng đã xóa
            {
                $query = "DELETE FROM order_details WHERE orderId = $id";// câu lệnh xóa chi tiết đơn hàng
                $mysqli_result = $this->db->delete($query);// thực hiện truy vấn
                if ($mysqli_result) {// nếu xóa chi tiết đơn hàng thành công
                    $query1 = "DELETE FROM orders WHERE id = $id";// câu lệnh xóa đơn hàng
                    $mysqli_result1 = $this->db->delete($query1);// thực hiện truy vấn
                    if ($mysqli_result1) {
                        return true;// trả về true nếu xóa đơn hàng thành công
                    }
                    return false;// trả về false nếu xóa đơn hàng thất bại
                }
                return false;// trả về false nếu xóa chi tiết đơn hàng thất bại
            }

    public function getDate($id) // lấy ngày nhận hàng
{
    $query = "SELECT `receivedDate` FROM `orders` WHERE `userId` = $id AND `status` = 'Spam' LIMIT 1"; // câu lệnh lấy ngày nhận hàng
    $mysqli_result = $this->db->select($query); // thực hiện truy vấn
    if ($mysqli_result) {
        return $mysqli_result; // trả về kết quả nếu truy vấn thành công
    }
    return false; // trả về false nếu không có kết quả
}

         public  function toOrderCancel($id)// lấy đơn hàng đã hủy
         {
            $query = "UPDATE orders SET status = 'Cancel' WHERE userId = $id AND status = 'Spam'";// câu lệnh hủy đơn hàng
            $mysqli_result = $this->db->update($query);// thực hiện truy vấn
            if ($mysqli_result) {
                return true;// trả về true nếu hủy đơn hàng thành công
            }
            return false;// trả về false nếu hủy đơn hàng thất bại
         }

         public function  messCompleted($userId)// lấy thông tin người dùng
         {
            $query = "SELECT * FROM users WHERE id = $userId LIMIT 1";// câu lệnh lấy thông tin người dùng
            $result = $this->db->select($query);// thực hiện truy vấn
            $email_check = $result->fetch_assoc();// lấy thông tin email
            $email = $email_check['email'];
            if ($email) {
            $mail = new PHPMailer();
            $mail->IsSMTP();
            $mail->Mailer = "smtp";

            $mail->SMTPDebug  = 0;
            $mail->SMTPAuth   = TRUE;
            $mail->SMTPSecure = "tls";
            $mail->Port       = 587;
            $mail->Host       = "smtp.gmail.com";
            $mail->Username   = "buihuyhai@gmail.com";
            $mail->Password   = "udfhechjekxifivj";

            $mail->IsHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->AddAddress($email, "recipient-name");
            $mail->SetFrom("buihuyhai@gmail@gmail.com", "HB-shop");
            $mail->Subject = "Giao hàng thành công - HB-SHOP";
            $mail->Body = "<h3>Đơn hàng đã được giao thành công! <br> Nếu bạn chưa nhận được hàng xin liên hệ chúng tôi sớm nhất để được xử lý! <br> Cảm ơn bạn đã mua hàng tại HB-Shop >.<";

            $mail->Send();

			return true;
            }
            else {
                return false;
            }
         }

         public function get($orderId)// lấy chi tiết đơn hàng
         {
            $query = "SELECT * FROM order_details WHERE orderId = '$orderId'";// câu lệnh lấy chi tiết đơn hàng
            $mysqli_result = $this->db->select($query);// thực hiện truy vấn
            if ($mysqli_result) 
            {
                $result = mysqli_fetch_all($mysqli_result, MYSQLI_ASSOC);// lấy tất cả chi tiết đơn hàng
                return $result;// trả về kết quả nếu truy vấn thành công
            }
            return false;// trả về false nếu truy vấn thất bại
         }
        }

?>