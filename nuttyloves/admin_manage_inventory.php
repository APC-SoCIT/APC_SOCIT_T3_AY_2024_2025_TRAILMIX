<?php
session_start();

include 'config.php';

require __DIR__ . '/phpmailer/src/Exception.php';
require __DIR__ . '/phpmailer/src/PHPMailer.php';
require __DIR__ . '/phpmailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!isset($_SESSION['user']) || $_SESSION['user']['Role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
if (isset($_GET['error']) && $_GET['error'] === 'lowstock') {
    $ingredient = htmlspecialchars($_GET['item']);
    echo "<script>alert('⚠️ Cannot increase product stock. Not enough of the ingredient: $ingredient');</script>";
}

$lowStockItems = [];
$query = "SELECT Name, StockQuantity FROM products";
$result = $conn->query($query);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        if ($row['StockQuantity'] < 5) {
            $lowStockItems[] = "{$row['Name']} ({$row['StockQuantity']} left)";
        }
    }
}

if (!empty($lowStockItems)) {


    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'francisconjd.csa@gmail.com'; 
        $mail->Password = 'wyyh bgys pvhh nsqm';    
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('francisconjd.csa@gmail.com', 'NuttyLoves');
        $mail->addAddress('your-email@gmail.com', 'Admin');

        $mail->isHTML(true);
        $mail->Subject = 'Low Stock Alert - NuttyLoves';
        $mail->Body = "
            <h3>Low Stock Notification</h3>
            <p>The following products are low on stock (less than 5 units):</p>
            <ul>" . implode('', array_map(fn($item) => "<li>$item</li>", $lowStockItems)) . "</ul>
            <p>Please consider restocking soon.</p>
        ";

        $mail->send();
    } catch (Exception $e) {
        error_log("Low stock email failed: {$mail->ErrorInfo}");
    }
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
    input[type=number] { width: 60px; }
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
  <h2>Manage Inventory</h2>
  <table>
    <tr><th>Product</th><th>Stock</th><th>Update Stock</th></tr>
    <?php
    $query = "SELECT ProductID, Name, StockQuantity FROM products";
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0):
        while ($row = $result->fetch_assoc()):
    ?>
    <tr>
      <td><?= htmlspecialchars($row['Name']) ?></td>
      <td><?= $row['StockQuantity'] ?></td>
      <td>
        <form method="post" action="update_stock.php">
          <input type="hidden" name="product_id" value="<?= $row['ProductID'] ?>">
          <input type="number" name="new_stock" value="<?= $row['StockQuantity'] ?>" required>
          <button class="btn" type="submit">Update</button>
        </form>
      </td>
    </tr>
    <?php
        endwhile;
    else:
        echo "<tr><td colspan='3'>No products found.</td></tr>";
    endif;
    ?>
  </table>
</div>

<footer>
  &copy; <?= date('Y') ?> NuttyLoves. All rights reserved.
</footer>

</body>
</html>
