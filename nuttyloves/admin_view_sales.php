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
  <title>NuttyLoves Sales Report</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    body { font-family: 'Segoe UI', sans-serif; margin: 0; background: #f9f9f9; }
    header, footer { background: #2e7d32; color: white; padding: 15px; text-align: center; }
    nav { background: #c62828; display: flex; justify-content: center; padding: 10px; }
    nav a { color: white; margin: 0 15px; text-decoration: none; font-weight: bold; }
    .container { max-width: 1100px; margin: 30px auto; padding: 20px; background: white; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }

    h2 { color: #2e7d32; text-align: center; margin-bottom: 10px; }
    .report-summary { text-align: center; margin-bottom: 20px; font-size: 1.1em; color: #555; }

    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    th, td { padding: 12px; border: 1px solid #ccc; text-align: center; }
    th { background-color: #2e7d32; color: white; }
    .btn {
      display: block;
      margin: 20px auto;
      padding: 10px 20px;
      background-color: #c62828;
      color: white;
      border: none;
      border-radius: 5px;
      font-weight: bold;
      cursor: pointer;
    }
    .btn:hover {
      background-color: #b71c1c;
    }
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

<div class="container" id="report">
  <h2>📈 Monthly Sales List</h2>
  <p class="report-summary">Summary of total orders and revenue by date</p>

  <table>
    <tr>
      <th>Date</th>
      <th>Total Orders</th>
      <th>Total Revenue</th>
    </tr>

    <?php
    $query = "
      SELECT DATE(OrderDate) as OrderDay, COUNT(*) as OrderCount, SUM(TotalAmount) as Revenue
      FROM orders
      GROUP BY OrderDay
      ORDER BY OrderDay DESC
    ";
    $result = $conn->query($query);
    if ($result && $result->num_rows > 0):
      while ($row = $result->fetch_assoc()):
    ?>
    <tr>
      <td><?= date('F j, Y', strtotime($row['OrderDay'])) ?></td>
      <td><?= $row['OrderCount'] ?></td>
      <td>₱<?= number_format($row['Revenue'], 2) ?></td>
    </tr>
    <?php endwhile; else: ?>
    <tr><td colspan="3">No sales data found.</td></tr>
    <?php endif; ?>
  </table>
</div>

<button class="btn" id="exportBtn">📤 Export as JPG</button>

<footer>
  &copy; <?= date('Y') ?> NuttyLoves. All rights reserved.
</footer>

<script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
<script>
document.getElementById('exportBtn').addEventListener('click', function () {
  html2canvas(document.getElementById('report')).then(function(canvas) {
    const link = document.createElement('a');
    link.download = 'NuttyLoves_Sales_Report.jpg';
    link.href = canvas.toDataURL('image/jpeg', 1.0);
    link.click();
  });
});
</script>

</body>
</html>
