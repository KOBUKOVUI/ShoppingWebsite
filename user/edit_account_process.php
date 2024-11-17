<?php
ini_set('session.cookie_secure', 1); // Chỉ cho phép cookie qua HTTPS
ini_set('session.cookie_httponly', 1); // Ngăn truy cập cookie qua JavaScript
ini_set('session.cookie_samesite', 'Strict'); // Chỉ gửi cookie trong cùng một trang web

session_start();
require '../includes/db_connect.php';

// Lấy thông tin tài khoản từ session
$user_id = $_SESSION['user_id'];

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!$user_id) {
    header("Location: ../auth/login.php");
    exit();
}

// Xử lý cập nhật thông tin
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = htmlspecialchars(trim($_POST['name']));
    $phone_number = htmlspecialchars(trim($_POST['phone_number']));
    //ktra số điện thoại tránh xss
    if (!preg_match('/^\+?\d{10,15}$/', $phone_number)) {
        $message = "Số điện thoại không hợp lệ!";
        $_SESSION["error_message"] = $message;
        header("Location: edit_account.php");
        exit();
    }
    $stmt = $conn->prepare("UPDATE users SET name = ?, phone_number = ? WHERE id = ?");
    $stmt->bind_param("ssi", $name, $phone_number, $user_id);
    
    if ($stmt->execute()) {
        $message = "Cập nhật thành công!";
    } else {
        $message = "Có lỗi khi cập nhật.";
    }
}
if(isset($message)){
    $_SESSION["error_message"] = $message;
    header("Location: edit_account.php");
    exit();
}

?>