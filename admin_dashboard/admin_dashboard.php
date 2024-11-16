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
//Lấy số lượng users
$query = "SELECT COUNT(*) AS total_users FROM users WHERE role = 'user'";
$result = $conn->query($query);
$row = $result->fetch_assoc();
$total_users = $row['total_users'];
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="icon" href="../icon/admin.png" type = "image/x-icon">
    <link rel="stylesheet" href="../css/admin_styles.css">
</head>
<body>
    <header>
        <h1>Admin Dashboard</h1>
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
            <h2>Basic Statistics</h2>
            <table>
                <thead>
                    <tr>
                        <th>Statistics Type</th>
                        <th>Quantity</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>s
                        <td>Number of Accounts</td>
                        <td class ="quantity"><?php echo $total_users; ?></td>
                    </tr>
                    <tr>
                        <td>Number of Products</td>
                        <td class ="quantity" ><?php //echo $total_orders; ?></td>
                    </tr>
                    <tr>
                        <td>Number of Orders</td>
                        <td class ="quantity" ><?php //echo $total_products; ?></td>
                    </tr>
                </tbody>
            </table>
        </section>
        
        
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
