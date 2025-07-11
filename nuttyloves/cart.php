<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
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
    input[type="number"] {
      width: 60px;
      padding: 5px;
      text-align: center;
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
  <h2>Your Shopping Cart</h2>
  <a href="shop.php" class="btn">← Continue Shopping</a>

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
      $price = $row['Price'];
      $subtotal = $price * $qty;
      $total += $subtotal;

      echo "<tr>
              <td>" . htmlspecialchars($row['Name']) . "</td>
              <td>₱" . number_format($price, 2) . "</td>
              <td>
                <form method='post' action='update_cart.php' style='display:inline-block;'>
                  <input type='hidden' name='product_id' value='{$row['ProductID']}'>
                  <input type='number' name='quantity' value='{$qty}' min='1'>
                  <button type='submit' class='btn' style='padding:4px 10px;font-size:0.9em;'>Update</button>
                </form>
                <br>
                <a href='update_cart.php?action=remove&product_id={$row['ProductID']}' style='color:#c62828;font-size:0.9em;'>Remove</a>
              </td>
              <td>₱" . number_format($subtotal, 2) . "</td>
            </tr>";
    }

    $tax = $total * 0.12;
    $grand_total = $total + $tax;

    echo "<tr><td colspan='3' class='total'>Subtotal</td>
              <td class='total'>₱" . number_format($total, 2) . "</td></tr>";
    echo "<tr><td colspan='3' class='total'>Tax (12%)</td>
              <td class='total'>₱" . number_format($tax, 2) . "</td></tr>";
    echo "<tr><td colspan='3' class='total'><strong>Grand Total</strong></td>
              <td class='total'><strong>₱" . number_format($grand_total, 2) . "</strong></td></tr>";
    echo "</table>";
  }
  ?>
</div>

<?php if (isset($_SESSION['user'])): ?>
  <form action="checkout.php" method="post">
    <div style="text-align: center; margin-top: 20px;">
      <button type="submit" class="btn">Checkout Now</button>
    </div>
  </form>
<?php else: ?>
  <div style="text-align: center; margin-top: 20px;">
    <p><strong>Please log in to proceed with checkout.</strong></p>
    <a href="login.php" class="btn">Login to Checkout</a>
  </div>
<?php endif; ?>

<footer>
  &copy; <?= date('Y') ?> NuttyLoves. All rights reserved.
</footer>

</body>
</html>
