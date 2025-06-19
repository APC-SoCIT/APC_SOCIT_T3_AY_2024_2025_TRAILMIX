<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['Role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$sql = "
  SELECT 
      o.OrderID,
      o.TotalAmount,                               -- subtotal
      ROUND(o.TotalAmount * 0.12, 2)  AS TaxAmount,
      ROUND(o.TotalAmount * 1.12, 2)  AS GrandTotal,
      o.Status,
      o.OrderDate,
      u.Name                    AS CustomerName,
      p.Name                    AS ProductName,
      oi.Quantity
  FROM orders        o
  JOIN users         u  ON u.UserID  = o.UserID
  JOIN order_items   oi ON oi.OrderID = o.OrderID
  JOIN products      p  ON p.ProductID = oi.ProductID
  ORDER BY o.OrderDate DESC
";

$result = $conn->query($sql);
if (!$result) die("SQL error: " . $conn->error);

$orders = [];
while ($row = $result->fetch_assoc()) {

    $id = $row['OrderID'];

    if (!isset($orders[$id])) {
        $orders[$id] = [
            'OrderID'      => $id,
            'CustomerName' => $row['CustomerName'],
            'OrderDate'    => $row['OrderDate'],
            'Subtotal'     => $row['TotalAmount'],
            'Tax'          => $row['TaxAmount'],
            'GrandTotal'   => $row['GrandTotal'],
            'Status'       => $row['Status'],
            'Items'        => []
        ];
    }

    $orders[$id]['Items'][] = [
        'ProductName' => $row['ProductName'],
        'Quantity'    => $row['Quantity']
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>NuttyLoves • Manage Orders</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
 :root { --green:#2e7d32; --red:#c62828; --red-dark:#b71c1c; }

 body   { font-family:Arial, sans-serif; margin:0; background:#fff; }
 header,footer { background:var(--green); color:#fff; text-align:center; padding:15px }
 nav    { background:var(--red); display:flex; flex-wrap:wrap; justify-content:center; padding:10px }
 nav a  { color:#fff; font-weight:bold; margin:6px 12px; text-decoration:none }
 nav a:hover { text-decoration:underline }

 .wrap  { max-width:1100px; margin:30px auto; padding:0 15px }
 h2     { color:var(--green); margin:0 0 15px; text-align:center }

 table  { width:100%; border-collapse:collapse; margin-bottom:40px }
 th,td  { border:1px solid #ccc; padding:10px; text-align:left; vertical-align:top }
 th     { background:var(--green); color:#fff }
 .products { font-size:.9em; line-height:1.4 }
 select { padding:4px }
 .btn   { background:var(--red); color:#fff; border:none; border-radius:3px; padding:6px 12px; cursor:pointer }
 .btn:hover { background:var(--red-dark) }
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

<div class="wrap">
  <h2>Manage Customer Orders</h2>

  <table>
    <tr>
      <th>ID</th>
      <th>Customer</th>
      <th>Date</th>
      <th>Sub‑Total</th>
      <th>Tax (12%)</th>
      <th>Grand Total</th>
      <th>Status</th>
      <th>Products</th>
      <th>Action</th>
    </tr>

<?php if ($orders): ?>
<?php foreach ($orders as $o): ?>
    <tr>
      <td><?= $o['OrderID'] ?></td>
      <td><?= htmlspecialchars($o['CustomerName']) ?></td>
      <td><?= date('F j, Y', strtotime($o['OrderDate'])) ?></td>
      <td>₱<?= number_format($o['Subtotal'], 2) ?></td>
      <td>₱<?= number_format($o['Tax'],       2) ?></td>
      <td><strong>₱<?= number_format($o['GrandTotal'], 2) ?></strong></td>
      <td><?= htmlspecialchars($o['Status']) ?></td>
      <td class="products">
        <?php foreach ($o['Items'] as $it): ?>
           <?= htmlspecialchars($it['ProductName']) ?> (x<?= $it['Quantity'] ?>)<br>
        <?php endforeach; ?>
      </td>
      <td>
        <form method="post" action="update_order_status.php">
          <input type="hidden" name="order_id" value="<?= $o['OrderID'] ?>">
          <select name="new_status">
            <?php foreach (['Pending','Shipped','Delivered'] as $st): ?>
              <option <?= $o['Status']===$st ? 'selected':'' ?>><?= $st ?></option>
            <?php endforeach; ?>
          </select>
          <button class="btn" type="submit">Update</button>
        </form>
      </td>
    </tr>
<?php endforeach; ?>
<?php else: ?>
    <tr><td colspan="9" style="text-align:center">No orders found.</td></tr>
<?php endif; ?>
  </table>
</div>

<footer>&copy; <?= date('Y') ?>NuttyLoves. All rights reserved.</footer>

</body>
</html>
