<?php
session_start();
require '../includes/db_connect.php';

// Kiểm tra quyền truy cập
if ($_SESSION['role'] != 'admin') {
    header("Location: /ShoppingWebsite/index.php");
    exit();
}

// Kiểm tra xem người dùng đã chọn user để sửa chưa
if (!isset($_POST['id'])) {
    echo "User ID not specified!";
    exit();
}

// Kiểm tra có là tài khoản admin
if (isset($_POST['id'])) {
    $user_id = $_POST['id'];

    // Kiểm tra k cho sửa tk admin
    $query = "SELECT role FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    if ($user && $user['role'] === 'admin') {
        $_SESSION['error_message'] = "You cannot edit an admin account! ";
        header ("Location: user_management.php");
        exit();
    }
}
$user_id = $_POST['id'];

// Lấy thông tin người dùng từ cơ sở dữ liệu
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    echo "User not found.";
    exit();
}

$user = $result->fetch_assoc();

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link rel="stylesheet" href="../css/edit_user_styles.css">
</head>
<body>

<?php include '../includes/admin_header.php'; ?>

<main>
    <h2>Edit User Information</h2>

    <!-- Hiển thị thông tin người dùng -->
    <div class="form_container">
    <form action="edit_user_process.php" method="POST">
        <input type="hidden" name="id" value="<?= htmlspecialchars($user['id']) ?>">

        <label for="name">Full Name</label>
        <input type="text" id="name" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>

        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

        <label for="phone_number">Phone Number</label>
        <input type="text" id="phone_number" name="phone_number" value="<?= htmlspecialchars($user['phone_number']) ?>">

        <label for="role">Role</label>
        <select id="role" name="role" required>
            <option value="user" <?= ($user['role'] == 'user') ? 'selected' : ''; ?>>User</option>
            <option value="admin" <?= ($user['role'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
        </select>

        <button type="submit">Update</button>
    </form>
</div>


</main>

<?php include '../includes/footer.php'; ?>

</body>
</html>
