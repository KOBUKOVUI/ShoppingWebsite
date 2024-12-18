<?php
ini_set('session.cookie_secure', 1); // chỉ cho phép cookie qua HTTPS
ini_set('session.cookie_httponly', 1); // không cho phép truy cập cookie qua JavaScript
ini_set('session.cookie_samesite', 'Strict'); // chỉ gửi cookie trong cùng một trang web
session_start();

// Kiểm tra xem người dùng có phải là admin không
if ($_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

require '../includes/db_connect.php';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy dữ liệu từ form
    $product_id = intval($_POST['product_id']);
    $name = htmlspecialchars(trim($_POST['name']), ENT_QUOTES, 'UTF-8');
    $category_id = intval($_POST['category_id']);
    $price = floatval(str_replace(',', '', $_POST['price'])); // Loại bỏ dấu ',' trước khi ép thành số
    $brand = htmlspecialchars(trim($_POST['brand']), ENT_QUOTES, 'UTF-8');
    $description = htmlspecialchars(trim($_POST['description']), ENT_QUOTES, 'UTF-8');
    
    // Kiểm tra nếu người dùng có tải lên ảnh mới
    if (!empty($_FILES['image']['name'])) {
        // Lấy thông tin tệp ảnh
        $image = $_FILES['image']['name'];
        $target_dir = "../uploads/";
        $target_file = $target_dir . basename($image);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Kiểm tra định dạng tệp ảnh
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($imageFileType, $allowed_extensions)) {
            $errors[] = "Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.";
        }

        // Kiểm tra kích thước tệp (tối đa 5MB)
        if ($_FILES['image']['size'] > 5000000) { // 5MB
            $errors[] = "File is too large. Maximum file size is 5MB.";
        }

        // Kiểm tra MIME type của tệp (chắc chắn là hình ảnh)
        $check = getimagesize($_FILES['image']['tmp_name']);
        if ($check === false) {
            $errors[] = "File is not an image.";
        }

        // Nếu không có lỗi, thực hiện tải lên ảnh
        if (empty($errors)) {
            // Di chuyển tệp đến thư mục uploads
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                $image_url = $target_file;
            } else {
                $errors[] = "Error in uploading file. Please try again.";
            }
        }
    } else {
        // Nếu không có ảnh mới, giữ lại ảnh cũ trong cơ sở dữ liệu
        $sql = "SELECT image_url FROM products WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $image_url = $row['image_url']; // Lấy ảnh cũ từ cơ sở dữ liệu
        } else {
            $errors[] = "Product not found.";
        }
    }

    // Kiểm tra nếu có lỗi trong quá trình xử lý
    if (!empty($errors)) {
        // Thêm dấu '+' vào đầu mỗi lỗi
        $errors_with_plus = array_map(function ($error) {
            return '+ ' . $error;
        }, $errors);

        // Nối các lỗi thành chuỗi và lưu vào session để hiển thị thông báo
        $_SESSION['error_message'] = implode("<br>", $errors_with_plus);

        // Chuyển hướng về trang edit_product.php với thông báo lỗi
        header("Location: edit_product.php?product_id=" . $product_id);
        exit();
    }

    // Cập nhật thông tin sản phẩm trong cơ sở dữ liệu
    $sql_update = "
        UPDATE products SET 
        name = ?, 
        category_id = ?, 
        price = ?, 
        brand = ?, 
        description = ?, 
        image_url = ? 
        WHERE id = ?
    ";
    $stmt = $conn->prepare($sql_update);
    $stmt->bind_param("sissssi", $name, $category_id, $price, $brand, $description, $image_url, $product_id);

    if ($stmt->execute()) {
        echo "<script>
                        alert('Product updated successfully.');
                        window.location.href = 'product_management.php';
                    </script>";
        exit();
    } else {
        echo "<script>
                        alert('Error in updating product.');
                        window.location.href = 'edit_product.php?product_id=" . $product_id . "';
                    </script>";
        exit();
    }
}
?>
