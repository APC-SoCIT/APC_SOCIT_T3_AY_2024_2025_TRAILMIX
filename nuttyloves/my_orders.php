<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['Role'] !== 'customer') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user']['UserID'];

$query = "
    SELECT o.OrderID, o.OrderDate, o.TotalAmount, o.Status,
           GROUP_CONCAT(CONCAT(p.Name, ' (x', oi.Quantity, ')') SEPARATOR '||') AS Items
    FROM orders o
    JOIN order_items oi ON o.OrderID = oi.OrderID
    JOIN products p ON p.ProductID = oi.ProductID
    WHERE o.UserID = ?
    GROUP BY o.OrderID
    ORDER BY o.OrderDate DESC
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
  <title>My Orders - NuttyLoves</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    body { font-family: 'Segoe UI', sans-serif; margin: 0; background: #f9f9f9; color: #333; }
    header { background: #2e7d32; color: white; padding: 20px; text-align: center; }
    nav {
      background: #c62828; padding: 10px; display: flex;
      justify-content: space-between; align-items: center;
    }
    nav a {
      color: white; text-decoration: none; font-weight: bold; margin: 0 15px;
    }
    nav a:hover { text-decoration: underline; }
    .container {
      max-width: 1000px; margin: 30px auto; padding: 20px;
      background: white; box-shadow: 0 0 10px rgba(0,0,0,0.1); border-radius: 10px;
    }
    h2 { color: #2e7d32; text-align: center; margin-bottom: 20px; }
    table {
      width: 100%; border-collapse: collapse; margin-top: 10px;
    }
    th, td {
      border: 1px solid #ccc; padding: 12px; text-align: center;
    }
    th {
      background-color: #c62828; color: white;
    }
    tr:nth-child(even) { background-color: #f5f5f5; }
    .empty {
      text-align: center; font-size: 1.1em; color: #999;
    }
  </style>
</head>
<body>

<header>
  <h1>NuttyLoves</h1>
  <p>Your Delicious Order History</p>
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
  <h2>My Orders</h2>

  <table>
    <tr>
      <th>Order ID</th>
      <th>Date</th>
      <th>Items</th>
      <th>Total</th>
      <th>Status</th>
    </tr>

    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= $row['OrderID'] ?></td>
          <td><?= date('F j, Y', strtotime($row['OrderDate'])) ?></td>
          <td>
            <ul style="list-style: none; padding-left: 0; margin: 0;">
              <?php foreach (explode('||', $row['Items']) as $item): ?>
                <li><?= htmlspecialchars($item) ?></li>
              <?php endforeach; ?>
            </ul>
          </td>
          <td>₱<?= number_format($row['TotalAmount'], 2) ?></td>
          <td><?= $row['Status'] ?></td>
        </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="5" class="empty">You have no orders yet.</td></tr>
    <?php endif; ?>
  </table>
</div>

<footer style="text-align:center; padding:20px; background:#2e7d32; color:white; margin-top:30px;">
  &copy; <?= date('Y') ?> NuttyLoves. All rights reserved.
</footer>

</body>
</html>
