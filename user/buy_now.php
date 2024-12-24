<?php
ini_set('session.cookie_secure', 1); // Chỉ cho phép cookie qua HTTPS
ini_set('session.cookie_httponly', 1); // Ngăn truy cập cookie qua JavaScript
ini_set('session.cookie_samesite', 'Strict'); // Chỉ gửi cookie trong cùng một trang web

session_start();
require '../includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'], $_POST['quantity'])) {
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);

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
    <title>Buy "<?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?>"</title>
    <link rel="stylesheet" href="../css/buy_now_styles.css">
    <link rel="icon" href="../icon/buy_now.png" type="image/x-icon">
</head>
<body>
    <header>
        <?php include '../includes/header.php'; ?>
    </header>
    <main>
        <h1>Complete Your Purchase</h1>
        <div class="order_summary">
            <h2>Order Summary</h2>
            <div class="product_image_container">
                <img src="<?= htmlspecialchars($product['image_url'], ENT_QUOTES, 'UTF-8'); ?>" 
                    alt="<?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?>" 
                    class="product_image" 
                    onerror="this.src='../images/default_product.png';">
            </div>
            <p><strong>Product:</strong> <?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?></p>
            <p><strong>Price per unit:</strong> <?= number_format($product['price'], 0, ',', '.'); ?> VNĐ</p>
            <p><strong>Quantity:</strong> <?= $quantity; ?></p>
            <p><strong>Total:</strong> <span class="total_price"><?= number_format($product['price'] * $quantity, 0, ',', '.'); ?> VNĐ</span></p>

        </div>
        <form action="buy_now_process.php" method="POST">
            <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['id'], ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="quantity" value="<?= htmlspecialchars($quantity, ENT_QUOTES, 'UTF-8'); ?>">
            
            <div class="form_group">
                <label for="name">Full name:</label>
                <textarea id="name" name="name" rows="4" required></textarea>
            </div>
            <div class="form_group">
                <label for="address">Address:</label>
                <textarea id="address" name="address" rows="4" required></textarea>
            </div>
            <div class="form_group">
                <label for="phone">Phone Number:</label>
                <input type="tel" id="phone" name="phone" pattern="[0-9]{10}" required>
            </div>
            <button type="submit">Confirm and Buy</button>
        </form>
    </main>
    <footer>
        <?php include '../includes/footer.php'; ?>
    </footer>
</body>
</html>
