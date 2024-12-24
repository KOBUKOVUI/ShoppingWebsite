<?php
require '../includes/db_connect.php';

// Truy vấn lấy danh sách tất cả sản phẩm
$sql = "SELECT id, name, price, image_url, brand FROM products";
$result = $conn->query($sql);

$products = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title> 
    <link rel="stylesheet" href="../css/home_styles.css">
    <link rel="icon" href="../icon/shoe.png" type="image/x-icon">
</head> 
<body>
   
    <?php include '../includes/header.php'; ?>

    <main>
        <h1>Our Products</h1>
        <div class="product_grid">
            <?php foreach ($products as $product): ?>
                <div class="product_card">
                    <img src="<?= htmlspecialchars($product['image_url'], ENT_QUOTES, 'UTF-8'); ?>" alt="Product Image">
                    <h3><?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?></h3>
                    <p class="brand"><?= htmlspecialchars($product['brand'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <p class="price"><?= number_format($product['price'], 0, ',', '.') ?> VNĐ</p>
                    <form action="product_detail.php" method="POST" class="view_button_form">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($product['id'], ENT_QUOTES, 'UTF-8'); ?>">
                        <button type="submit" class="view_button">Buy now</button>
                    </form>
                </div>

            <?php endforeach; ?>
        </div>
    </main>

    <?php include '../includes/footer.php'; ?>
</body>
</html>
