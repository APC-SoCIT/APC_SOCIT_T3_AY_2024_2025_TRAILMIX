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
    o.OrderDate,
    o.TaxAmount,
    o.GrandTotal,
    p.Name               AS ProductName,
    oi.Quantity,
    oi.Price,
    (oi.Quantity * oi.Price) AS LineTotal
  FROM orders o
  JOIN order_items oi ON oi.OrderID  = o.OrderID
  JOIN products     p ON p.ProductID = oi.ProductID
  ORDER BY o.OrderDate DESC, o.OrderID DESC
";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>NuttyLoves • Sales List</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<link rel="preconnect" href="https://html2canvas.hertzen.com">
<style>
 :root{--green:#2e7d32;--red:#c62828;--red-dark:#b71c1c}
 body{font-family:'Segoe UI',sans-serif;margin:0;background:#f9f9f9}
 header,footer{background:var(--green);color:#fff;text-align:center;padding:15px}
 nav{background:var(--red);display:flex;justify-content:center;flex-wrap:wrap;padding:10px}
 nav a{color:#fff;font-weight:bold;margin:6px 12px;text-decoration:none}
 nav a:hover{text-decoration:underline}
 .wrap{max-width:1100px;margin:30px auto;padding:20px;background:#fff;border-radius:8px;box-shadow:0 0 10px rgba(0,0,0,.1)}
 h2{color:var(--green);text-align:center;margin:0 0 20px}
 table{width:100%;border-collapse:collapse}
 th,td{padding:10px;border:1px solid #ccc;text-align:center;font-size:.95rem}
 th{background:var(--green);color:#fff}
 tr.order-header td{background:#eceff1;font-weight:600;text-align:left}
 tr.total-row td{background:#fafafa;font-weight:600}
 .btn{display:block;margin:25px auto 10px;padding:10px 25px;background:var(--red);color:#fff;border:none;border-radius:5px;font-weight:700;cursor:pointer}
 .btn:hover{background:var(--red-dark)}
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

<div class="wrap" id="report">
  <h2>Detailed Sales List</h2>

<?php
if ($result && $result->num_rows):
    $currentOrder = null;          
    $orderSub     = 0;            
    $tax          = 0;
    $grand        = 0;

    echo '<table>';

    while ($row = $result->fetch_assoc()) {

        if ($currentOrder !== $row['OrderID']) {

            if ($currentOrder !== null) {
                echo "<tr class='total-row'><td colspan='3'>Subtotal</td><td>₱".number_format($orderSub,2)."</td></tr>";
                echo "<tr class='total-row'><td colspan='3'>Tax&nbsp;(12%)</td><td>₱".number_format($tax,2)."</td></tr>";
                echo "<tr class='total-row'><td colspan='3'><strong>Grand&nbsp;Total</strong></td><td><strong>₱".number_format($grand,2)."</strong></td></tr>";
            }

            $currentOrder = $row['OrderID'];
            $orderSub     = 0;
            $tax          = $row['TaxAmount'];
            $grand        = $row['GrandTotal'];

            echo "<tr class='order-header'><td colspan='4'>
                    <strong>Order #{$row['OrderID']}</strong> |
                    ".date('F j, Y g:i A',strtotime($row['OrderDate']))."
                  </td></tr>";
            echo '<tr><th>Product</th><th>Qty</th><th>Unit&nbsp;Price</th><th>Subtotal</th></tr>';
        }

        $orderSub += $row['LineTotal'];

        echo '<tr>
                <td>'.htmlspecialchars($row['ProductName']).'</td>
                <td>'.$row['Quantity'].'</td>
                <td>₱'.number_format($row['Price'],2).'</td>
                <td>₱'.number_format($row['LineTotal'],2).'</td>
              </tr>';
    }

    echo "<tr class='total-row'><td colspan='3'>Subtotal</td><td>₱".number_format($orderSub,2)."</td></tr>";
    echo "<tr class='total-row'><td colspan='3'>Tax&nbsp;(12%)</td><td>₱".number_format($tax,2)."</td></tr>";
    echo "<tr class='total-row'><td colspan='3'><strong>Grand&nbsp;Total</strong></td><td><strong>₱".number_format($grand,2)."</strong></td></tr>";

    echo '</table>';

else:
    echo "<p style='text-align:center;margin:25px 0;'>No sales data found.</p>";
endif;
?>
</div>

<button class="btn" id="exportBtn">Export as JPG</button>

<footer>&copy; <?= date('Y') ?>NuttyLoves. All rights reserved.</footer>

<script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
<script>
document.getElementById('exportBtn').addEventListener('click',()=>{
  html2canvas(document.getElementById('report')).then(canvas=>{
    const link=document.createElement('a');
    link.download='NuttyLoves_Sales_List.jpg';
    link.href=canvas.toDataURL('image/jpeg',1.0);
    link.click();
  });
});
</script>

</body>
</html>
