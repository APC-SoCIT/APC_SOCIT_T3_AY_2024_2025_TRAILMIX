<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['Role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $user_id = intval($_POST['user_id']);
    $conn->query("UPDATE users SET Role = 'admin' WHERE UserID = $user_id");
    header("Location: admin_grant_admin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Grant Admin Rights</title>
  <style>
    body { font-family: Arial; margin: 0; background: #fff; }
    header, footer { background: #2e7d32; color: white; text-align: center; padding: 15px; }
    nav { background: #c62828; padding: 10px; text-align: center; }
    nav a { color: white; margin: 0 10px; text-decoration: none; font-weight: bold; }
    .container { max-width: 1000px; margin: 30px auto; padding: 20px; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th, td { border: 1px solid #ccc; padding: 12px; text-align: left; }
    th { background-color: #2e7d32; color: white; }
    .btn { padding: 6px 12px; background: #c62828; color: white; border: none; border-radius: 4px; cursor: pointer; }
  </style>
</head>
<body>

<header>
  <h1>NuttyLoves Admin Panel</h1>
</header>

<nav>
  <a href="admin_dashboard.php">Dashboard</a>
  <a href="admin_low_stock_alerts.php">Low Stock Alerts</a>
  <a href="admin_manage_products.php">Products</a>
  <a href="admin_manage_inventory.php">Inventory</a>
  <a href="admin_manage_ingredients.php">Ingredients</a>
  <a href="admin_manage_orders.php">Orders</a>
  <a href="admin_view_sales.php">Sales</a>
  <a href="admin_sales_chart.php">Reports</a>
  <a href="admin_verify_users.php">Verify Users</a>
  <a href="admin_grant_admin.php">Grant Admin</a>
  <a href="admin_edit_account.php">My Account</a>
  <a href="logout.php">Logout</a>
</nav>

<div class="container">
  <h2>Grant Admin Rights</h2>

  <table>
    <tr>
      <th>User ID</th>
      <th>Name</th>
      <th>Email</th>
      <th>Current Role</th>
      <th>Action</th>
    </tr>
    <?php
    $users = $conn->query("SELECT * FROM users WHERE Role = 'customer'");
    while ($row = $users->fetch_assoc()):
    ?>
    <tr>
      <td><?= $row['UserID'] ?></td>
      <td><?= htmlspecialchars($row['Name']) ?></td>
      <td><?= htmlspecialchars($row['Email']) ?></td>
      <td><?= $row['Role'] ?></td>
      <td>
        <form method="post">
          <input type="hidden" name="user_id" value="<?= $row['UserID'] ?>">
          <button type="submit" class="btn">Make Admin</button>
        </form>
      </td>
    </tr>
    <?php endwhile; ?>
  </table>
</div>

<footer>
  &copy; <?= date('Y') ?> NuttyLoves. All rights reserved.
</footer>

</body>
</html>
