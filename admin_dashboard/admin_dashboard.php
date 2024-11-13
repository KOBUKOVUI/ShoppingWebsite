<?php
ini_set('session.cookie_secure', 1); // chỉ cho phép cookie qua HTTPS
ini_set('session.cookie_httponly', 1); // không cho phép truy cập cookie qua JavaScript
ini_set('session.cookie_samesite', 'Strict'); // chỉ gửi cookie trong cùng một trang web
session_start();

// Kiểm tra xem người dùng có phải là admin không
if ($_SESSION['role'] != 'admin') {
    header("Location: index.php");
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
            <a href="user_management.php">Quản lý người dùng</a>
            <a href="product_management.php">Quản lý sản phẩm</a>
            <a href="orders.php">Quản lý đơn hàng</a>
            <a id = 'header_logout' href="logout.php">Đăng xuất</a>
        </nav>
    </header>

    <main>
        <section>
            <h2>Thống kê cơ bản</h2>
            <table>
                <thead>
                    <tr>
                        <th>Loại Thống Kê</th>
                        <th>Số Lượng</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Số lượng tài khoản</td>
                        <td class ="quantity"><?php echo $total_users; ?></td>
                    </tr>
                    <tr>
                        <td>Số lượng đơn hàng</td>
                        <td class ="quantity" ><?php //echo $total_orders; ?></td>
                    </tr>
                    <tr>
                        <td>Số lượng sản phẩm</td>
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
            <a href="">Chính sách bảo mật</a> |
            <a href="">Điều khoản sử dụng</a> |
            <a href="">Liên hệ</a>
        </div>
        <p id="footer_social">
            Theo dõi chúng tôi:
            <a href="" target="_blank">Facebook</a> |
            <a href="" target="_blank">Twitter</a> |
            <a href="" target="_blank">Instagram</a>
        </p>
    </div>
</footer>

</body>
</html>
