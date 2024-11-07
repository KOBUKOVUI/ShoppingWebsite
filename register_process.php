<?php
session_start();
require db_connect.php;

//lấy thông tin từ form
//dùng html specialchars và trim để tránh xss
$email = htmlspecialchars(trim($_POST['email']));
$name = htmlspecialchars(trim($_POST['name']));  
$password = $_POST['password'];
$confirm_password = $_POST['confirm_password'];
$phone_number = htmlspecialchars(trim($_POST['phone_number']));

//kiểm tra nhập lại mật khẩu
if($password !== $confirm_password){
    die("Passwords do not match"); 
    header("Location: register.php");
}

//mã hóa mật khẩu
$hashed_password = password_hash($password, PASSWORD_DEFAULT) // dùng bcrypt có thêm salt

//ktra số dt và email đã tồn tại chưa
// dùng prepare statement tránh sql injection
$stmt = $conn->prepare ("SELECT * FROM users WHERE email = ? OR phone_number = ?");
$stmt->bind_param("ss", $email, $phone_number);  // 'ss' tương ứng với 2 tham số kiểu string
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows > 0){
    die("Email or phone number already exists.");
}

//tạo mã OTP (6 chữ số)
$otp_code = rand(100000, 999999);

//Thời gian hết hạn của OTP (ví dụ 5 phút)
$otp_expiration = date("Y-m-d H:i:s", strtotime("+5 minutes"));

//Lưu thông tin người dùng và OTP vào cơ sở dữ liệu bằng prepared statement
$stmt = $conn->prepare("INSERT INTO users (email, password, phone_number, name, otp_code, otp_expiration) 
                        VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssss", $email, $hashed_password, $phone_number, $name, $otp_code, $otp_expiration);
if ($stmt->execute()) {
    // Lưu thông tin vào session khi đăng ký thành công
    $_SESSION['user_id'] = $conn->insert_id;  // Lưu ID người dùng vào session
    $_SESSION['email'] = $email;  // Lưu email vào session
    $_SESSION['role'] = 'user';  // Gán role mặc định là user

// gửi mail xác nhận OTP
$subject = "Your OTP Code";
    $message = "Your OTP code is: " . $otp_code;
    $headers = "From: no-reply@example.com";

    // Gửi email
    if (mail($email, $subject, $message, $headers)) {
        echo "Registration successful. OTP has been sent to your email.";
    } else {
        echo "Error sending OTP.";
    }
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>