<?php
session_start();
require '../includes/db_connect.php';

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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Account</title>
    <link rel="icon" href="../icon/edit_account.png" type="image/x-icon">
    <link rel="stylesheet" href="../css/edit_account_styles.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <main>
        <h2>Account Information</h2>

        <!-- Hiển thị thông tin tài khoản -->
        <div id="account_info">
            <div class="info_item">
                <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
            </div>
            <div class="info_item">
                <p><strong>Full Name:</strong> <?= htmlspecialchars($user['name']) ?></p>
            </div>
            <div class="info_item">
                <p><strong>Phone Number:</strong> <?= htmlspecialchars($user['phone_number'] ?? 'Not Provided') ?></p>
            </div>
        </div>

        <h2>Edit Information</h2>

        <!-- Hiển thị thông báo lỗi nếu có -->
        <?php if (isset($_SESSION['error_message'])): ?>
            <p style="color: green; font-weight: bold; font-size: 15px; text-align: center;">
                <?= $_SESSION['error_message']; ?>
            </p>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>

        <!-- Form cập nhật thông tin người dùng -->
        <form method="POST" action="edit_account_process.php">
            <label for="name">Full Name</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>

            <label for="phone_number">Phone Number</label>
            <input type="text" id="phone_number" name="phone_number" value="<?= htmlspecialchars($user['phone_number']) ?>">

            <button type="submit">Update</button>
        </form>
    </main>

    <?php include '../includes/footer.php'; ?>
</body>
</html>
