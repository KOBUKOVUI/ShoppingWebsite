<?php
ini_set('session.cookie_secure', 1); // chỉ cho phép cookie qua HTTPS
ini_set('session.cookie_httponly', 1); // không cho phép truy cập cookie qua JavaScript
ini_set('session.cookie_samesite', 'Strict'); // chỉ gửi cookie trong cùng một trang web
session_start();

// Kiểm tra quyền admin
if ($_SESSION['role'] != 'admin') {
    header("Location: /ShoppingWebsite/index.php");
    exit();
}

require '../includes/db_connect.php';

// Lấy danh sách người dùng từ cơ sở dữ liệu
$query = "SELECT * FROM users";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <link rel="icon" href="../icon/edit_account.png" type="image/x-icon">
    <link rel="stylesheet" href="../css/user_management_styles.css">
</head>
<body>
    <?php include '../includes/admin_header.php'; ?>

    <main>
        <section>
            <h2>All Users</h2>

            <!-- Hiển thị thông báo lỗi nếu có -->
            <?php if (isset($_SESSION['error_message'])): ?>
                <p style="color: red; font-weight: bold; font-size: 25px; text-align: center;">
                    <?= $_SESSION['error_message']; ?>
                </p>
                <?php unset($_SESSION['error_message']); ?>
            <?php endif; ?>

            <table>
                <thead>
                    <tr>
                        <th>User ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone Number</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['id']; ?></td>
                            <td><?= $row['name']; ?></td>
                            <td><?= $row['email']; ?></td>
                            <td><?= $row['phone_number']; ?></td>
                            <td><?= $row['role']; ?></td>
                            <td>
                                <!-- Edit Form -->
                                <form action="edit_user.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="id" value="<?= $row['id']; ?>">
                                    <button type="submit">Edit</button>
                                </form>

                                <!-- Delete Form -->
                                <form action="delete_user.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="id" value="<?= $row['id']; ?>">
                                    <button type="submit" onclick="return confirm('Are you sure you want to delete this user?');">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </section>
    </main>

    <?php include '../includes/footer.php'; ?>
</body>
</html>
