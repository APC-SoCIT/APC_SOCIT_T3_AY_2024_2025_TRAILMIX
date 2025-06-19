<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['Role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

function safeQuery(mysqli $conn, string $sql) {
    $res = $conn->query($sql);
    if (!$res) {
        die("SQL error:\n$sql\n\nMySQL says: " . $conn->error);
    }
    return $res;
}

$monthLabels = $monthData = [];
$sql = "
    SELECT DATE_FORMAT(OrderDate,'%M') AS month,
           SUM(GrandTotal)             AS revenue
    FROM   orders
    WHERE  YEAR(OrderDate) = YEAR(CURDATE())
    GROUP  BY MONTH(OrderDate)
    ORDER  BY MONTH(OrderDate)
";
$res = safeQuery($conn, $sql);
while ($row = $res->fetch_assoc()) {
    $monthLabels[] = $row['month'];
    $monthData[]   = (float)$row['revenue'];
}

$prodLabels = $prodQty = [];
$sql = "
    SELECT p.Name, SUM(oi.Quantity) AS qty
    FROM   order_items oi
    JOIN   products p ON p.ProductID = oi.ProductID
    GROUP  BY oi.ProductID
    ORDER  BY qty DESC
    LIMIT  5
";
$res = safeQuery($conn, $sql);
while ($row = $res->fetch_assoc()) {
    $prodLabels[] = $row['Name'];
    $prodQty[]    = (int)$row['qty'];
}

$grossLabels = $grossData = [];
$sql = "
    SELECT p.Name,
           SUM(oi.Quantity * oi.Price) AS sales
    FROM   order_items oi
    JOIN   products p ON p.ProductID = oi.ProductID
    GROUP  BY oi.ProductID
    ORDER  BY sales DESC
    LIMIT  5
";
$res = safeQuery($conn, $sql);
while ($row = $res->fetch_assoc()) {
    $grossLabels[] = $row['Name'];
    $grossData[]   = (float)$row['sales'];
}

$ingLabels = $ingQty = [];
$sql = "
    SELECT i.Name,
           SUM(oi.Quantity * ri.QuantityUsed) AS total_used
    FROM   order_items oi
    JOIN   recipe_ingredients ri ON ri.ProductID = oi.ProductID
    JOIN   ingredients i         ON i.IngredientID = ri.IngredientID
    GROUP  BY i.IngredientID
    ORDER  BY total_used DESC
    LIMIT  5
";
$res = safeQuery($conn, $sql);
while ($row = $res->fetch_assoc()) {
    $ingLabels[] = $row['Name'];
    $ingQty[]    = (float)$row['total_used'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>NuttyLoves • Admin Reports</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    :root { --green:#2e7d32; --red:#c62828; --red-dark:#b71c1c; }
    body { font-family: 'Segoe UI', sans-serif; margin: 0; background: #fff; }
    header, footer { background: var(--green); color: white; text-align: center; padding: 15px; }
    nav { background: var(--red); display: flex; justify-content: center; flex-wrap: wrap; padding: 10px; }
    nav a { color: white; margin: 6px 12px; text-decoration: none; font-weight: bold; }
    nav a:hover { text-decoration: underline; }
    .wrap { max-width: 1100px; margin: 25px auto; padding: 0 15px; }
    h2 { color: var(--green); text-align: center; margin: 30px 0 10px; }
    canvas { background: #fafafa; border: 1px solid #ddd; border-radius: 10px; padding: 15px; margin-bottom: 40px; }
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

  <h2>Monthly Revenue (<?= date('Y'); ?>)</h2>
  <canvas id="revChart" height="300"></canvas>

  <h2>op 5 Best-Selling Products</h2>
  <canvas id="qtyChart" height="300"></canvas>

  <h2>Top 5 Highest-Grossing Products</h2>
  <canvas id="grossChart" height="300"></canvas>

  <h2>Top 5 Most-Used Ingredients</h2>
  <canvas id="ingChart" height="300"></canvas>

</div>

<footer>&copy; <?= date('Y') ?> NuttyLoves. All rights reserved.</footer>

<script>
new Chart(document.getElementById('revChart'), {
  type: 'bar',
  data: {
    labels: <?= json_encode($monthLabels) ?>,
    datasets: [{
      label: 'Revenue (₱, incl. tax)',
      data: <?= json_encode($monthData) ?>,
      backgroundColor: 'var(--red)',
      borderRadius: 6
    }]
  },
  options: {
    responsive: true,
    scales: {
      y: {
        beginAtZero: true,
        ticks: { callback: value => '₱' + value.toLocaleString() }
      }
    }
  }
});

new Chart(document.getElementById('qtyChart'), {
  type: 'bar',
  data: {
    labels: <?= json_encode($prodLabels) ?>,
    datasets: [{
      label: 'Units Sold',
      data: <?= json_encode($prodQty) ?>,
      backgroundColor: 'var(--green)',
      borderRadius: 6
    }]
  },
  options: {
    indexAxis: 'y',
    responsive: true,
    scales: { x: { beginAtZero: true } }
  }
});

new Chart(document.getElementById('grossChart'), {
  type: 'pie',
  data: {
    labels: <?= json_encode($grossLabels) ?>,
    datasets: [{
      data: <?= json_encode($grossData) ?>,
      backgroundColor: ['#c62828', '#ef5350', '#ff7043', '#ffb74d', '#9ccc65']
    }]
  },
  options: {
    responsive: true,
    plugins: { legend: { position: 'bottom' } }
  }
});

new Chart(document.getElementById('ingChart'), {
  type: 'bar',
  data: {
    labels: <?= json_encode($ingLabels) ?>,
    datasets: [{
      label: 'Qty Used',
      data: <?= json_encode($ingQty) ?>,
      backgroundColor: 'var(--red)',
      borderRadius: 6
    }]
  },
  options: {
    indexAxis: 'y',
    responsive: true,
    scales: { x: { beginAtZero: true } }
  }
});
</script>

</body>
</html>
