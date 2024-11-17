<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP</title>
    <link rel="stylesheet" href="../css/otp_styles.css">
    <link rel="icon" href="../icon/password.png" type = "image/x-icon">
</head>
<body id="otp_body">
    <div id="otp_container">
        <form id="otp_form" action="verify_otp.php" method="POST">
            <h2>Verify OTP</h2>
            <!--Thông báo các lỗi trong quá trình kiểm tra -->
            <?php
            
            session_start();
            if (isset($_SESSION['error_message'])) {
                echo "<p style='color: red; font-size: 25px;'>" . $_SESSION['error_message'] . "</p>";
                unset($_SESSION['error_message']);  // Xóa thông báo lỗi
            }
            ?>
            <p>Please enter the OTP sent to your email.</p>
            
            <?php if (isset($error_message)): ?>
                <p style="color: red;"><?php echo $error_message; ?></p>
            <?php endif; ?>

            <label for="otp_code">OTP Code</label>
            <input type="text" id="otp_code" name="otp_code" required>
            <button type="submit">Verify</button>
        </form>
    </div>
</body>
</html>