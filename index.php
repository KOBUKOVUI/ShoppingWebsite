<?php
// Cấu hình bảo mật session
ini_set('session.cookie_secure', 1); // Chỉ cho phép cookie qua HTTPS
ini_set('session.cookie_httponly', 1); // Ngăn truy cập cookie qua JavaScript
ini_set('session.cookie_samesite', 'Strict'); // Cấm gửi cookie trong các yêu cầu từ trang web khác
session_start();

// Kiểm tra xem người dùng đã đăng nhập chưa
if (isset($_SESSION['verified']) && $_SESSION['verified'] == true) {
    if (($_SESSION['role'] == 'admin') ) {
        // Nếu là admin đã đăng nhập, chuyển hướng tới trang admin dashboard
        header("Location: admin_dashboard/admin_dashboard.php");
        exit(); 
    }else{
        header("Location: user/home.php"); // Nếu là ng dùng đến home
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="css/login_register_styles.css">
    <link rel="icon" href="icon/domain.png" type = "image/x-icon">
    <!-- add capcha -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body id = "login_body">
    <div id = "login_container">
        <h2>Login</h2>
        <!--ktra các lỗi xảy ra -->
        <?php
            if (isset($_SESSION['error_message'])) {
                echo "<p style='color: red; font-size: 15px; text-align: left;'>" . $_SESSION['error_message'] . "</p>";
                unset($_SESSION['error_message']);  // Xóa thông báo lỗi
            }
            ?>
        <form id ="login_form" action="auth/login_process.php" method = "POST">
            <label for="email">Email</label>
            <input type="text" id="email" name="email" required>
            <br>
            <label for="password">Password</label>
            <label for="password_help"> <ul id="password_help">
                    <li style="font-size: 14px; color: #f0ad4e; margin-top: 5px; text-align: left; list-style-type: disc; padding-left: 20px;">Must contain at least one uppercase letter.</li>
                    <li style="font-size: 14px; color: #f0ad4e; margin-top: 5px; text-align: left; list-style-type: disc; padding-left: 20px;">Must contain at least one number.</li>
                    <li style="font-size: 14px; color: #f0ad4e; margin-top: 5px; text-align: left; list-style-type: disc; padding-left: 20px;">Must contain at least one special character.</li>
                    <li style="font-size: 14px; color: #f0ad4e; margin-top: 5px; text-align: left; list-style-type: disc; padding-left: 20px;">Must be at least 8 characters long.</li>
                </ul></label>
             <br>
            <input type="password" id="password" name="password" required>
            <br>
            <div style="display: flex; align-items: center;">
             <label for="captcha" style = "font-size: 15px">Verify that you are not a robot:</label>
             <div class="g-recaptcha" data-sitekey="6LcWmnsqAAAAAHCiHq6DoK7T74iYmHe54khajjZ8" style ="transform: scale(0.8); transform-origin: 0 0; margin: 0 auto; "></div>
             </div>
            <button type = "submit">Login</button>
            <p>Don't have any account? <a  href="auth/register.php">Register now</a></p>
        </form>

    </div>
</body>
</html>