<?php
ini_set('session.cookie_secure', 1); // Chỉ cho phép cookie qua HTTPS
ini_set('session.cookie_httponly', 1); // Ngăn truy cập cookie qua JavaScript
ini_set('session.cookie_samesite', 'Strict'); // Chỉ gửi cookie trong cùng một trang web
session_start();

// Kiểm tra xem người dùng có phải là admin không
if ($_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

require '../includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy dữ liệu từ form
    $name = htmlspecialchars(trim($_POST['name']), ENT_QUOTES, 'UTF-8');
    $category_id = intval($_POST['category_id']);
    $price = htmlspecialchars(intval(str_replace('.', '', $_POST['price']))); // Loại bỏ dấu chấm
    $size_id = intval($_POST['size_id']);
    $brand = trim($_POST['brand']);
    $stock = intval($_POST['stock']);
    $description = htmlspecialchars(trim($_POST['description']), ENT_QUOTES, 'UTF-8');
    $image = htmlspecialchars(basename($_FILES['image']['name']), ENT_QUOTES, 'UTF-8');
    $errors = []; //tạo mảng lưu lỗi 

    //ktra sản phầm đã tồn tại chưa
    $sql_check = "SELECT id FROM products WHERE name = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param('s', $name);
    $stmt_check->execute();
    $stmt_check->store_result();
    
    if ($stmt_check->num_rows > 0) {
        $errors[] = "Product already exists with the same name.";
    }
    $stmt_check->close();
    if (!empty($errors)) {
        // Thêm dấu '+' vào đầu mỗi lỗi
        $errors_with_plus = array_map(function($error) {
            return '+ ' . $error;
        }, $errors);
        
        // Nối các lỗi thành chuỗi và lưu vào session để hiển thị thông báo
        $_SESSION['error_message'] = implode("<br>", $errors_with_plus); 
        
        // Chuyển hướng về trang add_product.php với thông báo lỗi
        header("Location: add_product.php");
        exit();
    }

    //Lấy địa chỉ file khi upload
    $target_dir = "../uploads/";
    $target_file = $target_dir . basename($image);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Kiểm tra định dạng tệp ảnh
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Kiểm tra nếu tệp ảnh có hợp lệ không
    if (!in_array($imageFileType, $allowed_extensions)) {
        $errors[] = "Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.";
    }

    // Ktra định dạng tệp hợp lệ để upload
    if (empty($errors)) {
        // Kiểm tra upload ảnh
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) { 
            $image = $target_file;
        } else {
            $errors[] = "Error in uploading file. Please try again.";
        }
    }

// Kiểm tra nếu có lỗi
if (!empty($errors)) {
    // Thêm dấu '+' vào đầu mỗi lỗi
    $errors_with_plus = array_map(function($error) {
        return '+ ' . $error;
    }, $errors);
    
    // Nối các lỗi thành chuỗi và lưu vào session để hiển thị thông báo
    $_SESSION['error_message'] = implode("<br>", $errors_with_plus); 
    
    // Chuyển hướng về trang add_product.php với thông báo lỗi
    header("Location: add_product.php");
    exit();
}

// Tiếp tục xử lý phần khác của mã (ví dụ, lưu thông tin sản phẩm vào cơ sở dữ liệu)

    // Thêm sản phẩm vào bảng products
    $sql_product = "INSERT INTO products (name, description, price, stock, brand, category_id, image_url, created_at, updated_at) 
    VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
    $stmt_product = $conn->prepare($sql_product);
    $stmt_product->bind_param('ssiisss', $name, $description, $price, $stock, $brand, $category_id, $image);
    if ($stmt_product->execute()) {
        //thêm sản phầm vào bảng size nữa
        $product_id = $conn->insert_id;

        // Thêm thông tin kích thước và tồn kho vào bảng product_sizes
        $sql_sizes = "INSERT INTO product_sizes (product_id, size_id, stock) VALUES (?, ?, ?)";
        $stmt_sizes = $conn->prepare($sql_sizes);
        $stmt_sizes->bind_param('iii', $product_id, $size_id, $stock);
        if ($stmt_sizes->execute()) {
            echo "<script>
                            alert('Added new products succesfully.');
                            window.location.href = 'add_product.php';
                        </script>";
                        exit();
        }else{
            echo "<script>
                            alert('Error in adding new products(in product_sizes table)');
                            window.location.href = 'add_product.php';
                        </script>";
                        exit();
        }
    }else{
        echo "<script>
                        alert('Error in adding new products');
                        window.location.href = 'add_product.php';
                    </script>";
                    exit();
    }
}
?>