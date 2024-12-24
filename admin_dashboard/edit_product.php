<?php
ini_set('session.cookie_secure', 1); // chỉ cho phép cookie qua HTTPS
ini_set('session.cookie_httponly', 1); // không cho phép truy cập cookie qua JavaScript
ini_set('session.cookie_samesite', 'Strict'); // chỉ gửi cookie trong cùng một trang web
session_start();

// Kiểm tra xem người dùng có phải là admin không
if ($_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

require '../includes/db_connect.php';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $product_id = intval($_POST['product_id']); // Ép kiểu để đảm bảo an toàn
    // dùng inner join để tạo ra 1 bảng mới với tham số chung là id của bảng category
    $sql = "
        SELECT p.*, c.name AS category_name 
        FROM products p
        INNER JOIN categories c ON p.category_id = c.id 
        WHERE p.id = ?
    ";
    $stmt = $conn->prepare($sql); 
    $stmt->bind_param("i", $product_id);
    $stmt->execute(); 
    $result = $stmt->get_result(); 
    if ($result->num_rows > 0) {
        // Lấy thông tin sản phẩm và tên category
        $row = $result->fetch_assoc();
    }
    //lấy category 
    $categories = [];
    $sql_category = "SELECT id, name FROM categories";
    $result_category = $conn->query($sql_category);
    if ($result_category->num_rows > 0) {
    while ($row_category = $result_category->fetch_assoc()) {
        $categories[] = $row_category;
    }
}
    
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit product</title>
    <link rel="icon" href="../icon/edit_product.png" type="image/x-icon">
    <link rel="stylesheet" href="../css/edit_product_styles.css">
</head>
<body>
    <?php include '../includes/admin_header.php'; ?>

    <main >
            <div class="form_container">
                <h1>EDIT PRODUCT</h1>
                <form action="edit_product_process.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" id="product_id" name="product_id" value="<?= $row['id']?>">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" value="<?= $row['name']?>" required>

                    <label for="category">Category:</label>
                        <select name="category_id" id="category" required>
                        <option value=""> <?php echo $row['category_name']; ?> </option>
                            <?php foreach ($categories as $category): ?>
                            <option value="<?= htmlspecialchars($category['id'], ENT_QUOTES, 'UTF-8'); ?>">
                            <?= htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8'); ?>
                            </option>
                            <?php endforeach; ?>
                </select>

                    <label for="price">Price (VNĐ)</label>
                    <?php $price = number_format($row['price'], 0, ',', '.'); ?>
                    <input type="text" name="price" id="price" required oninput="formatPrice(this)" value="<?php echo htmlspecialchars($price); ?>">
                    
                    <label for="brand">Brand:</label>
                    <select name="brand" id="brand" required>
                        <option value=""> <?php echo $row['brand'] ?> </option>
                        <option value="nike">Nike</option>
                        <option value="addidas">Addidas</option>
                        <option value="puma">Puma</option>
                    </select>

                    <label for="stock">Stock:</label>
                    <input type="number" name="stock" id="stock" required min="0">     
                               
                    <label for="description">Description:</label>
                    <textarea name="description" id="description" rows="5"><?php echo $row['description'] ?> </textarea>
                    <?php echo "<img src='" . $row['image_url'] . "' alt='Product Image'>"; ?>
                    <br>
                    <label for="image">Image:</label>
                    <label for="image" class="custom_file_upload">Choose File</label>
                    <input type="file" name="image" id="image" accept="image/*">
                    <br>
                    <span id="file_name">No file chosen</span>
                    <br>
                    <button type="submit">Update</button>
                </form>
            </div>
    </main>

    <?php include '../includes/footer.php'; ?>
</body>
</html>


<script>
    function formatPrice(input) {
        let value = input.value.replace(/\D/g, ''); // Loại bỏ ký tự không phải số
        input.value = Number(value).toLocaleString('vi-VN'); // Thêm dấu chấm phân cách
    }
    document.getElementById('image').addEventListener('change', function () {
        const fileName = this.files[0]?.name || 'No file chosen';
        document.getElementById('file_name').textContent = fileName;
    });
</script>