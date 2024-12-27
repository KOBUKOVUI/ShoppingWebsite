<?php
session_start();
require '../includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['action'])) {
    $order_id = intval($_POST['order_id']);
    $action = $_POST['action'];

    if ($action === 'confirm') {
        // Cập nhật trạng thái
        $stmt = $conn->prepare("UPDATE orders SET status = 'confirmed' WHERE id = ?");
        $stmt->bind_param("i", $order_id);
        if ($stmt->execute()) {
            $_SESSION['error_message'] = "Order #$order_id has been confirmed.";
        } else {
            $_SESSION['error_message'] = "Failed to confirm order #$order_id.";
        }
        $stmt->close();
    }
}

header("Location: admin_orders.php");
exit();
?>
