<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$customer_id = 1;
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
$customer_id = $_SESSION['user']['CustomerID'];
if (empty($_SESSION['cart'])) {
    header('Location: cart.php');
    exit();
}

$total = 0;
$ids = implode(',', array_keys($_SESSION['cart']));
echo "<pre>";
print_r($_SESSION['cart']);
echo "</pre>";
$result = $conn->query("SELECT * FROM products WHERE ProductID IN ($ids)");

if (!$result) {
    die("Failed to fetch products: " . $conn->error);
}

while ($row = $result->fetch_assoc()) {
    $qty = $_SESSION['cart'][$row['ProductID']];
    $total += $row['Price'] * $qty;
}

$order_sql = "INSERT INTO orders (CustomerID, TotalAmount) VALUES ($customer_id, $total)";
if (!$conn->query($order_sql)) {
    die("Order insert failed: " . $conn->error);
}
$order_id = $conn->insert_id;

$result->data_seek(0);
while ($row = $result->fetch_assoc()) {
    $product_id = $row['ProductID'];
    $qty = $_SESSION['cart'][$product_id];
    $price = $row['Price'];

    $item_sql = "INSERT INTO order_items (OrderID, ProductID, Quantity, Price)
                 VALUES ($order_id, $product_id, $qty, $price)";
    if (!$conn->query($item_sql)) {
        die("Order item insert failed: " . $conn->error);
    }
}

unset($_SESSION['cart']);
header("Location: receipt.php?order_id=$order_id");
exit();
?>
