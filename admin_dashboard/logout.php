<?php
session_start(); // Khởi tạo lại session

// Hủy tất cả các session
session_unset(); 

// Hủy session ID
session_destroy(); 

// Chuyển hướng về trang đăng nhập
header("Location: ../index.php");
exit();
?>
