<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body id = "register_body">
    <div id = "register_container">
        <form id = "register_form" action="register_process.php" method = "POST">
            <h2>Register</h2>
             <label for="email">Email</label>
             <input type="text" id = "email" name = "email" required>
             <br>
             <label for="name">Name</label>
             <input type="text" id = "name" name = "name">
             <label for="password">Password</label>
             <input type="password" id = "password" name = "password" required>
             <br>
             <label for="confirm_password">Confirm password</label>
             <input type="password" id = "confirm_password" name = "confirm_password" required>
             <br>
             <label for="number">Phone number</label>
             <input type="text" id = "phone_number" name = "phone_number" required>
            <button type="submit">Register</button>
            <p>Already have an account <a href="index.php">Login now</a></p>

        </form>
    </div>
</body>
</html>