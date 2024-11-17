<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit account</title>
    <link rel="icon" href="icon/edit_account.png" type = "image/x-icon">
    <link rel="stylesheet" href="css/edit_account_styles.css">
</head>
<body>
<?php
session_start();
require 'db_connect.php';

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Lấy thông tin user_id từ session
$user_id = $_SESSION['user_id'];

// Truy vấn thông tin người dùng
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Nếu người dùng không tồn tại
if ($result->num_rows !== 1) {
    echo "User not found.";
    exit();
}

// Lấy thông tin người dùng
$user = $result->fetch_assoc();

$stmt->close();
$conn->close();
?>
 <header>
        <h1>Account management</h1>
        <nav>
            <a href="home.php">Home</a>
            <a href="edit_account.php">Account management</a>
            <a href="orders.php">Orders</a>
            <a id = "header_logout" href = 'logout.php'>Log out</a>
        </nav>
    </header>
<main>
        <h2>Account informations</h2>

        <!-- Hiển thị thông tin tài khoản -->
        <div id="account_info">
    <div class="info_item">
        <p><strong>Email:</strong></p>
        <p><?= htmlspecialchars($user['email']) ?></p>
    </div>
    <div class="info_item">
        <p><strong>Full name:</strong></p>
        <p><?= htmlspecialchars($user['name']) ?></p>
    </div>
    <div class="info_item">
        <p><strong>Phone number:</strong></p>
        <p><?= htmlspecialchars($user['phone_number'] ?? 'Not Provided') ?></p>
    </div>
</div>
        <h2>Edit informations</h2>
        <!--ktra các lỗi xảy ra -->
        <?php
            if (isset($_SESSION['error_message'])) {
                echo "<p style='color: green; font-weight: bold; font-size: 15px; text-align: center;'>" . $_SESSION['error_message'] . "</p>";
                unset($_SESSION['error_message']);  // Xóa thông báo lỗi
            }
        ?>
        <form method="POST" action="edit_account_process.php">
            <label for="name">Full name</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>

            <label for="phone_number">Phone number</label>
            <input type="text" id="phone_number" name="phone_number" value="<?= htmlspecialchars($user['phone_number']) ?>">
            <br>
            <button type="submit">Update</button>
        </form>
    </main>
    <footer>
    <div id="footer_content">
        <p>&copy; 2024 B2C-Shoe Shop</p>
        <p>All rights reserved.</p>
        <div id="footer_links">
            <a href="">Privacy Policy</a> |
            <a href="">Terms of Service</a> |
            <a href="">Contact</a>
        </div>

        <p id="footer_social">
            Follow us:
            <a href="" target="_blank">Facebook</a> |
            <a href="" target="_blank">Twitter</a> |
            <a href="" target="_blank">Instagram</a>
        </p>
    </div>
</footer>
</body>
</html>