<?php
$filepath = realpath(dirname(__FILE__));
include_once($filepath . '/../lib/database.php');
include_once($filepath . '/../lib/session.php');
?>

<?php

 class product 
 {
    private $db;
    public function __construct()
    {
        $this->db = new Database();
    }

    public function insert($data) //Thêm sản phẩm 
    {
        $name = $data['name'];
        $originalPrice = $data['originalPrice'];
        $promotionPrice = $data['promotionPrice'];
        $cateId = $data['cateId'];
        $des = $data['des'];
        $qty = $data['qty'];

        // Xử lý hình ảnh tải lên 
        $file_name = $_FILES['image']['name'];
        $file_temp = $_FILES['image']['tmp_name'];

        $div = explode('.', $file_name);
        $file_ext = strtolower(end($div));
        $unique_image = substr(md5(time()), 0, 10) . '.' . $file_ext;
        $uploaded_image = "uploads/" . $unique_image;

        move_uploaded_file($file_temp, $uploaded_image);
        $query = "INSERT INTO products VALUES (NULL,'$name','$originalPrice','$promotionPrice','$unique_image'," . Session::get('userId') . ",'" . date('Y/m/d') . "','$cateId','$qty','$des',1,0) ";
        $result = $this->db->insert($query);
        if ($result) {
            $alert = "<span class='success'>Sản phẩm đã được thêm thành công</span>";
            return $alert;
        } else {
            $alert = "<span class='error'>Thêm sản phẩm thất bại</span>";
            return $alert;
        }
    }

    public function getAllAdmin($page = 1, $total = 8)// lấy danh sách sản phẩm
    {
        if ($page <= 0) {
            $page = 1;
        }
        $tmp = ($page - 1) * $total;
        $query =
            "SELECT products.*, categories.name as cateName, users.fullName
			 FROM products INNER JOIN categories ON products.cateId = categories.id INNER JOIN users ON products.createdBy = users.id
			 order by products.id desc 
             limit $tmp,$total";
        $result = $this->db->select($query);
        return $result;
    }

    public function getAll()
    {
        $query =
            "SELECT products.*, categories.name as cateName
			 FROM products INNER JOIN categories ON products.cateId = categories.id INNER JOIN users ON products.createdBy = users.id
			 WHERE products.status = 1
             order by products.id desc ";
        $result = $this->db->select($query);
        return $result;
    }

    public function getCountPaging($row = 8)
    {
        $query = "SELECT COUNT(*) FROM products";
        $mysqli_result = $this->db->select($query);
        if ($mysqli_result) {
            $totalrow = intval((mysqli_fetch_all($mysqli_result, MYSQLI_ASSOC)[0])['COUNT(*)']);
            $result = ceil($totalrow / $row);//celi làm tròn
            return $result;
        }
        return false;
    }

     public function getCountPagingClient($cateId, $row = 8)//Tính số trang cần thiết để hiển thị sản phẩm thuộc một danh mục cụ thể (dựa trên $cateId)
    {
        $query = "SELECT COUNT(*) FROM products WHERE cateId = $cateId";
        $mysqli_result = $this->db->select($query);
        if ($mysqli_result) {
            $totalrow = intval((mysqli_fetch_all($mysqli_result, MYSQLI_ASSOC)[0])['COUNT(*)']);
            $result = ceil($totalrow / $row);
            return $result;
        }
        return false;
    }

     public function getFeaturedProducts()//lấy danh sách các sản phẩm nổi bật (hiển thị ở trang chủ hoặc trang nổi bật)
    {
        $query =
            "SELECT *
			 FROM products
			 WHERE products.status = 1
             order by products.soldCount DESC";//sx sl đã bán từ cao-thấp
        $result = $this->db->select($query);
        return $result;
    }

     public function getProductByName($name_product)//tìm kiếm sản phẩm theo tên
    {
        $query =
            "SELECT products.*, categories.name as cateName, users.fullName
            FROM products INNER JOIN categories ON products.cateId = categories.id INNER JOIN users ON products.createdBy = users.id
			 WHERE products.name LIKE '%$name_product%' and products.status = 1
            order by products.id desc";
        $mysqli_result = $this->db->select($query);
        if ($mysqli_result) {
            $result = mysqli_fetch_all($mysqli_result, MYSQLI_ASSOC);
            return $result;
        }
        return false;
    }

     public function getProductsByCateId($page, $cateId, $total = 8)
    {
        if ($page <= 0) {
            $page = 1;
        }
        $tmp = ($page - 1) * $total;
        $query =
            "SELECT *
			 FROM products
			 WHERE status = 1 AND cateId = $cateId
            ";
        $mysqli_result = $this->db->select($query);
        if ($mysqli_result) {
            $result = mysqli_fetch_all($mysqli_result, MYSQLI_ASSOC);
            return $result;
        }
        return false;
    }


    public function update($data, $files) //cập nhật thông tin của một sản phẩm
    {
        $name = $data['name'];
        $originalPrice = $data['originalPrice'];
        $promotionPrice = $data['promotionPrice'];
        $cateId = $data['cateId'];
        $des = $data['des'];
        $qty = $data['qty'];

        $file_name = $_FILES['image']['name'];
        $file_temp = $_FILES['image']['tmp_name'];

        $div = explode('.', $file_name);
        $file_ext = strtolower(end($div));
        $unique_image = substr(md5(time()), 0, 10) . '.' . $file_ext;
        $uploaded_image = "uploads/" . $unique_image;

        //Kiểm tra người dùng có chọn ảnh mới không
        if (!empty($file_name)) {
            move_uploaded_file($file_temp, $uploaded_image);
            $query = "UPDATE products SET 
					name ='$name',
					cateId = '$cateId',
					originalPrice = '$originalPrice',
					promotionPrice = '$promotionPrice',
					des = '$des',
					qty = '$qty',
					image = '$unique_image'
					 WHERE id = " . $data['id'] . " ";
        } else {
            $query = "UPDATE products SET 
					name ='$name',
					cateId = '$cateId',
					originalPrice = '$originalPrice',
					promotionPrice = '$promotionPrice',
					des = '$des',
					qty = '$qty'
					 WHERE id = " . $data['id'] . " ";
        }
        $result = $this->db->update($query);
        if ($result) {
            $alert = "<span class='success'>Cập nhật sản phẩm thành công</span>";
            return $alert;
        } else {
            $alert = "<span class='error'>Cập nhật sản phẩm thất bại</span>";
            return $alert;
        }
    }

    public function getProductbyIdAdmin($id)//lấy thông tin chi tiết của một sản phẩm dựa trên id
    {
        $query = "SELECT * FROM products where id = '$id'";
        $result = $this->db->select($query);
        return $result;
    }

    public function getProductbyId($id)// lấy thông tin một sản phẩm (còn hoạt động) dựa theo id
    {
        $query = "SELECT * FROM products where id = '$id' AND status = 1";
        $mysqli_result = $this->db->select($query);
        if ($mysqli_result) {
            $result = mysqli_fetch_all($this->db->select($query), MYSQLI_ASSOC)[0];
            return $result;
        }
        return false;
    }

    public function block($id)//"ẩn" hoặc "khóa" một sản phẩm
    {
        $query = "UPDATE products SET status = 0 where id = '$id' ";
        $result = $this->db->delete($query);
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    public function delete($id)//xóa vĩnh viễn một sản phẩm khỏi cơ sở dữ liệu theo id
    {
        $query = "DELETE FROM products WHERE id = $id";
        $row = $this->db->delete($query);
        if ($row) {
            return true;
        }
        return false;
    }

     public function active($id)//kích hoạt lại sản phẩm
    {
        $query = "UPDATE products SET status = 1 where id = '$id' ";
        $result = $this->db->delete($query);
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    public function updateQty($id, $qty)//giảm số lượng (qty) của sản phẩm có id tương ứng
    {
        $query = "UPDATE products SET qty = qty - $qty WHERE id = $id";
        $mysqli_result = $this->db->update($query);
        if ($mysqli_result) {
            return true;
        }
        return false;
    }

     public function updateSold($id, $qty)//cập nhật số lượng sản phẩm đã bán
    {
        $query = "UPDATE products SET soldCount = soldCount + $qty WHERE id = $id";
        $mysqli_result = $this->db->update($query);
        if ($mysqli_result) {
            return true;
        }
        return false;
    }

    public function checkQty($productId)// kiểm tra xem số lượng sản phẩm trong giỏ hàng có vượt quá số lượng tồn kho hay không.
    {
        // Lấy số lượng còn lại trong kho
        $sql_confirm_qty = "SELECT qty FROM `products` WHERE id = $productId LIMIT 1";
        $mysqli_result1 = $this->db->select($sql_confirm_qty);
        $qtyNow = mysqli_fetch_all($mysqli_result1, MYSQLI_ASSOC)[0];
        $qtyNow1 = array_sum($qtyNow );
        // Lấy số lượng sản phẩm trong giỏ hàng
        $query = "SELECT qty FROM `cart` WHERE productId = $productId LIMIT 1";
        $mysqli_result2 = $this->db->select($query);
        $qtyInCart= mysqli_fetch_all($mysqli_result2, MYSQLI_ASSOC)[0];
        $qtyInCart1 = array_sum($qtyInCart );

        if(intval($qtyInCart1) <= intval($qtyNow1)){
            return true;
        }
        return false;
    }
 }
 ?>