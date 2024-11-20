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

// Chuẩn bị câu lệnh SQL trả về số lượng giày mỗi loại
$category_names = ['Running Shoes', 'Soccer Shoes', 'Fashion Shoes'];
$quantities = [];
$sql = "
    SELECT 
        c.name AS category_name,
        COALESCE(SUM(p.stock), 0) AS total_quantity
    FROM 
        products p
    JOIN 
        categories c 
    ON 
        p.category_id = c.id
    WHERE 
        c.name = ?
    GROUP BY 
        c.name
";

// Mảng chứa tên các loại giày bạn muốn tính
$category_names = ['Running Shoes', 'Soccer Shoes', 'Fashion Shoes']; // Thêm các loại giày vào đây
$quantities = [];

// Lặp qua các tên loại giày và thực hiện truy vấn
foreach ($category_names as $category_name) {
    $stmt = $conn->prepare($sql); // Chuẩn bị câu lệnh SQL
    $stmt->bind_param('s', $category_name); // Gắn tham số
    $stmt->execute(); // Thực thi truy vấn
    $result = $stmt->get_result(); // Lấy kết quả

    // Lấy dữ liệu trả về từ truy vấn
    $row = $result->fetch_assoc();

    // Kiểm tra xem có kết quả không và lấy tổng số lượng
    if ($row) {
        $quantities[$category_name] = (int)$row['total_quantity']; // Lưu số lượng
    } else {
        $quantities[$category_name] = 0; // Nếu không có kết quả, gán 0
    }
}

// truy vấn tổng số lượng giày dựa theo cột stock 
$sql = "SELECT SUM(stock) AS total_quantity FROM products";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$total_quantity = $row['total_quantity'];
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product management</title>
    <link rel="icon" href="../icon/product_management.png" type = "image/x-icon">
    <link rel="stylesheet" href="..\css\product_management_styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

</head>
<body>
    <?php include '../includes/admin_header.php'; ?>

    <main>
        <section>
                <form class = "search_container" action="search_product.php" method="POST">
                    <input type="text" id="search_product" name="query" placeholder="Search products..." class="search_input" required>
                        <button type="submit" class="search_btn"><i class="fa fa-search"></i> <!-- Icon kính lúp --></button>
                </form>
        </section>
        <section class="button_container">
            <a href="add_product.php">
                <button class="custom_button">ADD NEW</button>
            </a>
            <a href="">
                <button class="custom_button">ADD STOCK</button>
            </a>
        </section>
        <section>
            <h2>Basic Statistics</h2>
            
            <table>
                <thead>
                    <tr>
                        <th>Statistics Type</th>
                        <th>Quantity</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Number of running shoes </td>
                        <td class ="quantity"><?php echo $quantities['Running Shoes']; ?></td>
                    </tr>
                    <tr>
                        <td>Number of fashion shoes</td>
                        <td class ="quantity" ><?php echo $quantities['Fashion Shoes']; ?></td>
                    </tr>
                    <tr>
                        <td>Number of soccer shoes</td>
                        <td class ="quantity" ><?php echo $quantities['Soccer Shoes']; ?></td>
                    </tr>
                    <tr>
                        <td class="total_row">Total</td>
                        <td class ="total_row" ><?php echo $total_quantity  ?></td>
                    </tr>
                </tbody>
            </table>
        </section>
        <br>
        <br>
        <hr>
        <h2>Products</h2>
        <!-- hiển thị các sản phẩm đang có-->
    <section>
        <?php
    // Câu lệnh SQL để lấy tất cả sản phẩm
    $sql = "
        SELECT p.id AS product_id, p.name AS product_name, p.brand, p.price, p.stock, p.image_url, c.name AS category_name
        FROM products p
        JOIN categories c ON p.category_id = c.id
    ";

    // Thực thi truy vấn
    $result = $conn->query($sql);

    // Kiểm tra xem có sản phẩm nào không

if ($result->num_rows > 0) {
    // Nếu có sản phẩm, hiển thị
    echo '<table class="table_product">'; // Class cho bảng
    echo '<thead>';
    echo '<tr class="tr_header">
            <th class="th_product_name">Product Name</th>
            <th class="th_brand">Brand</th>
            <th class="th_category">Category</th>
            <th class="th_price">Price</th>
            <th class="th_stock">Stock</th>
            <th class="th_product">Image</th>
            <th class="th_edit">Edit</th> <!-- Cột Edit -->
          </tr>';
    echo "</thead><tbody>";

    // Hiển thị từng sản phẩm
    while ($row = $result->fetch_assoc()) {
        echo "<tr class='tr_product'>";
        echo "<td class='td_product_name'>" . htmlspecialchars($row['product_name']) . "</td>";
        echo "<td class='td_brand'>" . htmlspecialchars($row['brand']) . "</td>";
        echo "<td class='td_category'>" . htmlspecialchars($row['category_name']) . "</td>"; // Hiển thị tên category
        echo "<td class='td_price'>" . number_format($row['price'], 0, ',', '.') . " VND</td>";
        echo "<td class='td_stock'>" . $row['stock'] . "</td>";
        echo "<td class='td_image'><img src='" . htmlspecialchars($row['image_url']) . "' alt='" . htmlspecialchars($row['product_name']) . "' class='product_image'></td>";
        // truyền = POST
        echo "<td class='td_edit'>
                <form action='edit_product.php' method='POST'>
                    <input type='hidden' name='product_id' value='" . $row['product_id'] . "'>
                    <input type='submit' value='Edit' class='edit_button'>
                </form>
              </td>";
        echo "</tr>";
    }

    echo "</tbody></table>";
} else {
    // Nếu không có sản phẩm nào
    echo "<p>No products available in the database.</p>";
}
        ?>
    </section>

    </main>

    <?php include '../includes/footer.php'; ?>
</body>
</html>