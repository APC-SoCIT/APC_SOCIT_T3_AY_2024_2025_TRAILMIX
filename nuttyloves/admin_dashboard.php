<?php
// admin_dashboard.php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['Role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard - NuttyLoves</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    body { font-family: Arial, sans-serif; margin: 0; background: #fff; }
    header, footer { background: #2e7d32; color: white; padding: 15px; text-align: center; }
    nav { background: #c62828; display: flex; justify-content: center; padding: 10px; }
    nav a { color: white; margin: 0 15px; text-decoration: none; font-weight: bold; }
    .container { max-width: 1000px; margin: 30px auto; padding: 20px; }
    h2 { color: #2e7d32; }
    .admin-links a { display: block; margin: 10px 0; color: #c62828; font-weight: bold; }
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
  <h2>Admin Functions</h2>
  <div class="admin-links">
    <a href="admin_manage_products.php">Manage Products</a>
    <a href="admin_manage_inventory.php">Manage Inventory</a>
    <a href="admin_manage_orders.php">Manage Orders</a>
    <a href="admin_view_sales.php">View Sales List</a>
	<a href="admin_sales_chart.php">View Sales Reports</a>
	<a href="admin_verify_users.php">Verify Users</a>
  </div>
</div>

<footer>
  &copy; <?= date('Y') ?> NuttyLoves. All rights reserved.
</footer>

</body>
</html>
