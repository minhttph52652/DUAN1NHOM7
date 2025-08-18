<?php
// Lấy đường dẫn tuyệt đối của file hiện tại
$filepath = realpath(dirname(__FILE__));

// Include các file cần thiết
include_once($filepath . '/../lib/database.php');  // Kết nối Database
include_once($filepath . '/../lib/session.php');   // Quản lý Session
include_once($filepath . '/../models/cart.php');   // Lớp giỏ hàng
include_once($filepath . '/../models/product.php'); // Lớp sản phẩm
// include_once($filepath . '/../lib/PHPMailer.php'); // Thư viện PHPMailer
include_once($filepath . '/../lib/SMTP.php');      // Thư viện SMTP
include_once($filepath . '/../lib/Exception.php'); // Thư viện Exception

use PHPMailer\PHPMailer\PHPMailer; // Sử dụng PHPMailer namespace

class order
{
    private $db; // Biến lưu đối tượng Database

    // Hàm khởi tạo class
    public function __construct()
    {
        $this->db = new Database(); // Khởi tạo kết nối Database
    }

    // Hàm thêm đơn hàng
    public function add($data)
    {
        $fullName = $data['fullName'];         // Lấy tên khách hàng từ dữ liệu
        $phoneNumber = $data['phoneNumber'];   // Lấy số điện thoại từ dữ liệu
        $address = $data['address'];           // Lấy địa chỉ từ dữ liệu
        $userId = Session::get('userId');      // Lấy userId từ Session

        // Thêm đơn hàng vào bảng orders
        $sql_insert_cart = "INSERT INTO orders VALUES (NULL, '$userId', '" . date('y/m/d') . "',NULL, 'Processing', '$fullName', '$phoneNumber', '$address')";
        $insert_cart = $this->db->insert($sql_insert_cart); // Thực hiện truy vấn insert
        if (!$insert_cart) return false; // Nếu thêm thất bại, trả về false

        // Lấy danh sách sản phẩm trong giỏ hàng
        $cart = new cart();      // Tạo đối tượng cart
        $cart_user = $cart->get();// Lấy danh sách sản phẩm trong giỏ hàng của người dùng

        // Lấy ID của đơn hàng vừa thêm (ID lớn nhất)
        $sql_get_cart_last_id = "SELECT id FROM orders ORDER BY id DESC LIMIT 1";
        $get_cart_last_id = $this->db->select($sql_get_cart_last_id); // Thực hiện truy vấn select
        if ($get_cart_last_id) {
            $orderId = mysqli_fetch_row($get_cart_last_id)[0]; // Lấy ID đơn hàng mới nhất
        }

        $product = new product(); // Tạo đối tượng product

        // Duyệt từng sản phẩm trong giỏ hàng
        foreach ($cart_user as $key => $value) {
            // Thêm chi tiết sản phẩm vào bảng order_details
            $sql_insert_order_details = "INSERT INTO order_details VALUES(NULL,'$orderId'," . $value['productId'] . "," . $value['qty'] . "," . $value['productPrice'] . ",'" . $value['productName'] . "','" . $value['productImage'] . "')";
            $insert_order_details = $this->db->insert($sql_insert_order_details); // Thực hiện insert chi tiết
            if (!$insert_order_details) return false; // Nếu thêm chi tiết thất bại, trả về false

            // Cập nhật số lượng sản phẩm trong kho
            $product->updateQty($value['productId'], $value['qty']); // Trừ số lượng đã bán
        }

        // Xóa giỏ hàng của người dùng sau khi đặt xong
        $sql_delete_cart = "DELETE FROM cart WHERE userId = $userId";
        $delete_cart = $this->db->delete($sql_delete_cart); // Thực hiện delete
        if ($delete_cart) return true; // Xóa thành công, trả về true

        return false; // Nếu xóa thất bại, trả về false
    }

    // Cập nhật ngày nhận hàng dự kiến
    public function updateReceivedDateOrder($id)
    {
        $query = "UPDATE orders SET receivedDate = '" . date('y/m/d', strtotime('+3 days')) . "' WHERE id = $id";
        $mysqli_result = $this->db->update($query); // Thực hiện update
        if ($mysqli_result) return true; // Nếu update thành công
        return false; // Nếu update thất bại
    }

