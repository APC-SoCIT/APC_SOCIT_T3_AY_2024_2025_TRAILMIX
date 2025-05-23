<?php
session_start();
include 'config.php';

// Simulate customer ID (replace with $_SESSION['user']['CustomerID'] when logged in)
$customer_id = 1;

// Redirect if cart is empty
if (empty($_SESSION['cart'])) {
    header('Location: cart.php');
    exit();
}

// Step 1: Calculate total
$total = 0;
$ids = implode(',', array_keys($_SESSION['cart']));
$result = $conn->query("SELECT * FROM products WHERE ProductID IN ($ids)");

while ($row = $result->fetch_assoc()) {
    $qty = $_SESSION['cart'][$row['ProductID']];
    $total += $row['Price'] * $qty;
}

// Step 2: Insert order into `orders` table
$conn->query("INSERT INTO orders (CustomerID, TotalAmount) VALUES ($customer_id, $total)");
$order_id = $conn->insert_id;

// Step 3: Insert items into `order_items` table
$result->data_seek(0); // rewind result set

while ($row = $result->fetch_assoc()) {
    $product_id = $row['ProductID'];
    $qty = $_SESSION['cart'][$product_id];
    $price = $row['Price'];

    $conn->query("INSERT INTO order_items (OrderID, ProductID, Quantity, Price)
                  VALUES ($order_id, $product_id, $qty, $price)");
}

// Step 4: Clear cart
unset($_SESSION['cart']);

// Step 5: Redirect to receipt page
header("Location: receipt.php?order_id=$order_id");
exit();
?>
