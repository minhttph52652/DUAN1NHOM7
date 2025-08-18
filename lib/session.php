<?php
class Session// dùng để quản lí trang đăng nhập    
{
   public static function init()// Mục đích: Bắt đầu session nếu chưa có. Luôn phải chạy trước khi đọc/ghi dữ liệu vào $_SESSION.
   {
      if (session_status() === PHP_SESSION_NONE) {
         session_start();
     }
   }

   public static function set($key, $val)// Lưu thông tin vào session.
   {
      self::init();
      $_SESSION[$key] = $val;
   }

   public static function get($key)// Lấy giá trị đã lưu trong session.Nếu chưa tồn tại → trả về false.( kiểu như lưu $role_id)
   {
      self::init();
      if (isset($_SESSION[$key])) {
         return $_SESSION[$key];
      } else {
         return false;
      }
   }

   public static function checkSession($type)// Nếu chưa có user trong session → chuyển về trang login. Nếu $type là 'admin' thì chuyển đến ../login.php (đường dẫn khác).
   {
      self::init();
      if (self::get("user") == false) {
         if ($type == 'admin') {
            header("Location:../login.php");
         }
         header("Location:login.php");
      }
   }

   public static function checkLogin()//Nếu đã đăng nhập (user tồn tại) → đưa về index.php luôn, không cho ở trang login nữa. 
   {
      self::init();
      if (self::get("user") == true) {
         header("Location:index.php");
      }
   }

   public static function destroy()// xóa session khi user đăng xuất
   {
      self::init();
      session_destroy();
      header("Location:login.php");
   }
}
