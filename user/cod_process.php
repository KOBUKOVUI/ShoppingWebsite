<?php
session_start();
require '../includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'], $_POST['quantity'], $_POST['name'], $_POST['address'], $_POST['phone'], $_POST['payment_method'])) {
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);
    $name = htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8');
    $address = htmlspecialchars($_POST['address'], ENT_QUOTES, 'UTF-8');
    $phone = htmlspecialchars($_POST['phone'], ENT_QUOTES, 'UTF-8');
    $payment_method = $_POST['payment_method'];

    // Truy vấn thông tin sản phẩm
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $product = $result->fetch_assoc();
        $total_amount = $product['price'] * $quantity;

        if ($payment_method === 'cash'){
            // Thêm vào bảng orders
            $order_stmt = $conn->prepare("INSERT INTO orders (user_id, customer_name, phone, address, total_amount, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())");
            $user_id = $_SESSION['user_id'];
            $status = 'pending'; // Trạng thái mặc định cho COD
            $order_stmt->bind_param("isssds", $user_id, $name, $phone, $address, $total_amount, $status);
            $order_stmt->execute();
            $order_id = $order_stmt->insert_id; 

            // Thêm vào bảng order_items
            $item_stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price, total) VALUES (?, ?, ?, ?, ?)");
            $item_total = $product['price'] * $quantity;
            $item_stmt->bind_param("iiidd", $order_id, $product_id, $quantity, $product['price'], $item_total);
            $item_stmt->execute();

            $product_stmt = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
            $product_stmt->bind_param("ii", $quantity, $product_id);
            $product_stmt->execute();


            // Sau khi xử lý đơn hàng thành công
            header("Location: /ShoppingWebsite/user/payment_success.php");
            exit();

            
        } else {
            echo "Unsupported payment method.";
            //phần dành cho momo
        }
    } else {
        die("Product not found.");
    }
} else {
    die("Invalid request.");
}
?>