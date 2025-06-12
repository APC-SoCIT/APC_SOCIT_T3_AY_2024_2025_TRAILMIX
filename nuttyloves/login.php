<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $pass = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE Email=? AND Password=? AND Verified=1");
	$error = "Account not verified yet. Please wait for admin approval.";
    $stmt->bind_param("ss", $email, $pass);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        $_SESSION['user'] = $user;

        if ($user['Role'] === 'admin') {
            header('Location: admin_dashboard.php');
        } else {
            header('Location: index.php');
        }
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
	  align-items: center;
	  padding: 10px;
	  position: relative;
	}

	.nav-center {
	  display: flex;
	  gap: 15px;
	}

	.nav-right {
	  position: absolute;
	  right: 15px;
	  display: flex;
	  align-items: center;
	  gap: 10px;
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
  <div class="nav-center">
    <a href="index.php">Home</a>
    <a href="shop.php">Shop</a>
    <a href="cart.php">Cart</a>
  </div>
  <div class="nav-right">
    <?php if (isset($_SESSION['user'])): ?>
      <span style="color: white; margin-right: 15px;">
        Welcome, <?= htmlspecialchars($_SESSION['user']['Name']) ?>
      </span>
      <a href="logout.php">Logout</a>
    <?php else: ?>
      <a href="login.php">Login</a>
    <?php endif; ?>
  </div>
</nav>

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

    <div class="register-link" style="text-align:center; margin-top: 20px;">
        <p>Don't have an account?</p>
        <a href="register.php" style="background-color: #2e7d32; color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none; font-weight: bold;">Register Account</a>
    </div>
</div>

<footer>
    &copy; <?= date('Y') ?> NuttyLoves. All rights reserved.
</footer>

</body>
</html>
