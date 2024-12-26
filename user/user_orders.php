<?php
session_start();
require '../includes/db_connect.php';

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("
    SELECT o.id AS order_id, o.customer_name, o.phone, o.address, o.total_amount, o.status, o.created_at,
           p.name AS product_name, oi.quantity
    FROM orders o
    JOIN order_items oi ON o.id = oi.order_id
    JOIN products p ON oi.product_id = p.id
    WHERE o.user_id = ?
    ORDER BY o.created_at DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Orders</title>
    <link rel="stylesheet" href="../css/user_orders_styles.css">
    <link rel="icon" href="../icon/user_orders.png" type="image/x-icon">
</head>
<body>
    <header>
        <?php include '../includes/header.php'; ?>
    </header>
    <main>
        <div class="container">
            <h1>Your Orders</h1>
            <?php if ($result->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Product name</th>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Address</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                            <th>Created At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['product_name']); ?></td>
                                <td><?= htmlspecialchars($row['customer_name']); ?></td>
                                <td><?= htmlspecialchars($row['phone']); ?></td>
                                <td><?= htmlspecialchars($row['address']); ?></td>
                                <td><?= number_format($row['total_amount'], 0, ',', '.'); ?> VNĐ</td>
                                <td>
                                    <span class="
                                        <?= $row['status'] === 'pending' ? 'status-pending' : ($row['status'] === 'confirmed' ? 'status-confirmed' : 'status-other'); ?>">
                                        <?= htmlspecialchars($row['status']); ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($row['created_at']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="no-orders">You have no orders yet.</p>
            <?php endif; ?>
        </div>
    </main>
    <footer>
        <?php include '../includes/footer.php'; ?>
    </footer>
</body>
</html>
