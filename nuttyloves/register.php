<?php
session_start();
include 'config.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);
    $address  = trim($_POST['address']);

    $check = $conn->prepare("SELECT * FROM users WHERE Email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        $error = "Email is already registered.";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (Name, Email, Password, Address, Role, Verified) VALUES (?, ?, ?, ?, 'customer', 0)");
		$stmt->bind_param("ssss", $name, $email, $password, $address);

        if ($stmt->execute()) {
            $success = "Account created successfully. Redirecting to login...";
            header("refresh:2;url=login.php");
        } else {
            $error = "Registration failed. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - NuttyLoves</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            background-color: #ffffff;
            color: #333;
        }
        header {
            background-color: #2e7d32;
            padding: 20px;
            text-align: center;
            color: white;
        }
        nav {
            background-color: #c62828;
            display: flex;
            justify-content: center;
            padding: 10px;
        }
        nav a {
            color: white;
            margin: 0 15px;
            text-decoration: none;
            font-weight: bold;
        }
        nav a:hover {
            text-decoration: underline;
        }
        .register-container {
            max-width: 500px;
            margin: 40px auto;
            padding: 30px;
            background-color: #f5f5f5;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .register-container h2 {
            text-align: center;
            color: #2e7d32;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        label {
            font-weight: bold;
        }
        input, textarea {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            background-color: #c62828;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
        }
        button:hover {
            background-color: #b71c1c;
        }
        .message {
            text-align: center;
            margin-top: 10px;
            font-weight: bold;
        }
        .message.success {
            color: #2e7d32;
        }
        .message.error {
            color: #c62828;
        }
        footer {
            text-align: center;
            padding: 20px;
            background-color: #2e7d32;
            color: white;
            margin-top: 40px;
        }
    </style>
</head>
<body>

<header>
    <h1>NuttyLoves</h1>
    <p>Natural Goodness in Every Bite</p>
</header>

<nav>
    <a href="index.php">Home</a>
    <a href="shop.php">Shop</a>
    <a href="cart.php">Cart</a>
	<a href="my_orders.php">My Orders</a>
	<a href="account_settings.php">My Account</a>
    <a href="login.php">Login</a>
</nav>

<div class="register-container">
    <h2>Create an Account</h2>
    <?php
    if ($success) echo "<p class='message success'>$success</p>";
    if ($error) echo "<p class='message error'>$error</p>";
    ?>
    <form method="post">
        <label for="name">Full Name</label>
        <input type="text" name="name" id="name" required>

        <label for="email">Email Address</label>
        <input type="email" name="email" id="email" required>

        <label for="password">Password</label>
        <input type="password" name="password" id="password" required>

        <label for="address">Address</label>
        <textarea name="address" id="address" rows="3" required></textarea>

        <button type="submit">Register</button>
    </form>
</div>

<footer>
    &copy; <?= date('Y') ?> NuttyLoves. All rights reserved.
</footer>

</body>
</html>