    // Lấy đơn hàng của người dùng
    public function getOrderByUser()
    {
        $userId = Session::get('userId'); // Lấy userId từ session
        $query = "SELECT * FROM orders WHERE userId = '$userId'";
        $mysqli_result = $this->db->select($query); // Thực hiện truy vấn
        if ($mysqli_result) {
            return mysqli_fetch_all($mysqli_result, MYSQLI_ASSOC); // Trả về tất cả kết quả
        }
        return false; // Nếu không tìm thấy
    }

    // Lấy đơn hàng theo ID
    public function getById($id)
    {
        $query = "SELECT * FROM orders WHERE id = '$id'";
        $mysqli_result = $this->db->select($query);
        if ($mysqli_result) {
            return mysqli_fetch_all($mysqli_result, MYSQLI_ASSOC)[0]; // Trả về đơn hàng đầu tiên
        }
        return false;
    }

    // Lấy đơn hàng theo trạng thái Processing
    public function getProcessingOrder()
    {
        $query = "SELECT * FROM orders WHERE status = 'Processing'";
        $mysqli_result = $this->db->select($query);
        if ($mysqli_result) return mysqli_fetch_all($mysqli_result, MYSQLI_ASSOC);
        return false;
    }

    // Lấy đơn hàng theo trạng thái Processed
    public function getProcessedOrder()
    {
        $query = "SELECT * FROM orders WHERE status = 'Processed'";
        $mysqli_result = $this->db->select($query);
        if ($mysqli_result) return mysqli_fetch_all($mysqli_result, MYSQLI_ASSOC);
        return false;
    }

    // Lấy đơn hàng theo trạng thái Delivering
    public function getDeliveringOrder()
    {
        $query = "SELECT * FROM orders WHERE status = 'Delivering'";
        $mysqli_result = $this->db->select($query);
        if ($mysqli_result) return mysqli_fetch_all($mysqli_result, MYSQLI_ASSOC);
        return false;
    }

    // Lấy đơn hàng hoàn thành
    public function getCompleteOrder()
    {
        $query = "SELECT * FROM orders WHERE status = 'Complete' OR status = 'Delivered'";
        $mysqli_result = $this->db->select($query);
        if ($mysqli_result) return mysqli_fetch_all($mysqli_result, MYSQLI_ASSOC);
        return false;
    }

    // Lấy đơn hàng đã hủy
    public function getCancelOrder()
    {
        $query = "SELECT * FROM orders WHERE status = 'Cancel' OR status = 'Spam'";
        $mysqli_result = $this->db->select($query);
        if ($mysqli_result) return mysqli_fetch_all($mysqli_result, MYSQLI_ASSOC);
        return false;
    }

    // Cập nhật trạng thái đơn hàng thành Processed
    public function processedOrder($id)
    {
        $query = "UPDATE orders SET status = 'Processed' WHERE id = $id";
        $mysqli_result = $this->db->update($query);
        if ($mysqli_result) {
            if ($this->updateReceivedDateOrder($id)) return true;
        }
        return false;
    }

    // Đánh dấu đơn hàng là Spam
    public function spamOrder($id)
    {
        $query = "SELECT userId FROM orders WHERE id = $id";
        $mysqli_result = $this->db->select($query);
        if ($mysqli_result) {
            $row = $mysqli_result->fetch_assoc(); // Lấy thông tin userId
            $userId = $row['userId'];

            // Cập nhật trạng thái đơn hàng thành Spam
            $this->db->update("UPDATE orders SET status = 'Spam' WHERE id = $id");

            // Cập nhật trạng thái người dùng thành 2 (spam)
            $this->db->update("UPDATE users SET status = 2 WHERE id = $userId");

            // Cập nhật ngày nhận hàng dự kiến
            $targetDate = date('Y-m-d', strtotime('+10 days'));
            $this->db->update("UPDATE orders SET receivedDate = '$targetDate' WHERE id = $id");

            return true;
        }
        return false;
    }

