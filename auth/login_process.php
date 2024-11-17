<?php
// Cấu hình bảo mật session
ini_set('session.cookie_secure', 1); // Chỉ cho phép cookie qua HTTPS
ini_set('session.cookie_httponly', 1); // Ngăn truy cập cookie qua JavaScript
ini_set('session.cookie_samesite', 'Strict'); // Cấm gửi cookie trong các yêu cầu từ trang web khác
session_start();
require 'db_connect.php'; 

// Lấy thông tin từ form
$email = htmlspecialchars(trim($_POST['email'])); // Dùng html specialchars và trim để tránh XSS
$password = $_POST['password'];
$recaptcha_response = $_POST['g-recaptcha-response']; // Lấy recaptcha
$errors = []; // Tạo mảng lưu lỗi

// Ktra recaptcha
$secret_key = '6LcWmnsqAAAAABIRsSdRmzsd5qVCZ4uaL3iY6w-h';  
$response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secret_key&response=$recaptcha_response");
$response_keys = json_decode($response, true);

if (empty($recaptcha_response)) { // Kiểm tra đã điền reCAPTCHA 
    $errors[] = "Please complete the reCAPTCHA.";
}
if (intval($response_keys["success"]) !== 1) { // Kiểm tra lỗi reCaptcha
    $errors[] = "ReCaptcha error";
}

// Kiểm tra đầu vào của email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Invalid email format.";
}

// Kiểm tra điều kiện mật khẩu (Chữ hoa, có số, ký tự đặc biệt tối thiểu 8 ký tự)
if (!preg_match('/^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password)) {
    $errors[] = "Password must contain at least one uppercase letter, one number, one special character, and be at least 8 characters long.";
}

// Nếu có lỗi, chuyển hướng về trang đăng nhập
if (!empty($errors)) {
    // Thêm dấu '+' vào đầu mỗi lỗi
    $errors_with_plus = array_map(function($error) {
        return '+ ' . $error;
    }, $errors);
    
    // Nối các lỗi thành chuỗi 
    $_SESSION['error_message'] = implode("<br>", $errors_with_plus); 
    header("Location: index.php");
    exit();
}

// Kiểm tra đăng nhập với email
$stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $user = $result->fetch_assoc();
    
    //Ktra tài khoản bị khóa và xem đã hết thời gian khóa
    if ($user['account_locked'] == 1) {
        $lock_time = $user['lock_time']; 
        $current_time = date("Y-m-d H:i:s");

        //Tính tgian khóa (30p)
        $lock_expiration_time = strtotime($lock_time . ' +30 minutes');

        if (strtotime($current_time) > $lock_expiration_time) {
            //Nếu hết thời gian khóa, mở khóa tài khoản và tiếp tục đăng nhập
            $stmt = $conn->prepare("UPDATE users SET account_locked = 0, failed_attempts = 0, lock_time = NULL WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
        } else {
            $_SESSION['error_message'] = "+ Your account is locked due to multiple failed login attempts. Please try again later.";
            header("Location: index.php");
            exit();
        }
    }
    // Xác minh mật khẩu
    if (password_verify($password, $user['password'])) {
         //Đặt lại số lần thử khi đăng nhập thành công
         $stmt = $conn->prepare("UPDATE users SET failed_attempts = 0 WHERE email = ?");
         $stmt->bind_param("s", $email);
         $stmt->execute();

        // Khởi tạo lại session ID để tránh session hijacking
        session_regenerate_id(true);

        // Lưu thông tin người dùng vào session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];

        // Điều hướng đến trang người dùng hoặc trang admin
        if ($user['role'] == 'user') {
            // Tạo mã OTP (6 chữ số)
            $otp_code = random_int(100000, 999999);

            // Thời gian hết hạn của OTP là 5 phút
            $otp_expiration = date("Y-m-d H:i:s", strtotime("+5 minutes"));

            // Cập nhật mã OTP và thời gian hết hạn trong cơ sở dữ liệu với email đã biết
            $stmt = $conn->prepare("UPDATE users SET otp_code = ?, otp_expiration = ? WHERE email = ?");
            $stmt->bind_param("sss", $otp_code, $otp_expiration, $email);

            if ($stmt->execute()) {
                // Gửi mail OTP 
                $subject = "Your OTP Code";
                $message = "Your OTP code is: " . $otp_code;
                $headers = "From: B2C-Shoe Shop";
                
                if (mail($email, $subject, $message, $headers)) {
                    // Thông báo gửi OTP thành công
                    echo "<script>
                        alert('Login successful. OTP has been sent to your email.');
                        window.location.href = 'otp.php';
                    </script>";
                    exit();
                } else {
                    $errors[] = "Error in sending OTP";
                }
            } else {
                $errors[] = "OTP update error";
            }
        } else {
            $_SESSION['verified'] = true;
            $_SESSION['role'] = 'admin';
            header("Location: admin_dashboard/admin_dashboard.php");
            exit();
        }
    } else {
        // Tăng số lần thử sai
        $failed_attempts = $user['failed_attempts'] + 1;

        // Tính số lần thử còn lại
        $remaining_attempts = 3 - $failed_attempts;

        // Cập nhật số lần thử vào cơ sở dữ liệu
        $stmt = $conn->prepare("UPDATE users SET failed_attempts = ? WHERE email = ?");
        $stmt->bind_param("is", $failed_attempts, $email);
        $stmt->execute();

        // Thông báo số lần thử còn lại
        if ($remaining_attempts > 0) {
            $errors[] = "You have $remaining_attempts attempts remaining.";
        } else {
            // Khóa tài khoản sau khi thử sai đủ 3 lần
            $lock_time = date("Y-m-d H:i:s");
            $stmt = $conn->prepare("UPDATE users SET account_locked = 1, lock_time = ? WHERE email = ?");
            $stmt->bind_param("ss", $lock_time, $email);
            $stmt->execute();
            
            $errors[] = "Your account is locked after this attempt.";
        }
    }
} else {
    $_SESSION['error_message'] = "+ No account found with that email.";
    header("Location: index.php");
    exit();
}

// Nếu có lỗi, chuyển hướng về trang đăng nhập
if (!empty($errors)) {
    // Thêm dấu '+' vào đầu mỗi lỗi
    $errors_with_plus = array_map(function($error) {
        return '+ ' . $error;
    }, $errors);

    // Nối các lỗi thành chuỗi với mỗi lỗi trên một dòng mới
    $_SESSION['error_message'] = implode("<br>", $errors_with_plus); 
    header("Location: index.php");
    exit();
}

$stmt->close();
$conn->close();
?>
