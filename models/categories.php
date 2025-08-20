<?php 
$filepath = realpath(dirname(__FILE__));
include_once($filepath . '/../lib/database.php');
include_once($filepath . '/../lib/session.php');
?>

<?php 

class categories 
{
    private $db;// kết nối đến database
    public function __construct()
    {
         $this->db = new Database();
    }

    public function insert($name) // thêm danh mục
    {
         $query = "INSERT INTO categories VALUES (NULL,'" . $name . "',1) ";// Thêm danh mục mới
         $result = $this->db->insert($query);// kết quả truy vấn 
         if ($result) {
            $alert = "<span class='success'>Thêm danh mục thành công</span>";// thêm thành công
            return $alert;
         }
         else
         {
             $alert = "<span class='error'>Thêm danh mục thất bại</span>";// thất bại
             return $alert;
         }
    }

     public function getAllAdmin($page = 1, $total = 8)// Lấy tất cả danh mục cho admin, và phân trang
    {
        if ($page <= 0) {// Nếu trang nhỏ hơn hoặc bằng 0 thì gán bằng 1
            $page = 1;// Gán lại giá trị cho trang
        }
        $tmp = ($page - 1) * $total;// Tính toán vị trí bắt đầu
        $query = "SELECT * FROM categories limit $tmp,$total";// Lấy danh sách danh mục với phân trang
        $result = $this->db->select($query);// Kết quả truy vấn
        return $result;// trả về danh sách danh mục
    }

     public function getAll()// Lấy tất cả danh mục đang hoạt động
    {
        $query = "SELECT * FROM categories WHERE status = 1"; ///ấy toàn bộ danh mục có trạng thái đang hoạt động
        $result_mysqli = $this->db->select($query);// kết quả truy vấn
        if ($result_mysqli) {// Nếu có kết quả
             $result = mysqli_fetch_all($result_mysqli, MYSQLI_ASSOC);// Lấy tất cả danh mục
            return $result;// trả về danh sách danh mục
        }
        return false;// Nếu không có kết quả
    }

    
    public function getCountPaging($row = 8)//tổng số trang cần có để phân trang toàn bộ danh mục (categories)
    {
        $query = "SELECT COUNT(*) FROM categories";// Lấy tổng số lượng danh mục
        $mysqli_result = $this->db->select($query);// thực hiện truy vấn
         if ($mysqli_result) {
            $totalrow = intval((mysqli_fetch_all($mysqli_result, MYSQLI_ASSOC)[0])['COUNT(*)']);// Lấy tổng số lượng danh mục
            $result = ceil($totalrow / $row);// Tính toán tổng số trang
            return $result;// trả về tổng số trang
        }
        return false;// Nếu không có kết quả
    }

    public function update($data) // Cập nhật danh mục
{
    // Kiểm tra dữ liệu đầu vào
    if (!isset($data['id']) || !isset($data['name'])) {
        return "<span class='error'>Dữ liệu không hợp lệ</span>";
    }

    // Chuẩn bị câu lệnh UPDATE an toàn
    $stmt = $this->db->link->prepare("UPDATE categories SET name = ? WHERE id = ?");
    if (!$stmt) {
        return "<span class='error'>Lỗi chuẩn bị câu lệnh: ".$this->db->link->error."</span>";
    }

    // Gán giá trị vào câu lệnh
    $stmt->bind_param("si", $data['name'], $data['id']); // "s" = string, "i" = integer

    // Thực thi câu lệnh
    $result = $stmt->execute();

    // Kiểm tra kết quả
    if ($result) {
        return "<span class='success'>Cập nhật danh mục thành công</span>";
    } else {
        return "<span class='error'>Cập nhật danh mục thất bại: ".$stmt->error."</span>";
    }

    $stmt->close();
}


     public function delete($id)// xóa danh mục theo id
    {
        $query = "DELETE FROM categories WHERE id = '$id'";// câu lệnh xóa danh mục theo id
        $row = $this->db->delete($query);// thực hiện truy vấn
        if ($row) {
            return true;// trả về true nếu xóa thành cônga
        }
        return false;// Nếu không có kết quả
    }

    public function getByIdAdmin($id) //Lấy thông tin của một danh mục ADMIN
    {
        $query = "SELECT * FROM categories WHERE id = '$id'";// Lấy thông tin danh mục theo id
        $result = $this->db->select($query);// thực hiện truy vấn
        return $result;// trả về thông tin danh mục
    }

     public function getById($id)//Lấy thông tin danh mục theo id
    {
        $query = "SELECT * FROM categories WHERE id  = '$id' AND status = 1";// Lấy thông tin danh mục theo id và trạng thái đang hoạt động
         $mysqli_result = $this->db->select($query);// thực hiện truy vấn
         if ($mysqli_result) {
            $result = mysqli_fetch_all($this->db->select($query), MYSQLI_ASSOC)[0];// Lấy thông tin danh mục
            return $result;// trả về thông tin danh mục
         }
         return false;// Nếu không có kết quả
    }

    public function block($id)//"Chặn" hoặc "ẩn" một danh mục (
    {
        $query = "UPDATE categories SET status = 0 WHERE id = '$id'";// câu lệnh "chặn" hoặc "ẩn" một danh mục
        $result = $this->db->update($query);// thực hiện truy vấn
        if ($result) {
            return true;
        } else {
           return  false;
        }
    }

    
    public function active($id)// Kích hoạt (hiển thị lại) một danh mục
    {
        $query = "UPDATE categories SET status = 1 WHERE id = '$id'";// câu lệnh "kích hoạt" một danh mục
        $result = $this->db->update($query);// thực hiện truy vấn
         if ($result) {
            return true;
        } else {
           return  false;
        }
    }

}
?>