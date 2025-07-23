<?php
$filepath = realpath(dirname(__FILE__));
include_once($filepath . '/..lib/session.php');
include_once($filepath . '/..lib/database.php');
include_once($filepath . '/..lib/SMTP.php');
include_once($filepath . '/..lib/Exception.php');
use PHPMailer\PHPMailer\PHPMailer;
?>

<?php

class user 
{
    private $db;
    public  function __construct() 
    {
        $this->db = new Database();
    }
    //Người dùng đăng nhập
    public function login($email, $password)
    {
        // Nên dùng mysqli_real_escape_string để tránh SQL Injection
    $email = $this->db->link->real_escape_string($email);
    $password = $this->db->link->real_escape_string($password);

    $query = "SELECT * FROM users WHERE email = '$email' AND password = '$password'";
    $result = $this->db->select($query);

    if ($result && $result->num_rows > 0) {
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
    //Đăng kí tài khoản người dùng
    public function insert($data)
    {
        //Lấy dữ liệu từ mảng $data
    $fullName = $data['fullName'];
    $email = $data['email'];
    $dob = $data['dob'];
    $address = $data['address'];
    $password = md5($data['password']); //mã hóa mật khẩu bằng md5

    // Check email tồn tại hay chư 
    $check_email = "SELECT * FROM users WHERE email='$email' LIMIT 1";
    $result_check = $this->db->select($check_email);

    if ($result_check) {
        return 'Email đã tồn tại!';
    }

    // Mặc định: role_id = 2, status = 1, isConfirmed = 1
    $query = "INSERT INTO users (email, fullname, dob, password, role_id, status, address, isConfirmed) 
              VALUES ('$email', '$fullName', '$dob', '$password', 2, 1, '$address', 1)";
    $result = $this->db->insert($query);

    if ($result) {
        return true;
    } else {
        return 'Đăng ký thất bại. Vui lòng thử lại.';
    }
    }

    public function update($data)
	{
		$userId = Session::get('userId');///Lấy userID của người đang đăng nhập 
		$fullName = $data['fullName'];
		$email = $data['email'];
		$dob = $data['dob'];
		$address = $data['address'];
		$password = md5($data['password']);

		$query = "UPDATE users SET email = '$email', fullname = '$fullName', dob = '$dob', password = '$password', address = '$address' WHERE id = '$userId' ";
		$result = $this->db->update($query);
		return $result;
	}

    public function get() //lấy thông tin tài khoản người dùng đang đăng nhập, dựa vào userId
	{
		$userId = Session::get('userId');
		$query = "SELECT * FROM users WHERE id = '$userId' LIMIT 1";
		$mysqli_result = $this->db->select($query);
		if ($mysqli_result) {
			$result = mysqli_fetch_all($this->db->select($query), MYSQLI_ASSOC)[0];
			return $result;
		}
		return false;
	}

    public function getUserById($id) //lấy thông tin người dùng theo ID bất kỳ 
	{
		$query = "SELECT * FROM users where id = '$id'";
		$result = $this->db->select($query);
		return $result;
	}

    public function getAllAdmin($page = 1, $total = 8)//lấy ds người dùng(admin hoặc tất cả)
	{
		if ($page <= 0) {
			$page = 1;
		}
		$tmp = ($page - 1) * $total;
		$query =
			"SELECT users.*, role.name as roleName
			 FROM users INNER JOIN role ON users.role_id = role.id
             limit $tmp,$total";
		$result = $this->db->select($query);
		return $result;
	}   

    public function getAll() // lấy toàn bộ danh sách người dùng
	{
		$query =
			"SELECT users.*, role.name as cateName
			 FROM users INNER JOIN role ON users.role_id = role.id";
		$result = $this->db->select($query);
		return $result;
	}

    public function getCountPaging($row = 8)//mặc định 8 dòng mỗi trang 
	{
		$query = "SELECT COUNT(*) FROM users";
		$mysqli_result = $this->db->select($query);
		if ($mysqli_result) {
			$totalrow = intval((mysqli_fetch_all($mysqli_result, MYSQLI_ASSOC)[0])['COUNT(*)']);
			$result = ceil($totalrow / $row);
			return $result;
		}
		return false;
	}

    public function getUserByName($name_u) //tìm kiếm người dùng theo tên gần đúng
	{
		$query =
			"SELECT *
			 FROM users
			 WHERE fullname LIKE '%$name_u%'";
		$mysqli_result = $this->db->select($query);
		if ($mysqli_result) {
			$result = mysqli_fetch_all($mysqli_result, MYSQLI_ASSOC);
			return $result;
		}
		return false;
	}

    public function getLastUserId() //lấy ra thông tin của người dùng vừa được tạo gần nhất
	{
		$query = "SELECT * FROM users ORDER BY id DESC LIMIT 1";
		$mysqli_result = $this->db->select($query);
		if ($mysqli_result) {
			$result = mysqli_fetch_all($this->db->select($query), MYSQLI_ASSOC)[0];
			return $result;
		}
		return false;
	}

    public function block($id)
	{
		$query = "UPDATE users SET status = 0 where id = '$id'";
		$result = $this->db->delete($query);
		if ($result) {
			return true;
		} else {
			return false;
		}
	}

    public function delete($id)
	{
		$query = "DELETE FROM users WHERE id = $id";
		$row = $this->db->delete($query);
		if ($row) {
			return true;
		}
		return false;
	}

    // public function active($id)//kích hoạt lại tài khoản đã bị khóa
	// {
	// 	$query = "UPDATE users SET status = 1 where id = '$id'";
	// 	$result = $this->db->update($query);
	// 	if ($result) {
	// 		return true;
	// 	} else {
	// 		return false;
	// 	}
	// }

    public function updateStatus($id)
	{
		$query = "UPDATE users SET `status` = 1 where id = '$id' ";
		$result = $this->db->update($query);
		if ($result) {
			return true;
		} else {
			return false;
		}
	}

    public function getUserByOrder($orderId)//lấy thông tin người dùng (user) dựa trên mã đơn hàng
	{
		$query1 = "SELECT * FROM orders WHERE id = '$orderId'";
		$mysqli_result1 = $this->db->select($query1);
		$user = $mysqli_result1->fetch_assoc();
		$userId = $user['userId'];
        //Lấy thông tin người dùng từ bảng users
		$query = "SELECT * FROM users WHERE id = $userId LIMIT 1";
		$mysqli_result = $this->db->select($query);
		if ($mysqli_result) {
			$result = mysqli_fetch_all($this->db->select($query), MYSQLI_ASSOC)[0];
			return $result;
		}
		return false;
	}
    public function getPassword($email)
	{
        //kiểm tra xem email có tồn tại trong bảng users.
		$check_email = "SELECT * FROM users WHERE email='$email' LIMIT 1";
		$result_check = $this->db->select($check_email);
		if ($result_check) {
			 //Tạo mật khẩu mới gồm 5 chữ số và mã hoá bằng md5.
			$newPassword = rand(10000, 99999);
			$newPass = md5($newPassword);
			$query = "UPDATE `users` SET `password` = '$newPass' WHERE `email` = '$email'";
			$result = $this->db->update($query);
			if ($result) {
				//  Gửi email chứa mật khẩu mới về cho người dùng.
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
