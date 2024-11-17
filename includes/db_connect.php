<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "onlineShop";

//kết nối
$conn = new mysqli($servername, $username, $password, $dbname); 


//kiểm tra kết nối 
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>