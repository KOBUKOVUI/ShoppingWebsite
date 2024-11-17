<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="../css/login_register_styles.css">
    <link rel="icon" href="../icon/edit.png" type = "image/x-icon">
    <!-- add capcha -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body id = "register_body">
    <div id = "register_container">
        <form id = "register_form" action="register_process.php" method = "POST">
            <h2>Register</h2>
            <!--ktra các lỗi xảy ra -->
            <?php
            session_start();
            if (isset($_SESSION['error_message'])) {
                echo "<p style='color: red; font-size: 15px; text-align: left;'>" . $_SESSION['error_message'] . "</p>";
                unset($_SESSION['error_message']);  // Xóa thông báo lỗi
            }
            ?>
             <label for="email">Email</label>
             <input type="text" id = "email" name = "email" required>
             <br>
             <label for="name">Name</label>
             <input type="text" id = "name" name = "name">
             <label for="password">Password </label>
             <label for="password_help"> <ul id="password_help">
                    <li style="font-size: 14px; color: #f0ad4e; margin-top: 5px; text-align: left; list-style-type: disc; padding-left: 20px;">Must contain at least one uppercase letter.</li>
                    <li style="font-size: 14px; color: #f0ad4e; margin-top: 5px; text-align: left; list-style-type: disc; padding-left: 20px;">Must contain at least one number.</li>
                    <li style="font-size: 14px; color: #f0ad4e; margin-top: 5px; text-align: left; list-style-type: disc; padding-left: 20px;">Must contain at least one special character.</li>
                    <li style="font-size: 14px; color: #f0ad4e; margin-top: 5px; text-align: left; list-style-type: disc; padding-left: 20px;">Must be at least 8 characters long.</li>
                </ul></label>
             <br>
             <input type="password" id = "password" name = "password" required>
             <br>
             <label for="confirm_password">Confirm password</label>
             <input type="password" id = "confirm_password" name = "confirm_password" required>
             <br>
             <label for="number">Phone number</label>
             <input type="text" id = "phone_number" name = "phone_number" required>
             <br>
             <div style="display: flex; align-items: center;">
             <label for="captcha" style = "font-size: 15px">Verify that you are not a robot:</label>
             <div class="g-recaptcha" data-sitekey="6LcWmnsqAAAAAHCiHq6DoK7T74iYmHe54khajjZ8" style ="transform: scale(0.8); transform-origin: 0 0; margin: 0 auto; "></div>
             </div>
             <button type="submit">Register</button>
             <p>Already have an account <a href="../index.php">Login now</a></p>

        </form>
    </div>
</body>
</html>