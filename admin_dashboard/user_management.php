<?php
ini_set('session.cookie_secure', 1); // chỉ cho phép cookie qua HTTPS
ini_set('session.cookie_httponly', 1); // không cho phép truy cập cookie qua JavaScript
ini_set('session.cookie_samesite', 'Strict'); // chỉ gửi cookie trong cùng một trang web
session_start();

// Kiểm tra xem người dùng có phải là admin không
if ($_SESSION['role'] != 'admin') {
    header("Location: /ShoppingWebsite/index.php");
    exit();
}

require '../db_connect.php';

// Lấy danh sách người dùng từ cơ sở dữ liệu
$query = "SELECT * FROM users ";
$result = $conn->query($query);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User management</title>
    <link rel="icon" href="../icon/edit_account.png" type = "image/x-icon">
    <link rel="stylesheet" href="../css/user_management_styles.css">
</head>
<body>
<header>
        <h1>User management</h1>
        <nav>
            <a href="admin_dashboard.php">Home</a>
            <a href="user_management.php">User management </a>
            <a href="product_management.php">Product management</a>
            <a href="orders.php">Order management</a>
            <a id = 'header_logout' href="../logout.php">Log out</a>
        </nav>
    </header>

    <main>
    <section>
            <h2>All Users</h2>
            <?php
            if (isset($_SESSION['error_message'])) {
                echo "<p style='color: red;font-weight: bold; font-size: 25px; text-align: center;'>" . $_SESSION['error_message'] . "</p>";
                unset($_SESSION['error_message']);  // Xóa thông báo lỗi
            }
            ?>
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
                    <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['name']; ?></td>
                        <td><?php echo $row['email']; ?></td>
                        <td><?php echo $row['phone_number']; ?></td>
                        <td><?php echo $row['role']; ?></td>
                        <td>
                            <!-- Edit Form -->
                            <form class = "form_action"action="edit_user.php" method="POST" style="display:inline;">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <button type="submit">Edit</button>
                            </form>

                            <!-- Delete Form -->
                            <form class = "form_action"action="delete_user.php" method="POST" style="display:inline;">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <button type="submit" onclick="return confirm('Are you sure you want to delete this user?');">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </section>

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