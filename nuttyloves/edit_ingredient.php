<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['Role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['ingredient_name']);
    $stock = intval($_POST['stock_quantity']);

    $stmt = $conn->prepare(
        "UPDATE ingredients SET Name = ?, StockQuantity = ? WHERE IngredientID = ?"
    );
    $stmt->bind_param("sii", $name, $stock, $id);
    $stmt->execute();
    header("Location: admin_manage_ingredients.php");
    exit();
}

$stmt = $conn->prepare("SELECT * FROM ingredients WHERE IngredientID = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$ingredient = $stmt->get_result()->fetch_assoc();
if (!$ingredient) { die("Ingredient not found."); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>NuttyLoves | Edit Ingredient</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
:root{
  --green:#2e7d32;  --red:#c62828;  --red-dark:#b71c1c;
  --bg:#f7f7f7;     --card:#ffffff; --border:#e0e0e0;
}
*{box-sizing:border-box; font-family:'Segoe UI',Tahoma,Verdana,sans-serif;}
body{margin:0; background:var(--bg); color:#333;}
header,footer{background:var(--green); color:#fff; text-align:center; padding:15px;}
nav{background:var(--red); display:flex; justify-content:center; gap:25px; padding:10px;}
nav a{color:#fff; font-weight:bold; text-decoration:none;}
nav a:hover{text-decoration:underline;}

.container{max-width:640px; margin:40px auto; padding:0 15px;}
.card{background:var(--card); padding:25px 30px; border-radius:10px;
      box-shadow:0 2px 6px rgba(0,0,0,.05);}
h2{color:var(--green); margin-top:0;}

label{display:block; margin-bottom:6px; font-weight:600;}
input[type=text], input[type=number]{
  width:100%; padding:10px; border:1px solid var(--border);
  border-radius:6px; margin-bottom:18px;
}
button.btn{
  background:var(--red); color:#fff; border:none; padding:10px 24px;
  border-radius:6px; cursor:pointer; font-weight:bold;
}
button.btn:hover{background:var(--red-dark);}
.back-link{display:inline-block; margin-top:20px; text-decoration:none;
           color:var(--red); font-weight:bold;}
.back-link:hover{text-decoration:underline;}
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
    <h2>Edit Ingredient</h2>

    <form method="post">
      <label for="name">Ingredient Name</label>
      <input id="name" type="text" name="ingredient_name"
             value="<?= htmlspecialchars($ingredient['Name']) ?>" required>

      <label for="stock">Stock Quantity</label>
      <input id="stock" type="number" name="stock_quantity"
             value="<?= $ingredient['StockQuantity'] ?>" min="0" required>

      <button type="submit" class="btn">Save Changes</button>
    </form>

    <a href="admin_manage_ingredients.php" class="back-link">← Back to Ingredients</a>
  </div>
</div>

<footer>&copy; <?= date('Y') ?>NuttyLoves. All rights reserved.</footer>

</body>
</html>
