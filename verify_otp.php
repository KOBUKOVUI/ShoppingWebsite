<?php
// Kết nối cơ sở dữ liệu
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "your_database";

$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Lấy thông tin OTP từ form
$email = $_POST['email'];
$otp_code = $_POST['otp_code'];

// Kiểm tra OTP trong cơ sở dữ liệu
$sql = "SELECT * FROM users WHERE email = '$email' AND otp_code = '$otp_code' AND otp_expiration > NOW()";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // OTP hợp lệ, xác nhận tài khoản
    $update_sql = "UPDATE users SET is_verified = 1 WHERE email = '$email'";
    if ($conn->query($update_sql) === TRUE) {
        echo "Account verified successfully!";
    } else {
        echo "Error verifying account.";
    }
} else {
    echo "Invalid OTP or OTP expired.";
}

$conn->close();
?>
