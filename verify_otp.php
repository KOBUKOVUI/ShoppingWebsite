<?php
session_start();
// Cấu hình bảo mật session
ini_set('session.cookie_secure', 1); // Chỉ cho phép cookie qua HTTPS
ini_set('session.cookie_httponly', 1); // Ngăn truy cập cookie qua JavaScript
require 'db_connect.php';

//ktra người dùng đăng ký chưa 

if(!isset($_SESSION['user_id'])){
    header("Location: register.php");
    exit();
}

//ktra otp

//ktra dùng phương thức Post
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    // Lấy mã OTP từ form
    $otp_code = htmlspecialchars(trim($_POST['otp_code']));//dùng html specialchars và trim để tránh xss

    // Lấy user_id từ session để xác thực OTP
    $user_id = $_SESSION['user_id'];

    //ktra mã otp lưu trong csdl, dùng preapare tránh sql injection
    $stmt = $conn->prepare("SELECT otp_code, otp_expiration FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        // Kiểm tra mã OTP có khớp và còn hạn không
        if ($user['otp_code'] == $otp_code && strtotime($user['otp_expiration']) > time()) {
            // Xác minh thành công -> Cập nhật thông tin người dùng
            $stmt = $conn->prepare("UPDATE users SET otp_code = NULL, otp_expiration = NULL WHERE id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();

            // Cập nhật session để đánh dấu người dùng đã xác minh
            $_SESSION['verified'] = true;

            // Chuyển hướng trang home
            echo "<script>
            alert('Verified OTP successfully.');
                window.location.href = 'home.php';
            </script>";
        exit();
        } else {
            
            $_SESSION['error_message'] = "Invalid or expired OTP.";
            header("Location: otp.php");
            exit();
        }
    } else {
        $_SESSION['error_message'] = "OTP eror.";
        header("Location: otp.php");
        exit();
    }
}
?>

