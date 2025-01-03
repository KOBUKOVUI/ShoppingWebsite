<?php
ini_set('session.cookie_secure', 1); // Chỉ cho phép cookie qua HTTPS
ini_set('session.cookie_httponly', 1); // Ngăn truy cập cookie qua JavaScript
ini_set('session.cookie_samesite', 'Strict'); // Chỉ gửi cookie trong cùng một trang web
session_start();

// Kiểm tra xem người dùng có phải là admin không
if ($_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

require '../includes/db_connect.php';

// Lấy danh sách categories
$categories = [];
$sql = "SELECT id, name FROM categories";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <link rel="icon" href="../icon/add_product.png" type="image/x-icon">
    <link rel="stylesheet" href="../css/add_product_styles.css">
</head>
<body>
    <?php include '../includes/admin_header.php'; ?>

    <main>
        <h2 id = "main_title">ADD PRODUCT</h2>
        <div id = "form_container">
            <form action="add_product_process.php" method="POST" enctype="multipart/form-data">
                <!-- thông báo các lỗi -->
                <?php
                if (isset($_SESSION['error_message'])) {
                    echo "<p style='color: red; font-size: 20px; text-align: left; font-weight: bold'>" . $_SESSION['error_message'] . "</p> <br>";
                    unset($_SESSION['error_message']);  // Xóa thông báo lỗi
                }
                ?>
                <label for="name">Product Name:</label>
                <input type="text" name="name" id="name" required>

                <label for="category">Category:</label>
                <select name="category_id" id="category" required>
                    <option value="">-- Select a Category --</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= htmlspecialchars($category['id'], ENT_QUOTES, 'UTF-8'); ?>">
                            <?= htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                    
                    <label for="brand">Brand:</label>
                    <select name="brand" id="brand" onchange="updateColorBox()" required>
                        <option value="">-- Select a Brand --</option>
                        <option value="nike">Nike</option>
                        <option value="addidas">Addidas</option>
                        <option value="puma">Puma</option>
                    </select>
                    
                    <label for="price">Price (VNĐ):</label>
                    <input type="text" name="price" id="price" required oninput="formatPrice(this)">
                    
                <label for="stock">Stock:</label>
                <input type="number" name="stock" id="stock" required min="0">

                <label for="description">Description:</label>
                <textarea name="description" id="description" rows="5"></textarea>

                <label for="image">Image:</label>
                <label for="image" class="custom_file_upload">Choose File</label>
                <input type="file" name="image" id="image" accept="image/*">
                <br>
                <span id="file_name">No file chosen</span>
                <br>
                <br>
                <button type="submit">Add Product</button>
            </form>
        </div>
    </main>

    <?php include '../includes/footer.php'; ?>
</body>

<script>
    // Định dạng giá với dấu chấm
    function formatPrice(input) {
        let value = input.value.replace(/\D/g, ''); // Loại bỏ ký tự không phải số
        input.value = Number(value).toLocaleString('vi-VN'); // Thêm dấu chấm phân cách
    }

    // Cập nhật màu nền ô hiển thị
    function updateColorBox() {
        const selectElement = document.getElementById('color');
        const colorBox = document.getElementById('colorBox');
        const selectedColor = selectElement.value; // Lấy màu từ lựa chọn

        if (selectedColor) {
            colorBox.style.backgroundColor = selectedColor; // Cập nhật màu nền
        } else {
            colorBox.style.backgroundColor = 'transparent'; // Trả về trạng thái mặc định
        }
    }

    document.getElementById('image').addEventListener('change', function () {
        const fileName = this.files[0]?.name || 'No file chosen';
        document.getElementById('file_name').textContent = fileName;
    });


</script>
</html>
