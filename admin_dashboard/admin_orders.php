<?php
session_start();
require '../includes/db_connect.php';

//ktra admin

// Lấy danh sách tất cả các hóa đơn từ cơ sở dữ liệu
$stmt = $conn->prepare("
    SELECT 
        o.id AS order_id, 
        o.user_id, 
        o.customer_name, 
        o.phone, 
        o.address, 
        o.total_amount, 
        o.status, 
        o.created_at, 
        GROUP_CONCAT(p.name SEPARATOR ', ') AS product_names, 
        GROUP_CONCAT(CONCAT(p.name, ' (x', oi.quantity, ')') SEPARATOR ', ') AS product_details
    FROM orders o
    LEFT JOIN order_items oi ON o.id = oi.order_id
    LEFT JOIN products p ON oi.product_id = p.id
    GROUP BY o.id
    ORDER BY o.created_at DESC
");

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Orders</title>
    <link rel="stylesheet" href="../css/admin_orders_styles.css">
    <link rel="icon" href="../icon/admin_orders.png" type="image/x-icon">
</head>
<body>
    <header>
        <?php include '../includes/admin_header.php'; ?>
    </header>
    <main>
        <div class="container">
            <h1>All Orders</h1>
            <?php
            if (isset($_SESSION['error_message'])) {
                        echo "<p style='color: red; font-size: 20px; text-align: center; font-weight: bold'>" . $_SESSION['error_message'] . "</p> <br>";
                        unset($_SESSION['error_message']);  // Xóa thông báo lỗi
                    }
            ?>
            <?php if ($result->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>User ID</th>
                            <th>Customer Name</th>
                            <th>Phone</th>
                            <th>Address</th>
                            <th>Products</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['order_id']); ?></td>
                                <td><?= htmlspecialchars($row['user_id']); ?></td>
                                <td><?= htmlspecialchars($row['customer_name']); ?></td>
                                <td><?= htmlspecialchars($row['phone']); ?></td>
                                <td><?= htmlspecialchars($row['address']); ?></td>
                                <td><?= htmlspecialchars($row['product_details']); ?></td>
                                <td><?= number_format($row['total_amount'], 0, ',', '.'); ?> VNĐ</td>
                                <td>
                                    <span class="
                                        <?= $row['status'] === 'pending' ? 'status-pending' : ($row['status'] === 'confirmed' ? 'status-confirmed' : 'status-other'); ?>">
                                        <?= htmlspecialchars($row['status']); ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($row['created_at']); ?></td>
                                <td>
                                    <!-- Chuyển trạng thái -->
                                    <?php if ($row['status'] === 'pending'): ?>
                                        <form action="update_order_status.php" method="POST" class="inline-form">
                                            <input type="hidden" name="order_id" value="<?= $row['order_id']; ?>">
                                            <button type="submit" name="action" value="confirm" class="confirm-btn">Confirm</button>
                                        </form>
                                    <?php endif; ?>

                                    <!-- Xóa đơn hàng -->
                                    <form action="delete_order.php" method="POST" class="inline-form">
                                        <input type="hidden" name="order_id" value="<?= $row['order_id']; ?>">
                                        <button type="submit" name="action" value="delete" class="delete-btn">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="no-orders">No orders available.</p>
            <?php endif; ?>
        </div>
    </main>
    <footer>
        <?php include '../includes/footer.php'; ?>
    </footer>
</body>
</html>
