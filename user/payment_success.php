<?php include '../includes/db_connect.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Success</title>
    <link rel="stylesheet" href="../css/payment_success_styles.css">
    <link rel="icon" href="../icon/payment_success.png" type="image/x-icon">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <main>
        <div class="success_message">
            <h1>Order Placed Successfully!</h1>
            <p>Your order has been successfully placed. Thank you for shopping with us.</p>
            <a href="../index.php" class="back_home_button">Back to Home</a>
        </div>
    </main>

    <?php include '../includes/footer.php'; ?>
</body>
</html>
