<?php
ini_set('session.cookie_secure', 1); // Chỉ cho phép cookie qua HTTPS
ini_set('session.cookie_httponly', 1); // Ngăn truy cập cookie qua JavaScript
ini_set('session.cookie_samesite', 'Strict'); // Chỉ gửi cookie trong cùng một trang web

session_start();
require '../includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $product_id = intval($_POST['id']);

    
    $stmt = $conn->prepare("SELECT p.id, p.name, p.description, p.price, p.stock, p.image_url, p.brand, c.name AS category 
                            FROM products p 
                            JOIN categories c ON p.category_id = c.id 
                            WHERE p.id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $product = $result->fetch_assoc();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?> - Product Details</title>
    <link rel="stylesheet" href="../css/product_detail_styles.css">
    <link rel="icon" href="../icon/product_details.png" type="image/x-icon">
</head>
<body>
    <header>
        <?php include '../includes/header.php'; ?>
    </header>
    <main>
    <div class="product_detail">
        <img src="<?= htmlspecialchars($product['image_url'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?>">
        
        <div class="product_info">
            <h2><?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?></h2>
            <p class="brand"><strong>Brand:</strong> <?= htmlspecialchars($product['brand'], ENT_QUOTES, 'UTF-8'); ?></p>
            <p class="category"><strong>Category:</strong> <?= htmlspecialchars($product['category'], ENT_QUOTES, 'UTF-8'); ?></p>
            <p class="price"><strong>Price:</strong> <?= number_format($product['price'], 0, ',', '.'); ?> VNĐ</p>
            <p class="description"><?= htmlspecialchars($product['description'], ENT_QUOTES, 'UTF-8'); ?></p>
            
            <form action="buy_now.php" method="POST">
                <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['id'], ENT_QUOTES, 'UTF-8'); ?>">
                <div class="quantity_container">
                    <label for="quantity">Quantity:</label>
                    <input type="number" id="quantity" name="quantity" min="1" value="1">
                </div>
                <button type="submit">Buy now</button>
            </form>
        </div>
    </div>
</main>

    <footer>
     <?php include '../includes/footer.php'; ?>
    </footer>
</body>
</html>
