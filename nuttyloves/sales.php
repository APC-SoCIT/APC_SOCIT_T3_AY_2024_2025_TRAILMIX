<?php
include 'config.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product = $_POST['product'];
    $price = $_POST['price'];
    $stmt = $conn->prepare("INSERT INTO sales (product_name, price) VALUES (?, ?)");
    $stmt->bind_param("sd", $product, $price);
    $stmt->execute();
    echo "<p>Thank you for buying $product!</p><a href='index.php'>Back to Home</a>";
} else {
    echo "<h2>Sales Records</h2>";
    $result = $conn->query("SELECT * FROM sales");
    while ($row = $result->fetch_assoc()) {
        echo "<div class='product'>";
        echo "<strong>{$row['product_name']}</strong> - ₱{$row['price']}<br>Sold on: {$row['created_at']}";
        echo "</div>";
    }
}
?>