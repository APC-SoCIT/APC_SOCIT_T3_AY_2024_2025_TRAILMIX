<?php
include 'config.php';
echo "<h2>Inventory</h2>";
$result = $conn->query("SELECT * FROM inventory");
while ($row = $result->fetch_assoc()) {
    echo "<div class='inventory-item'>";
    echo "<strong>{$row['item_name']}</strong> - {$row['quantity']} in stock";
    echo "</div>";
}
?>