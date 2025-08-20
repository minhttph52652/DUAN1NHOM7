<?php
$filepath = realpath(dirname(__FILE__));
include_once($filepath . '/../lib/session.php');
include_once($filepath . '/../lib/database.php');
// include_once($filepath . '/../lib/PHPMailer.php');
include_once($filepath . '/../lib/SMTP.php');
include_once($filepath . '/../lib/Exception.php');

use PHPMailer\PHPMailer\PHPMailer;
?>

<?php
/**
 * 
 */
class user
{
	private $db;
	public function __construct()
	{
		$this->db = new Database();
	}

	public function login($email, $password)
{
    // Nên dùng mysqli_real_escape_string để tránh SQL Injection
    $email = $this->db->link->real_escape_string($email);
    $password = $this->db->link->real_escape_string($password);

    $query = "SELECT * FROM users WHERE email = '$email' AND password = '$password'";// câu lệnh lấy thông tin người dùng
    $result = $this->db->select($query);// thực hiện truy vấn

    if ($result && $result->num_rows > 0) {// Kiểm tra kết quả truy vấn
        $value = $result->fetch_assoc();
        
        if ($value['status'] == 0 || $value['isConfirmed'] == 0) {
            return "Tài khoản bạn đang bị khóa hoặc chưa xác nhận. Vui lòng liên hệ ADMIN.";
        }

        // Lưu session
        Session::set('user', true);
        Session::set('userId', $value['id']);
        Session::set('role_id', $value['role_id']);

        return true; // Báo thành công cho login.php biết để chuyển trang
    } else {
		
        return "Email hoặc mật khẩu không đúng!";
    }
}


	public function insert($data)// Đăng ký người dùng
{
    $fullName = $data['fullName'];// Lấy tên đầy đủ
    $email = $data['email'];// Lấy email
    $dob = $data['dob'];// Lấy ngày sinh
    $address = $data['address'];// Lấy địa chỉ
    $password = md5($data['password']);// Mã hóa mật khẩu

    // Check email tồn tại
    $check_email = "SELECT * FROM users WHERE email='$email' LIMIT 1";// câu lệnh kiểm tra email tồn tại
    $result_check = $this->db->select($check_email);// thực hiện truy vấn

    if ($result_check) {// Kiểm tra kết quả truy vấn
        return 'Email đã tồn tại!';
    }

    // Mặc định: role_id = 2, status = 1, isConfirmed = 1
    $query = "INSERT INTO users (email, fullname, dob, password, role_id, status, address, isConfirmed) 
              VALUES ('$email', '$fullName', '$dob', '$password', 2, 1, '$address', 1)";// câu lệnh thêm mới người dùng
    $result = $this->db->insert($query);// thực hiện truy vấn

    if ($result) {
        return true;
    } else {
        return 'Đăng ký thất bại. Vui lòng thử lại.';
    }
}


	public function update($data)// Cập nhật thông tin người dùng
	{
		$userId = Session::get('userId');// Lấy ID người dùng từ session
		$fullName = $data['fullName'];// Lấy tên đầy đủ
		$email = $data['email'];// Lấy email
		$dob = $data['dob'];// Lấy ngày sinh
		$address = $data['address'];// Lấy địa chỉ
		$password = md5($data['password']);// Mã hóa mật khẩu

		$query = "UPDATE users SET email = '$email', fullname = '$fullName', dob = '$dob', password = '$password', address = '$address' WHERE id = '$userId' ";// câu lệnh cập nhật thông tin người dùng
		$result = $this->db->update($query);// thực hiện truy vấn
		return $result;
	}

	public function get()// Lấy thông tin người dùng
	{
		$userId = Session::get('userId');// Lấy ID người dùng từ session
		$query = "SELECT * FROM users WHERE id = '$userId' LIMIT 1";// câu lệnh lấy thông tin người dùng
		$mysqli_result = $this->db->select($query);// thực hiện truy vấn
		if ($mysqli_result) {
			$result = mysqli_fetch_all($this->db->select($query), MYSQLI_ASSOC)[0];// lấy thông tin người dùng
			return $result;
		}
		return false;
	}

	public function getUserById($id)// Lấy thông tin người dùng theo ID
	{
		$query = "SELECT * FROM users where id = '$id'";// câu lệnh lấy thông tin người dùng theo ID
		$result = $this->db->select($query);// thực hiện truy vấn
		return $result;
	}

	public function getAllAdmin($page = 1, $total = 8)// Lấy danh sách quản trị viên
	{
		if ($page <= 0) {
			$page = 1;
		}
		$tmp = ($page - 1) * $total;
		$query =
			"SELECT users.*, role.name as roleName
			 FROM users INNER JOIN role ON users.role_id = role.id
             limit $tmp,$total";// câu lệnh lấy danh sách quản trị viên
		$result = $this->db->select($query);// thực hiện truy vấn
		return $result;
	}

	public function getAll()// Lấy danh sách người dùng
	{
		$query =
			"SELECT users.*, role.name as cateName
			 FROM users INNER JOIN role ON users.role_id = role.id";// câu lệnh lấy danh sách người dùng
		$result = $this->db->select($query);// thực hiện truy vấn
		return $result;
	}

