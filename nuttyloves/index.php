<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>NuttyLoves - Home</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: #ffffff;
      color: #333;
    }
    header {
      background-color: #2e7d32;
      padding: 20px;
      color: #fff;
      text-align: center;
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
      color: #fff;
      margin: 0 15px;
      text-decoration: none;
      font-weight: bold;
    }
    nav a:hover {
      text-decoration: underline;
    }
    .container {
      padding: 30px;
      max-width: 800px;
      margin: auto;
      text-align: center;
    }
    h2 {
      color: #2e7d32;
    }
    p {
      line-height: 1.6;
      font-size: 1.1em;
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
  <h1>Welcome to NuttyLoves</h1>
  <p>Natural Goodness in Every Bite</p>
</header>

<nav>
  <div class="nav-center">
    <a href="index.php">Home</a>
    <a href="shop.php">Shop</a>
    <a href="cart.php">Cart</a>
	<a href="my_orders.php">My Orders</a>
	<a href="account_settings.php">My Account</a>
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

<div class="container">
  <h2>About NuttyLoves</h2>
  <p>NuttyLoves is your go-to destination for premium trail mixes packed with natural goodness. We believe in offering snacks that are not only delicious but also nutritious, using the finest selection of nuts, dried fruits, and seeds.</p>
  <p>Browse our shop to find the perfect mix for your cravings and experience the joy of healthy snacking. From 8-in-1 blends to premium roasted cashews, we’ve got you covered.</p>
  <p><a href="shop.php" class="btn">Start Shopping →</a></p>
</div>

<footer>
  &copy; <?= date('Y') ?> NuttyLoves. All rights reserved.
</footer>

</body>
</html>
