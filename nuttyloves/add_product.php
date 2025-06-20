<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['Role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $price = floatval($_POST['price']);
    $desc = $_POST['description'];
    $stock = intval($_POST['stock']);

    $stmt = $conn->prepare("INSERT INTO products (Name, Description, Price, StockQuantity) VALUES (?, ?, ?, ?)");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("ssdi", $name, $desc, $price, $stock);
    $stmt->execute();

    header("Location: admin_manage_products.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Product - NuttyLoves Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    body { font-family: Arial, sans-serif; margin: 0; background: #f5f5f5; }
    header, footer { background: #2e7d32; color: white; padding: 15px; text-align: center; }
    nav { background: #c62828; display: flex; justify-content: center; padding: 10px; }
    nav a { color: white; margin: 0 15px; text-decoration: none; font-weight: bold; }
    nav a:hover { text-decoration: underline; }

    .container {
      max-width: 600px;
      margin: 40px auto;
      padding: 30px;
      background: white;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    h2 {
      text-align: center;
      color: #2e7d32;
      margin-bottom: 20px;
    }

    form {
      display: flex;
      flex-direction: column;
      gap: 15px;
    }

    label {
      font-weight: bold;
      color: #444;
    }

    input[type="text"],
    input[type="number"],
    textarea {
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 5px;
      font-size: 1em;
      width: 100%;
    }

    textarea {
      resize: vertical;
      height: 100px;
    }

    button {
      background-color: #c62828;
      color: white;
      padding: 12px;
      border: none;
      border-radius: 5px;
      font-weight: bold;
      font-size: 1em;
      cursor: pointer;
    }

    button:hover {
      background-color: #b71c1c;
    }

    .back-link {
      display: block;
      text-align: center;
      margin-top: 15px;
    }

    .back-link a {
      color: #2e7d32;
      text-decoration: none;
      font-weight: bold;
    }
    .back-link a:hover {
      text-decoration: underline;
    }
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
  <h2>Add New Product</h2>
  <form method="post">
    <label for="name">Product Name</label>
    <input type="text" id="name" name="name" required>

    <label for="description">Description</label>
    <textarea id="description" name="description" required></textarea>

    <label for="price">Price (₱)</label>
    <input type="number" id="price" name="price" step="0.01" required>

    <label for="stock">Stock Quantity</label>
    <input type="number" id="stock" name="stock" required>

    <button type="submit">Add Product</button>
  </form>

  <div class="back-link">
    <a href="admin_manage_products.php">← Back to Products</a>
  </div>
</div>

<footer>
  &copy; <?= date('Y') ?> NuttyLoves. All rights reserved.
</footer>

</body>
</html>