	public function getCountPaging($row = 8)// Lấy tổng số trang phân trang
	{
		$query = "SELECT COUNT(*) FROM users";// câu lệnh đếm số lượng người dùng
		$mysqli_result = $this->db->select($query);
		if ($mysqli_result) {
			$totalrow = intval((mysqli_fetch_all($mysqli_result, MYSQLI_ASSOC)[0])['COUNT(*)']);// lấy tổng số người dùng
			$result = ceil($totalrow / $row);// tính tổng số trang
			return $result;
		}
		return false;
	}

	public function getUserByName($name_u)// Lấy thông tin người dùng theo tên
	{
		$query =
			"SELECT *
			 FROM users
			 WHERE fullname LIKE '%$name_u%'";// câu lệnh lấy thông tin người dùng theo tên
		$mysqli_result = $this->db->select($query);
		if ($mysqli_result) {
			$result = mysqli_fetch_all($mysqli_result, MYSQLI_ASSOC);// lấy thông tin người dùng theo tên
			return $result;
		}
		return false;
	}

	public function getLastUserId()// Lấy thông tin người dùng theo ID
	{
		$query = "SELECT * FROM users ORDER BY id DESC LIMIT 1";// câu lệnh lấy thông tin người dùng theo ID
		$mysqli_result = $this->db->select($query);
		if ($mysqli_result) {
			$result = mysqli_fetch_all($this->db->select($query), MYSQLI_ASSOC)[0];// lấy thông tin người dùng theo ID
			return $result;
		}
		return false;
	}


	public function block($id)// Khóa người dùng
	{
		$query = "UPDATE users SET status = 0 where id = '$id'";// câu lệnh khóa người dùng
		$result = $this->db->delete($query);// thực hiện truy vấn
		if ($result) {
			return true;
		} else {
			return false;
		}
	}

	public function delete($id)// Xóa người dùng
	{
		$query = "DELETE FROM users WHERE id = $id";// câu lệnh xóa người dùng
		$row = $this->db->delete($query);// thực hiện truy vấn
		if ($row) {
			return true;
		}
		return false;
	}

	public function active($id)// Kích hoạt người dùng
	{
		$query = "UPDATE users SET status = 1 where id = '$id'";// câu lệnh kích hoạt người dùng
		$result = $this->db->delete($query);// thực hiện truy vấn
		if ($result) {
			return true;
		} else {
			return false;
		}
	}
	public function updateStatus($id)// Cập nhật trạng thái người dùng
	{
		$query = "UPDATE users SET `status` = 1 where id = '$id' ";// câu lệnh cập nhật trạng thái người dùng
		$result = $this->db->update($query);
		if ($result) {
			return true;
		} else {
			return false;
		}
	}

public function getUserByOrder($orderId) // Lấy thông tin người dùng theo đơn hàng
{
    // Lấy đơn hàng theo ID
    $query1 = "SELECT * FROM orders WHERE id = $orderId";
    $mysqli_result1 = $this->db->select($query1);

    if ($mysqli_result1 && mysqli_num_rows($mysqli_result1) > 0) {
        $order = mysqli_fetch_assoc($mysqli_result1); // Lấy thông tin đơn hàng
        $userId = $order['userId'];

        // Lấy thông tin người dùng
        $query = "SELECT * FROM users WHERE id = $userId LIMIT 1";
        $mysqli_result = $this->db->select($query);

        if ($mysqli_result && mysqli_num_rows($mysqli_result) > 0) {
            return mysqli_fetch_assoc($mysqli_result); // Trả về thông tin user
        }
    }

    return false; // Nếu không tìm thấy đơn hàng hoặc user
}


	public function getPassword($email)// Lấy lại mật khẩu người dùng
	{
		$check_email = "SELECT * FROM users WHERE email='$email' LIMIT 1";// câu lệnh kiểm tra email
		$result_check = $this->db->select($check_email);// thực hiện truy vấn
		if ($result_check) {
			// Genarate captcha
			$newPassword = rand(10000, 99999);// tạo mật khẩu mới
			$newPass = md5($newPassword);// mã hóa mật khẩu mới
			$query = "UPDATE `users` SET `password` = '$newPass' WHERE `email` = '$email'";// câu lệnh cập nhật mật khẩu
			$result = $this->db->update($query);
			if ($result) {
				// Send email

				// $mail = new PHPMailer();

				$mail = new PHPMailer();
				$mail->IsSMTP();
				$mail->Mailer = "smtp";

				$mail->SMTPDebug  = 0;
				$mail->SMTPAuth   = TRUE;
				$mail->SMTPSecure = "tls";
				$mail->Port       = 587;
				$mail->Host       = "smtp.gmail.com";
				$mail->Username   = "nguyenthdat7680@gmail.com";
				$mail->Password   = "udfhechjekxifivj";

				$mail->IsHTML(true);
				$mail->CharSet = 'UTF-8';
				$mail->AddAddress($email, "recipient-name");
				$mail->SetFrom("nguyenthdat7680@gmail.com", "HB-shop");
				$mail->Subject = "Lấy lại mật khẩu - HB-shop";
				$mail->Body = "<h3>Mật khẩu mới của bạn là: " . $newPassword . ". Bạn nên đổi lại mật khẩu dễ nhớ trong phần chỉnh sửa thông tin nhé >.<";

				$mail->Send();

				return true;
			} else {
				return false;
			}
		} else {
			return 'Email chưa tồn tại!';
		}
	}


}
?>