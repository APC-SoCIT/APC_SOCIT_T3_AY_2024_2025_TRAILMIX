<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['Role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch monthly sales data
$data = [];
$labels = [];

$sql = "
    SELECT DATE_FORMAT(OrderDate, '%M') AS month, SUM(TotalAmount) AS revenue
    FROM orders
    WHERE YEAR(OrderDate) = YEAR(CURDATE())
    GROUP BY MONTH(OrderDate)
    ORDER BY MONTH(OrderDate)
";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $labels[] = $row['month'];
    $data[] = $row['revenue'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Sales Chart - NuttyLoves</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body { font-family: Arial, sans-serif; margin: 0; padding: 0; background: #fff; }
    header, footer { background: #2e7d32; color: white; padding: 15px; text-align: center; }
    nav { background: #c62828; display: flex; justify-content: center; padding: 10px; }
    nav a { color: white; margin: 0 15px; text-decoration: none; font-weight: bold; }
    .container { max-width: 1000px; margin: 30px auto; padding: 20px; }
    canvas { background: #f9f9f9; border: 1px solid #ddd; padding: 20px; border-radius: 10px; }
    h2 { text-align: center; color: #2e7d32; margin-bottom: 20px; }
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
  <a href="admin_verify_users.php">Verify Users</a>
  <a href="logout.php">Logout</a>
</nav>

<div class="container">
  <h2>📊 Monthly Sales Report (<?= date('Y') ?>)</h2>
  <canvas id="salesChart" width="800" height="400"></canvas>
</div>

<footer>
  &copy; <?= date('Y') ?> NuttyLoves. All rights reserved.
</footer>

<script>
  const ctx = document.getElementById('salesChart').getContext('2d');
  const salesChart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: <?= json_encode($labels) ?>,
      datasets: [{
        label: 'Revenue (₱)',
        data: <?= json_encode($data) ?>,
        backgroundColor: '#c62828',
        borderRadius: 5
      }]
    },
    options: {
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            callback: value => '₱' + value.toLocaleString()
          }
        }
      }
    }
  });
</script>

</body>
</html>
