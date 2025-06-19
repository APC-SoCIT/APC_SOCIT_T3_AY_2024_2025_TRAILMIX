<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['Role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ingredient_name'], $_POST['stock_quantity'])) {
    $name = trim($_POST['ingredient_name']);
    $stock = intval($_POST['stock_quantity']);
    $stmt = $conn->prepare("INSERT INTO ingredients (Name, StockQuantity) VALUES (?, ?)");
    $stmt->bind_param("si", $name, $stock);
    $stmt->execute();
    header("Location: admin_manage_ingredients.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>NuttyLoves | Manage Ingredients</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
/* ----------  Global styles  ---------- */
:root{
  --green:#2e7d32;  --red:#c62828;  --red-dark:#b71c1c;
  --bg:#f7f7f7;     --card:#ffffff; --border:#e0e0e0;
}
*{box-sizing:border-box; font-family:'Segoe UI',Tahoma,Verdana,sans-serif;}
body{margin:0; background:var(--bg); color:#333;}
header,footer{background:var(--green); color:#fff; text-align:center; padding:15px;}
nav{background:var(--red); display:flex; justify-content:center; gap:25px; padding:10px;}
nav a{color:#fff; text-decoration:none; font-weight:bold;}
nav a:hover{text-decoration:underline;}
.container{max-width:960px; margin:30px auto; padding:0 15px;}
h2{color:var(--green); margin-top:0;}
/* ----------  Card  ---------- */
.card{background:var(--card); padding:25px; border-radius:10px; box-shadow:0 2px 6px rgba(0,0,0,.05);}
input[type=text]{width:100%; padding:10px; border:1px solid var(--border); border-radius:6px;}
button.btn{background:var(--red); color:#fff; border:none; padding:10px 18px; border-radius:6px; cursor:pointer;}
button.btn:hover{background:var(--red-dark);}
table{width:100%; border-collapse:collapse; margin-top:25px;}
th,td{padding:12px; border:1px solid var(--border); text-align:left;}
th{background:var(--green); color:#fff;}
.no-data{padding:30px 0; text-align:center; color:#777;}
</style>
</head>
<body>

<header><h1>NuttyLoves Admin Panel</h1></header>

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

  <div class="card">
    <h2>Manage Ingredients</h2>

    <!-- Add ingredient form -->
<form method="post" class="ingredient-form">
    <input type="text" name="ingredient_name" required placeholder="Ingredient name">
    <input type="number" name="stock_quantity" min="0" required placeholder="Initial stock">
    <button type="submit">Add Ingredient</button>
</form>


<?php
$res = $conn->query("SELECT * FROM ingredients ORDER BY IngredientID ASC");
if ($res && $res->num_rows): ?>
<table>
  <tr><th>ID</th><th>Name</th><th>Stock</th><th>Actions</th></tr>
  <?php
  $res = $conn->query("SELECT * FROM ingredients ORDER BY IngredientID ASC");
  while ($row = $res->fetch_assoc()):
  ?>
    <tr>
        <td><?= $row['IngredientID'] ?></td>
        <td><?= htmlspecialchars($row['Name']) ?></td>
        <td><?= $row['StockQuantity'] ?>g</td>
        <td>
          <a class="btn" href="edit_ingredient.php?id=<?= $row['IngredientID'] ?>">Edit</a>
        </td>
    </tr>
  <?php endwhile; ?>
</table>


<?php else: ?>
  <p class="no-data">No ingredients added yet.</p>
<?php endif; ?>

  </div>

</div>

<footer>&copy; <?= date('Y') ?>NuttyLoves. All rights reserved.</footer>

</body>
</html>
