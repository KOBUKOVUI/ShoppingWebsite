<?php
ini_set('session.cookie_secure', 1);
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_samesite', 'Strict');
session_start();

// Kiểm tra quyền admin
if ($_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

require '../includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = intval($_POST['product_id']);
    $name = htmlspecialchars(trim($_POST['name']), ENT_QUOTES, 'UTF-8');
    $category_id = intval($_POST['category_id']);
    $price = floatval(str_replace(',', '', $_POST['price'])); // Loại bỏ dấu ',' trước khi ép thành số
    $brand = htmlspecialchars(trim($_POST['brand']), ENT_QUOTES, 'UTF-8');
    $description = htmlspecialchars(trim($_POST['description']), ENT_QUOTES, 'UTF-8');
    $errors = [];
    $image = '';

    // Kiểm tra nếu người dùng có tải lên ảnh mới
    if (!empty($_FILES['image']['name'])) {
        // Lấy tên file
        $image = $_FILES['image']['name'];
        // Lấy đường dẫn tệp khi upload
        $target_dir = "../uploads/";
        $target_file = $target_dir . basename($image);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Kiểm tra định dạng tệp ảnh
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];

        // Kiểm tra nếu tệp ảnh có hợp lệ không
        if (!in_array($imageFileType, $allowed_extensions)) {
            $errors[] = "Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.";
        }

        // Kiểm tra nếu không có lỗi và upload ảnh
        if (empty($errors)) {
            // Kiểm tra upload ảnh
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                $image = $target_file;  // Lưu đường dẫn của ảnh
            } else {
                $errors[] = "Error in uploading file. Please try again.";
            }
        }
    } else {
        // Nếu không có ảnh mới, giữ lại ảnh cũ (trong cơ sở dữ liệu)
        $sql = "SELECT image_url FROM products WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $image = $row['image_url']; // Lấy ảnh cũ
        } else {
            $errors[] = "Product not found.";
        }
    }

    // Kiểm tra nếu có lỗi
    if (!empty($errors)) {
        // Thêm dấu '+' vào đầu mỗi lỗi
        $errors_with_plus = array_map(function ($error) {
            return '+ ' . $error;
        }, $errors);

        // Nối các lỗi thành chuỗi và lưu vào session để hiển thị thông báo
        $_SESSION['error_message'] = implode("<br>", $errors_with_plus);

        // Chuyển hướng về trang edit_product.php với thông báo lỗi
        echo "<script>
                        alert('Error: " . implode(", ", $errors) . "');
                        window.location.href = 'product_management.php';
                    </script>";
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
    $stmt->bind_param("sissssi", $name, $category_id, $price, $brand, $description, $image, $product_id);

    if ($stmt->execute()) {
        echo "<script>
                        alert('Edit product successfully.');
                        window.location.href = 'product_management.php';
                    </script>";
        exit();
    } else {
        echo "<script>
                        alert('Error in editing product.');
                        window.location.href = 'product_management.php';
                    </script>";
        exit();
    }
}
?>
