<?php 
$filepath = realpath(dirname(__FILE__));
include_once($filepath . '/../lib/database.php');
include_once($filepath . '/../lib/session.php');
?>

<?php 

class categories 
{
    private $db;
    public function __construct()
    {
         $this->db = new Database();
    }

    public function insert($name) //thêm danh mục 
    {
         $query = "INSERT INTO categories VALUES (NULL,'" . $name . "',1) ";
         $result = $this->db->insert($query);
         if ($result) {
            $alert = "<span class='success'>Thêm danh mục thành công</span>";
            return $alert;
         }
         else
         {
             $alert = "<span class='error'>Thêm danh mục thất bại</span>";
             return $alert;
         }
    }

     public function getAllAdmin($page = 1, $total = 8)
    {
        if ($page <= 0) {
            $page = 1;
        }
        $tmp = ($page - 1) * $total;
        $query = "SELECT * FROM categories limit $tmp,$total";
        $result = $this->db->select($query);
        return $result;
    }

     public function getAll()
    {
        $query = "SELECT * FROM categories WHERE status = 1"; ///ấy toàn bộ danh mục có trạng thái đang hoạt động
        $result_mysqli = $this->db->select($query);
        if ($result_mysqli) {
             $result = mysqli_fetch_all($result_mysqli, MYSQLI_ASSOC);
            return $result;
        }
        return false;
    }

    
    public function getCountPaging($row = 8)//tổng số trang cần có để phân trang toàn bộ danh mục (categories)
    {
        $query = "SELECT COUNT(*) FROM categories";
        $mysqli_result = $this->db->select($query);
         if ($mysqli_result) {
            $totalrow = intval((mysqli_fetch_all($mysqli_result, MYSQLI_ASSOC)[0])['COUNT(*)']);
            $result = ceil($totalrow / $row);
            return $result;
        }
        return false;
    }

     public function update($data)
    {
      $query = "UPDATE categories SET name = '".$data['name']."' WHERE id = '".$data['id']."'";
      $result = $this->db->update($query);
       if ($result) {
            $alert = "<span class='success'>Cập nhật danh mục thành công</span>";
            return $alert;
        } else {
            $alert = "<span class='error'>Cập nhật danh mục thất bại</span>";
            return $alert;
        }
    }

     public function delete($id)
    {
        $query = "DELETE FROM categories WHERE id = '$id'";
        $row = $this->db->delete($query);
        if ($row) {
            return true;
        }
        return false;
    }

    public function getByIdAdmin($id) //Lấy thông tin của một danh mục ADMIN
    {
        $query = "SELECT * FROM categories WHERE id = '$id'";
        $result = $this->db->select($query);
        return $result;
    }

     public function getById($id)//client
    {
        $query = "SELECT * FROM categories WHERE id  = '$id' AND status = 1";
         $mysqli_result = $this->db->select($query);
         if ($mysqli_result) {
            $result = mysqli_fetch_all($this->db->select($query), MYSQLI_ASSOC)[0];
            return $result;
         }
         return false;
    }

    public function block($id)//"Chặn" hoặc "ẩn" một danh mục (
    {
        $query = "UPDATE categories SET status = 0 WHERE id = '$id'";
        $result = $this->db->delete($query);
        if ($result) {
            return true;
        } else {
           return  false;
        }
    }

    
    public function active($id)// Kích hoạt (hiển thị lại) một danh mục
    {
        $query = "UPDATE categories SET status = 1 WHERE id = '$id'";
        $result = $this->db->delete($query);
         if ($result) {
            return true;
        } else {
           return  false;
        }
    }

}
?>