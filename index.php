<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="css/login_register_styles.css">
</head>
<body id = "login_body">
    <div id = "login_container">
        <h2>Login</h2>
        <form id ="login_form" action="" method = "POST">
            <label for="email">Email</label>
            <input type="text" id="email" name="email" required>
            <br>
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
            <br>
            <button type = "submit">Login</button>
            <p>Don't have any account? <a  href="register.php">Register now</a></p>
        </form>

    </div>
</body>
</html>