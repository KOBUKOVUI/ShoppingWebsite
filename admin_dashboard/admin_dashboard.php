<?php
ini_set('session.cookie_secure', 1); // chỉ cho phép cookie qua HTTPS
ini_set('session.cookie_httponly', 1); // không cho phép truy cập cookie qua JavaScript
ini_set('session.cookie_samesite', 'Strict'); // chỉ gửi cookie trong cùng một trang web
session_start();

// Kiểm tra xem người dùng có phải là admin không
if ($_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

require '../includes/db_connect.php';
//Lấy số lượng users
$query = "SELECT COUNT(*) AS total_users FROM users WHERE role = 'user'";
$result = $conn->query($query);
$row = $result->fetch_assoc();
$total_users = $row['total_users'];

// truy vấn tổng số lượng giày dựa theo cột stock 
$sql = "SELECT SUM(stock) AS total_quantity FROM products";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$total_quantity = $row['total_quantity'];
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
    <?php include '../includes/admin_header.php'; ?>

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
                    <tr>
                        <td>Number of Accounts</td>
                        <td class ="quantity"><?php echo $total_users; ?></td>
                    </tr>
                    <tr>
                        <td>Number of Products</td>
                        <td class ="quantity" ><?php echo $total_quantity; ?></td>
                    </tr>
                    <tr>
                        <td>Number of Orders</td>
                        <td class ="quantity" ><?php //echo $total_products; ?></td>
                    </tr>
                </tbody>
            </table>
        </section>
    </main>

    <?php include '../includes/footer.php'; ?>
</body>
</html>
