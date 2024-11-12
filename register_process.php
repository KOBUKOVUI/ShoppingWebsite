<?php
session_start();
// Cấu hình bảo mật session
ini_set('session.cookie_secure', 1); // Chỉ cho phép cookie qua HTTPS
ini_set('session.cookie_httponly', 1); // Ngăn truy cập cookie qua JavaScript
require 'db_connect.php';

//lấy thông tin từ form
$email = htmlspecialchars(trim($_POST['email']));//dùng html specialchars và trim để tránh xss
$name = htmlspecialchars(trim($_POST['name']));  //dùng html specialchars và trim để tránh xss
$password = $_POST['password'];
$confirm_password = $_POST['confirm_password'];
$phone_number = htmlspecialchars(trim($_POST['phone_number']));//dùng html specialchars và trim để tránh xss
$recaptcha_response = $_POST['g-recaptcha-response']; //lấy recaptcha  
$errors = []; //tạo mảng lưu lỗi

//kiểm tra nhập lại mật khẩu
if($password !== $confirm_password){
   $errors[] = "Password do not match";
}

// kiểm tra điều kiện đặt mật khẩu (Chữ hoa, có số, ký tự đặc biệt tối thiểu 8 kí tự)
if (!preg_match('/^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password)) {
    $errors[] = "Password must contain at least one uppercase letter, one number, one special character, and be at least 8 characters long.";
}

//ktra đầu vào của email 
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Invalid email format.";
}

//ktra đầu vào của phone number 
if (!preg_match('/^\+?\d{10,15}$/', $phone_number)) {
    $errors[] = "Invalid phone number format.";
}
//ktra mật khẩu k chứa tên người dùng hay email 
if (stripos($password, $email) !== false || stripos($password, $name) !== false) {
    $errors[] = "Password cannot contain your email or name.";
}


//ktra số dt và email đã tồn tại chưa
$stmt = $conn->prepare ("SELECT * FROM users WHERE email = ? OR phone_number = ?"); //dùng prepare statement tránh sql injection
$stmt->bind_param("ss", $email, $phone_number);  // 'ss' tương ứng với 2 tham số kiểu string
$stmt->execute();
$result = $stmt->get_result();
if($result->num_rows > 0){
    $errors[] = "Email or phone number already exists.";
}

//ktra recaptcha
$secret_key = '6LcWmnsqAAAAABIRsSdRmzsd5qVCZ4uaL3iY6w-h';  
$response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secret_key&response=$recaptcha_response");
$response_keys = json_decode($response, true);
if (empty($recaptcha_response)) {// Ktra đã điền reCAPTCHA chưa
    $errors[] = "Please complete the reCAPTCHA.";
}
if(intval($response_keys["success"]) !== 1) { //ktra lỗi reCaptcha
    $errors[] = "reCaptcha error";
}

// Nếu có lỗi, chuyển hướng về trang đăng ký
if (!empty($errors)) {
    // Thêm dấu '+' vào đầu mỗi lỗi
    $errors_with_plus = array_map(function($error) {
        return '+ ' . $error;
    }, $errors);
    
    // Nối các lỗi thành chuỗi với mỗi lỗi trên một dòng mới
    $_SESSION['error_message'] = implode("<br>", $errors_with_plus); 
    header("Location: register.php");
    exit();
}


//mã hóa mật khẩu
$hashed_password = password_hash($password, PASSWORD_DEFAULT); // dùng bcrypt có thêm salt

//tạo mã OTP (6 chữ số)
$otp_code = random_int(100000, 999999);

//Thời gian hết hạn của OTP là 5p
$otp_expiration = date("Y-m-d H:i:s", strtotime("+5 minutes"));

//Lưu thông tin người dùng và OTP vào cơ sở dữ liệu bằng prepared statement
$stmt = $conn->prepare("INSERT INTO users (email, password, phone_number, name, otp_code, otp_expiration) 
                        VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssss", $email, $hashed_password, $phone_number, $name, $otp_code, $otp_expiration);
if ($stmt->execute()) {
    // Lưu thông tin vào session khi đăng ký thành công
    session_regenerate_id(true); // Tạo session mới để bảo vệ session cũ, tránh session hijacking
    $_SESSION['user_id'] = $conn->insert_id;  
    $_SESSION['email'] = $email;  
    $_SESSION['role'] = 'user';  

//gửi mail xác nhận OTP
    $subject = "Your OTP Code";
    $message = "Your OTP code is: " . $otp_code;
    $headers = "From: B2C-Shoe Shop";

    if (mail($email, $subject, $message, $headers)) {
        //thông báo gửi otp thành công
        echo "<script>
            alert('Registration successful. OTP has been sent to your email.');
                window.location.href = 'otp.php';
        </script>";
        exit();
    } else {
        $errors[] = "Error in sending OTP";
    }
} else {
    $errors[] =  "Error: " . $stmt->error;
}
if (!empty($errors)) {
    // Thêm dấu '+' vào đầu mỗi lỗi
    $errors_with_plus = array_map(function($error) {
        return '+ ' . $error;
    }, $errors);
    
    // Nối các lỗi thành chuỗi với mỗi lỗi trên một dòng mới
    $_SESSION['error_message'] = implode("<br>", $errors_with_plus); 
    header("Location: register.php");
    exit();
}

$stmt->close();
$conn->close();
?>