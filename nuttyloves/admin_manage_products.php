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
    .admin-links a { display: block; margin: 10px 0; color: #c62828; font-weight: bold; text-decoration: none; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th, td { padding: 10px; border: 1px solid #ccc; text-align: left; }
    th { background-color: #2e7d32; color: white; }
    .btn { padding: 6px 12px; background: #c62828; color: white; border: none; border-radius: 3px; text-decoration: none; cursor: pointer; }
    .btn:hover { background-color: #b71c1c; }
    form { display: inline; }
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
  <h2>Manage Products</h2>
  <p><a class="btn" href="add_product.php">+ Add New Product</a></p>
  <table>
    <tr><th>ID</th><th>Name</th><th>Price</th><th>Actions</th></tr>

    <?php
    $query = "SELECT * FROM products";
    $result = $conn->query($query);

    if ($result->num_rows > 0):
      while ($row = $result->fetch_assoc()):
    ?>
    <tr>
      <td><?= $row['ProductID'] ?></td>
      <td><?= htmlspecialchars($row['Name']) ?></td>
      <td>₱<?= number_format($row['Price'], 2) ?></td>
	<td>
	  <a class="btn" href="edit_product.php?id=<?= $row['ProductID'] ?>">Edit</a>
	  <a class="btn" href="delete_product.php?id=<?= $row['ProductID'] ?>" onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
	</td>
    </tr>
    <?php
      endwhile;
    else:
      echo "<tr><td colspan='4'>No products found.</td></tr>";
    endif;
    ?>
  </table>
</div>

<footer>
  &copy; <?= date('Y') ?> NuttyLoves. All rights reserved.
</footer>

</body>
</html>
