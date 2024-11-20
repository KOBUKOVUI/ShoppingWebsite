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

// Kiểm tra nếu form được gửi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $product_id = intval($_POST['product_id']); // Ép kiểu để đảm bảo an toàn

        // Câu lệnh SQL để xóa sản phẩm
        $sql = "DELETE FROM products WHERE id = ?";

        // Chuẩn bị câu truy vấn
        $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $product_id); // Gán tham số

            // Thực thi câu truy vấn
            if ($stmt->execute()) {
                echo "<script>
                            alert('Delete product succesfully');
                            window.location.href = 'product_management.php';
                        </script>";
                        exit();
            } else {
                echo "<script>
                            alert('Error in deleting new products');
                            window.location.href = 'product_management.php';
                        </script>";
                        exit();
            }

            // Đóng câu lệnh
            $stmt->close();
}

// Đóng kết nối
$conn->close();
?>