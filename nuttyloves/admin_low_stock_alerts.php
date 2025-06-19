<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['Role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$lowProducts = $conn->query("SELECT * FROM products WHERE StockQuantity < 5");

$lowIngredients = $conn->query("SELECT * FROM ingredients WHERE StockQuantity < 5000");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Low Stock Alerts - Admin</title>
    <style>
        body { font-family: Arial; background: #fefefe; margin: 0; }
        header, footer { background: #2e7d32; color: white; text-align: center; padding: 15px; }
        .container { max-width: 1000px; margin: 30px auto; padding: 20px; }
        h2 { color: #c62828; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 10px; border: 1px solid #ccc; text-align: left; }
        th { background: #2e7d32; color: white; }
        .alert { background-color: #ffebee; color: #c62828; padding: 10px; border-left: 5px solid #b71c1c; margin-bottom: 20px; }
        nav { background: #c62828; display: flex; justify-content: center; padding: 10px; }
        nav a { color: white; margin: 0 15px; text-decoration: none; font-weight: bold; }
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
    <h2>Low Stock Products</h2>
    <?php if ($lowProducts->num_rows > 0): ?>
        <table>
            <tr><th>Product ID</th><th>Name</th><th>Stock Quantity</th></tr>
            <?php while ($row = $lowProducts->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['ProductID'] ?></td>
                    <td><?= htmlspecialchars($row['Name']) ?></td>
                    <td><?= $row['StockQuantity'] ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <div class="alert">All product stocks are sufficient.</div>
    <?php endif; ?>

    <h2>Low Stock Ingredients</h2>
    <?php if ($lowIngredients->num_rows > 0): ?>
        <table>
            <tr><th>Ingredient ID</th><th>Name</th><th>Stock Quantity</th></tr>
            <?php while ($row = $lowIngredients->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['IngredientID'] ?></td>
                    <td><?= htmlspecialchars($row['Name']) ?></td>
                    <td><?= $row['StockQuantity'] ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <div class="alert">All ingredient stocks are sufficient.</div>
    <?php endif; ?>
</div>

<footer>
    &copy; <?= date('Y') ?> NuttyLoves. All rights reserved.
</footer>

</body>
</html>
