<?php
require '../includes/db_connect.php';
session_start();

// Kiểm tra xem người dùng đã thanh toán thành công
if (isset($_GET['resultCode']) && $_GET['resultCode'] == '0') {
    $orderId = $_GET['orderId'];
    $transId = $_GET['transId'];
    $amount = $_GET['amount'];
    $name = $_SESSION['momo_payment']['name'];
    $address = $_SESSION['momo_payment']['address'];
    $phone = $_SESSION['momo_payment']['phone'];
    $product_id = $_SESSION['momo_payment']['product_id'];
    $quantity = $_SESSION['momo_payment']['quantity'];

    // Lưu thông tin vào bảng orders
    $stmt = $conn->prepare("INSERT INTO orders (product_id, quantity, total_amount, customer_name, address, phone, payment_method, momo_trans_id, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    $payment_method = 'momo';
    $status = 'paid';
    $stmt->bind_param("iidssssis", $product_id, $quantity, $amount, $name, $address, $phone, $payment_method, $transId, $status);
    $stmt->execute();

    echo "Thanh toán thành công. Đơn hàng đã được lưu!";
} else {
    echo "Thanh toán thất bại.";
}
?>
