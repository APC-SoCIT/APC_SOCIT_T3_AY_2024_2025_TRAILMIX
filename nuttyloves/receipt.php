<?php
include 'config.php';

$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

$order = $conn->query("SELECT * FROM orders WHERE OrderID = $order_id")->fetch_assoc();
$items = $conn->query("SELECT p.Name, oi.Quantity, oi.Price
                       FROM order_items oi
                       JOIN products p ON p.ProductID = oi.ProductID
                       WHERE oi.OrderID = $order_id");
?>
<!DOCTYPE html>
<html>
<head>
  <title>Order Receipt</title>
  <style>
    body { font-family: Arial; padding: 20px; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th, td { border: 1px solid #ddd; padding: 10px; text-align: center; }
    th { background-color: #2e7d32; color: white; }
    h2 { color: #2e7d32; }
  </style>
</head>
<body>

<h2>🧾 Order Receipt</h2>
<p><strong>Order ID:</strong> <?= $order_id ?></p>
<p><strong>Date:</strong> <?= $order['OrderDate'] ?></p>
<p><strong>Status:</strong> <?= $order['Status'] ?></p>

<table>
  <tr>
    <th>Product</th>
    <th>Quantity</th>
    <th>Price</th>
    <th>Subtotal</th>
  </tr>
  <?php
  while ($row = $items->fetch_assoc()) {
    $subtotal = $row['Quantity'] * $row['Price'];
    echo "<tr>
            <td>{$row['Name']}</td>
            <td>{$row['Quantity']}</td>
            <td>₱" . number_format($row['Price'], 2) . "</td>
            <td>₱" . number_format($subtotal, 2) . "</td>
          </tr>";
  }
  ?>
  <tr>
    <td colspan="3"><strong>Total</strong></td>
    <td><strong>₱<?= number_format($order['TotalAmount'], 2) ?></strong></td>
  </tr>
</table>

<p><a href="index.php">← Back to Shop</a></p>

</body>
</html>
