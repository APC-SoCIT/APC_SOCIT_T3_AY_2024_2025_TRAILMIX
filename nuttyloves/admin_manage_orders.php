<?php
session_start();
include 'config.php';
if (!isset($_SESSION['user']) || $_SESSION['user']['Role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>NuttyLoves Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    body { font-family: Arial, sans-serif; margin: 0; background: #fff; }
    header, footer { background: #2e7d32; color: white; padding: 15px; text-align: center; }
    nav { background: #c62828; display: flex; justify-content: center; padding: 10px; }
    nav a { color: white; margin: 0 15px; text-decoration: none; font-weight: bold; }
    .container { max-width: 1000px; margin: 30px auto; padding: 20px; }
    h2 { color: #2e7d32; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th, td { padding: 10px; border: 1px solid #ccc; text-align: left; }
    th { background-color: #2e7d32; color: white; }
    .btn { padding: 6px 12px; background: #c62828; color: white; border: none; border-radius: 3px; text-decoration: none; cursor: pointer; }
    .btn:hover { background-color: #b71c1c; }
    form { display: inline; }
    select { padding: 5px; }
  </style>
</head>
<body>

<header>
  <h1>NuttyLoves Admin Panel</h1>
</header>

<nav>
  <a href="admin_dashboard.php">Dashboard</a>
  <a href="admin_manage_products.php">Products</a>
  <a href="admin_manage_inventory.php">Inventory</a>
  <a href="admin_manage_orders.php">Orders</a>
  <a href="admin_view_sales.php">Sales</a>
  <a href="admin_sales_chart.php">Reports</a>
  <a href="admin_verify_users.php">Verify Users</a>
  <a href="logout.php">Logout</a>
</nav>

<div class="container">
  <h2>Manage Orders</h2>
  <table>
    <tr><th>Order ID</th><th>Customer</th><th>Total</th><th>Status</th><th>Actions</th></tr>

    <?php
    $query = "
      SELECT o.OrderID, o.TotalAmount, o.Status, c.Name
      FROM orders o
      JOIN users c ON o.UserID = c.UserID
      ORDER BY o.OrderID DESC
    ";
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0):
        while ($row = $result->fetch_assoc()):
    ?>
    <tr>
      <td><?= $row['OrderID'] ?></td>
      <td><?= htmlspecialchars($row['Name']) ?></td>
      <td>₱<?= number_format($row['TotalAmount'], 2) ?></td>
      <td><?= htmlspecialchars($row['Status']) ?></td>
      <td>
        <form method="post" action="update_order_status.php">
          <input type="hidden" name="order_id" value="<?= $row['OrderID'] ?>">
          <select name="new_status">
            <option <?= $row['Status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
            <option <?= $row['Status'] === 'Shipped' ? 'selected' : '' ?>>Shipped</option>
            <option <?= $row['Status'] === 'Delivered' ? 'selected' : '' ?>>Delivered</option>
          </select>
          <button class="btn" type="submit">Update</button>
        </form>
      </td>
    </tr>
    <?php
        endwhile;
    else:
        echo "<tr><td colspan='5'>No orders found.</td></tr>";
    endif;
    ?>
  </table>
</div>

<footer>
  &copy; <?= date('Y') ?> NuttyLoves. All rights reserved.
</footer>

</body>
</html>
