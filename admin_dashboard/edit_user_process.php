<?php
session_start();
require '../includes/db_connect.php';

// Kiểm tra quyền truy cập
if ($_SESSION['role'] != 'admin') {
    header("Location: /ShoppingWebsite/index.php");
    exit();
}

// Kiểm tra xem có dữ liệu gửi lên từ form hay không
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    $role = $_POST['role'];

    // Cập nhật thông tin người dùng vào cơ sở dữ liệu
    $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, phone_number = ?, role = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $name, $email, $phone_number, $role, $user_id);

    if ($stmt->execute()) {
        echo "<script>
                        alert('Edit user successful.');
                        window.location.href = 'user_management.php';
                    </script>";
                    exit();
    } else {
        echo "<script>
                        alert('Error in editting user.');
                        window.location.href = 'user_management.php';
                    </script>";
    }

    $stmt->close();
    $conn->close();
}
?>
