<?php
session_start();
require '../includes/db_connect.php';

// Kiểm tra xem người dùng có phải là admin không
if ($_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

// Kiểm tra nếu có id ng dùng cần xóa 
if (isset($_POST['id'])) {
    $user_id = $_POST['id'];

    // Kiểm tra k cho xóa tk admin
    $query = "SELECT role FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    if ($user && $user['role'] === 'admin') {
        $_SESSION['error_message'] = "You cannot delete an admin account! ";
        header ("Location: user_management.php");
        exit();
    }
    // Xóa người dùng khỏi cơ sở dữ liệu
    $query = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    
    if ($stmt->execute()) {
        echo "<script>
                        alert('Delete user successful.');
                        window.location.href = 'user_management.php';
                    </script>";
                    exit();
    } else {
        $_SESSION['error_message'] = "Deleting eror. ";
        header("Location: user_management.php");
        exit();
    }
} else {
    // Nếu không có id thì chuyển hướng về trang quản lý người dùng
    header("Location: user_management.php");
    exit();
}
