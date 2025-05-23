<?php
session_start();
include 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Your Cart - NuttyLoves</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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
    .container {
      max-width: 900px;
      margin: 30px auto;
      padding: 20px;
    }
    h2 {
      color: #2e7d32;
      text-align: center;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }
    th, td {
      padding: 12px;
      border: 1px solid #ddd;
      text-align: center;
    }
    th {
      background-color: #c62828;
      color: white;
    }
    .total {
      font-weight: bold;
      color: #2e7d32;
    }
    .empty {
      text-align: center;
      color: #999;
      font-size: 1.2em;
      margin-top: 30px;
    }
    .btn {
      display: inline-block;
      background-color: #c62828;
      color: white;
      padding: 10px 20px;
      text-decoration: none;
      border-radius: 5px;
      margin-top: 20px;
      font-weight: bold;
    }
    .btn:hover {
      background-color: #b71c1c;
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

<div class="container">
  <h2>Your Shopping Cart</h2>
  <a href="index.php" class="btn">← Continue Shopping</a>

  <?php
  if (empty($_SESSION['cart'])) {
    echo "<p class='empty'>Your cart is currently empty.</p>";
  } else {
    $ids = implode(',', array_keys($_SESSION['cart']));
    $sql = "SELECT * FROM products WHERE ProductID IN ($ids)";
    $result = $conn->query($sql);
    $total = 0;

    echo "<table>";
    echo "<tr><th>Product</th><th>Price</th><th>Quantity</th><th>Subtotal</th></tr>";

    while ($row = $result->fetch_assoc()) {
      $qty = $_SESSION['cart'][$row['ProductID']];
      $subtotal = $row['Price'] * $qty;
      $total += $subtotal;

      echo "<tr>
              <td>{$row['Name']}</td>
              <td>₱" . number_format($row['Price'], 2) . "</td>
              <td>
				<a href='update_cart.php?action=decrease&product_id={$row['ProductID']}' style='padding:4px 10px; background:#b71c1c; color:#fff; text-decoration:none; border-radius:4px;'>−</a>
				$qty
				<a href='update_cart.php?action=increase&product_id={$row['ProductID']}' style='padding:4px 10px; background:#2e7d32; color:#fff; text-decoration:none; border-radius:4px;'>+</a>
				<br>
				<a href='update_cart.php?action=remove&product_id={$row['ProductID']}' style='color:#c62828; font-size: 0.9em;'>Remove</a>
				</td>

              <td>₱" . number_format($subtotal, 2) . "</td>
            </tr>";
    }

    echo "<tr>
            <td colspan='3' class='total'>Total</td>
            <td class='total'>₱" . number_format($total, 2) . "</td>
          </tr>";
		  
    echo "</table>";
	
  }
  ?>
</div>
<form action="checkout.php" method="post">
  <div style="text-align: center; margin-top: 20px;">
    <button type="submit" class="btn">🛒 Checkout Now</button>
  </div>
</form>
<footer>
  &copy; <?= date('Y') ?> NuttyLoves. All rights reserved.
</footer>

</body>
</html>