    // Cập nhật trạng thái đơn hàng thành Cancel
    public function cancelOrder($id)
    {
        $query = "UPDATE orders SET status = 'Cancel' WHERE id = $id";
        $mysqli_result = $this->db->update($query);
        if ($mysqli_result) return true;
        return false;
    }

    // Cập nhật trạng thái đơn hàng thành Delivering
    public function deliveringOrder($id)
    {
        $query = "UPDATE orders SET status = 'Delivering' WHERE id = $id";
        $mysqli_result = $this->db->update($query);
        if ($mysqli_result) return true;
        return false;
    }

    // Cập nhật trạng thái đơn hàng thành Complete
    public function completeOrder($id)
    {
        $query = "UPDATE orders SET status = 'Complete', receivedDate = '" . date('y/m/d') . "' WHERE id = $id";
        $mysqli_result = $this->db->update($query);
        if ($mysqli_result) return true;
        return false;
    }

    // Cập nhật trạng thái đơn hàng thành Delivered
    public function deliveredOrder($id)
    {
        $query = "UPDATE orders SET status = 'Delivered' WHERE id = $id";
        $mysqli_result = $this->db->update($query);
        if ($mysqli_result) return true;
        return false;
    }

    // Xóa đơn hàng
    public function deleteOrder($id)
    {
        $query = "DELETE FROM order_details WHERE orderId = $id"; // Xóa chi tiết đơn hàng
        $mysqli_result = $this->db->delete($query);
        if ($mysqli_result) {
            $query1 = "DELETE FROM orders WHERE id = $id"; // Xóa đơn hàng
            $mysqli_result1 = $this->db->delete($query1);
            if ($mysqli_result1) return true;
            return false;
        }
        return false;
    }

    // Lấy chi tiết đơn hàng
    public function get($orderId)
    {
        $query = "SELECT * FROM order_details WHERE orderId = '$orderId'";
        $mysqli_result = $this->db->select($query);
        if ($mysqli_result) return mysqli_fetch_all($mysqli_result, MYSQLI_ASSOC);
        return false;
    }

    // Hàm hủy tất cả đơn hàng spam của 1 user
    public function toOrderCancel($userId)
    {
        $query = "UPDATE orders SET status = 'Cancel' WHERE userId = $userId AND status = 'Spam'";
        $mysqli_result = $this->db->update($query);
        if ($mysqli_result) return true;
        return false;
    }

    // Gửi email khi đơn hàng đã giao
//     public function messCompleted($userId)
//     {
//         $query = "SELECT * FROM users WHERE id = $userId LIMIT 1";
//         $result = $this->db->select($query);
//         $email_check = $result->fetch_assoc();
//         $email = $email_check['email'];

//         if ($email) {
//             $mail = new PHPMailer();
//             $mail->IsSMTP(); // Sử dụng SMTP
//             $mail->Mailer = "smtp";

//             $mail->SMTPDebug  = 0; // Debug off
//             $mail->SMTPAuth   = TRUE; // Xác thực SMTP
//             $mail->SMTPSecure = "tls"; // Bảo mật TLS
//             $mail->Port       = 587; // Cổng SMTP
//             $mail->Host       = "smtp.gmail.com"; // Host SMTP
//             $mail->Username   = "buihuyhai@gmail.com"; // Email gửi
//             $mail->Password   = "udfhechjekxifivj";   // Mật khẩu ứng dụng

//             $mail->IsHTML(true);
//             $mail->CharSet = 'UTF-8';
//             $mail->AddAddress($email, "recipient-name");
//             $mail->SetFrom("buihuyhai@gmail.com", "HB-shop");
//             $mail->Subject = "Giao hàng thành công - HB-SHOP";
//             $mail->Body = "<h3>Đơn hàng đã được giao thành công! <br> Nếu bạn chưa nhận được hàng xin liên hệ chúng tôi sớm nhất để được xử lý! <br> Cảm ơn bạn đã mua hàng tại HB-Shop >.<";

//             $mail->Send(); // Gửi email

//             return true;
//         } else {
//             return false;
//         }
//     }
}
?>
