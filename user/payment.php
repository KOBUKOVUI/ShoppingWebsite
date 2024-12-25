<?php
session_start();
require '../includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'], $_POST['quantity'], $_POST['name'], $_POST['address'], $_POST['phone'])) {
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);
    $name = htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8');
    $address = htmlspecialchars($_POST['address'], ENT_QUOTES, 'UTF-8');
    $phone = htmlspecialchars($_POST['phone'], ENT_QUOTES, 'UTF-8');

    // Truy vấn thông tin sản phẩm
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $product = $result->fetch_assoc();
    } else {
        die("Sản phẩm không tồn tại.");
    }
} else {
    die("Thông tin không hợp lệ.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment for "<?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?>"</title>
    <link rel="icon" href="../icon/payment.png" type="image/x-icon">
    <link rel="stylesheet" href="../css/payment_styles.css">
</head>
<body>
    <header>
        <?php include '../includes/header.php'; ?>
    </header>
    <main>
        <h1 class = "title">Select Payment Method</h1>
        <div class="order_summary">
            <h2>Order Summary</h2>
            <p><strong>Product:</strong> <?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?></p>
            <p><strong>Quantity:</strong> <?= htmlspecialchars($quantity, ENT_QUOTES, 'UTF-8'); ?></p>
            <p><strong>Total:</strong> <span class="total_price"><?= number_format($product['price'] * $quantity, 0, ',', '.'); ?> VNĐ</span></p>
            <p><strong>Full name:</strong> <?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?></p>
            <p><strong>Address:</strong> <?= htmlspecialchars($address, ENT_QUOTES, 'UTF-8'); ?></p>
        </div>
        <div class="form_container">
            <form action="payment_process.php" method="POST">
                <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['id'], ENT_QUOTES, 'UTF-8'); ?>">
                <input type="hidden" name="quantity" value="<?= htmlspecialchars($quantity, ENT_QUOTES, 'UTF-8'); ?>">
                <input type="hidden" name="name" value="<?= $name; ?>">
                <input type="hidden" name="address" value="<?= $address; ?>">
                <input type="hidden" name="phone" value="<?= $phone; ?>">
        
            <div class="form_group">
                <label for="payment_method">Choose Payment Method:</label>
                <select id="payment_method" name="payment_method" required>
                    <option value="momo">MoMo Wallet</option>
                    <option value="cash">Cash on Delivery</option>
                </select>
            </div>
            <button type="submit">Proceed to Payment</button>
            </form>
        </div>

    </main>
    <footer>
        <?php include '../includes/footer.php'; ?>
    </footer>
</body>
</html>
