<?php
session_start();
require '../includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['action'])) {
    $order_id = intval($_POST['order_id']);
    $action = $_POST['action'];

    if ($action === 'delete') {
        $conn->begin_transaction();
        try {
            // Xóa sản phẩm trong order_items
            $stmt = $conn->prepare("DELETE FROM order_items WHERE order_id = ?");
            $stmt->bind_param("i", $order_id);
            $stmt->execute();
            $stmt->close();

            // Xóa đơn hàng
            $stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
            $stmt->bind_param("i", $order_id);
            $stmt->execute();
            $stmt->close();

            $conn->commit();
            $_SESSION['error_message'] = "Order #$order_id has been deleted.";
        } catch (Exception $e) {
            $conn->rollback();
            $_SESSION['error_message'] = "Failed to delete order #$order_id.";
        }
    }
}

header("Location: admin_orders.php");
exit();
?>
