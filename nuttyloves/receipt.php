<?php
include 'config.php';

$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

if ($order_id <= 0) {
  die("Invalid order ID.");
}

$order_query = $conn->query("SELECT * FROM orders WHERE OrderID = $order_id");
$order = $order_query ? $order_query->fetch_assoc() : null;

if (!$order) {
  echo "<h2 style='color:#c62828;'>Sorry, we couldn’t find your order.</h2>";
  echo "<p><a href='index.php'>← Back to Home</a></p>";
  exit();
}

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
    body { font-family: Arial; padding: 20px; background: #f9f9f9; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th, td { border: 1px solid #ddd; padding: 10px; text-align: center; }
    th { background-color: #2e7d32; color: white; }
    h2 { color: #2e7d32; }
    .btn {
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
    .button-container {
      text-align: center;
      margin-top: 20px;
    }
  </style>
</head>
<body>

<div id="receipt">
  <h2>Order Receipt</h2>
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
      <td colspan="3"><strong>Subtotal</strong></td>
      <td><strong>₱<?= number_format($order['TotalAmount'], 2) ?></strong></td>
    </tr>
    <tr>
      <td colspan="3"><strong>Tax (12%)</strong></td>
      <td>₱<?= number_format($order['TaxAmount'], 2) ?></td>
    </tr>
    <tr>
      <td colspan="3"><strong>Grand Total</strong></td>
      <td><strong>₱<?= number_format($order['GrandTotal'], 2) ?></strong></td>
    </tr>
  </table>
</div>

<div class="button-container">
  <button id="downloadBtn" class="btn">Download as JPG</button>
</div>

<p><a href="index.php">← Back to Shop</a></p>

<script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
<script>
document.getElementById('downloadBtn').addEventListener('click', function () {
  html2canvas(document.getElementById('receipt')).then(function(canvas) {
    let link = document.createElement('a');
    link.download = 'receipt-<?= $order_id ?>.jpg';
    link.href = canvas.toDataURL('image/jpeg');
    link.click();
  });
});
</script>

</body>
</html>
