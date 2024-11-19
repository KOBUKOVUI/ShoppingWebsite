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

//Lấy số lượng products
$query = "SELECT COUNT(*) AS total_products FROM products ";
$result = $conn->query($query);
$row = $result->fetch_assoc();
$total_products = $row['total_products'];
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product management</title>
    <link rel="icon" href="../icon/product_management.png" type = "image/x-icon">
    <link rel="stylesheet" href="..\css\product_management_style.css">
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
                        <td>Number of running shoes </td>
                        <td class ="quantity"><?php //echo $total_users; ?></td>
                    </tr>
                    <tr>
                        <td>Number of soccer shoes</td>
                        <td class ="quantity" ><?php //echo $total_orders; ?></td>
                    </tr>
                    <tr>
                        <td>Number of fashion shoes</td>
                        <td class ="quantity" ><?php //echo $total_products; ?></td>
                    </tr>
                    <tr>
                        <td id="total_row">Total</td>
                        <td class ="quantity" ><?php echo $total_products; ?></td>
                    </tr>
                </tbody>
            </table>
        </section>
        <section>
            <a href="add_product.php">
                <button>ADD PRODUCT</button>
            </a>

        </section>
    </main>

    <?php include '../includes/footer.php'; ?>
</body>
</html>