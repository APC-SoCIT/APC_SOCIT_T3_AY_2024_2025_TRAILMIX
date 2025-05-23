<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $pass = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM customers WHERE Email=? AND Password=?");
    $stmt->bind_param("ss", $email, $pass);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $_SESSION['user'] = $result->fetch_assoc();
        header('Location: index.php');
        exit();
    } else {
        $error = "Invalid email or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - NuttyLoves</title>
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
        .login-container {
            max-width: 400px;
            margin: 40px auto;
            padding: 30px;
            background-color: #f5f5f5;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .login-container h2 {
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
        input[type=email], input[type=password] {
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
        .error {
            color: red;
            text-align: center;
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
    <a href="products.php">Shop</a>
    <a href="cart.php">Cart</a>
    <a href="login.php">Login</a>
</nav>

<div class="login-container">
    <h2>Login</h2>
    <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
    <form method="post">
        <label for="email">Email Address</label>
        <input type="email" id="email" name="email" required>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>

        <button type="submit">Login</button>
    </form>
</div>

<footer>
    &copy; <?= date('Y') ?> NuttyLoves. All rights reserved.
</footer>

</body>
</html>
