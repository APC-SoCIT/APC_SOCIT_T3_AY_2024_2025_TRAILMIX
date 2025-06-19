<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user']['UserID'];

if (empty($_SESSION['cart'])) {
    header('Location: cart.php');
    exit();
}

$ids = implode(',', array_keys($_SESSION['cart']));
$product_sql = "SELECT ProductID, Name, Price, StockQuantity FROM products WHERE ProductID IN ($ids)";
$result = $conn->query($product_sql);

if (!$result) {
    die("Failed to fetch products: " . $conn->error);
}

$insufficient = [];
$total = 0;

while ($row = $result->fetch_assoc()) {
    $pid  = $row['ProductID'];
    $need = $_SESSION['cart'][$pid];
    $have = $row['StockQuantity'];

    if ($need > $have) {
        $insufficient[] = "{$row['Name']} (needed {$need}, available {$have})";
    } else {
        $total += $row['Price'] * $need; // Only count items in stock
    }
}

if (!empty($insufficient)) {
    echo "<h2 style='color:#c62828;'>Insufficient stock for:</h2><ul>";
    foreach ($insufficient as $item) {
        echo "<li>$item</li>";
    }
    echo "</ul><p><a href='cart.php'>← Return to cart</a></p>";
    exit();
}

$tax   = $total * 0.12;
$grand = $total + $tax;

$conn->begin_transaction();

try {
    $stmt = $conn->prepare(
        "INSERT INTO orders (UserID, TotalAmount, TaxAmount, GrandTotal)
         VALUES (?, ?, ?, ?)"
    );
    $stmt->bind_param("iddd", $user_id, $total, $tax, $grand);
    $stmt->execute();
    $order_id = $stmt->insert_id;

    $result->data_seek(0);

    while ($row = $result->fetch_assoc()) {
        $pid   = $row['ProductID'];
        $qty   = $_SESSION['cart'][$pid];
        $price = $row['Price'];

        $oi = $conn->prepare(
            "INSERT INTO order_items (OrderID, ProductID, Quantity, Price)
             VALUES (?, ?, ?, ?)"
        );
        $oi->bind_param("iiid", $order_id, $pid, $qty, $price);
        $oi->execute();

        $upd = $conn->prepare(
            "UPDATE products SET StockQuantity = StockQuantity - ? WHERE ProductID = ?"
        );
        $upd->bind_param("ii", $qty, $pid);
        $upd->execute();
    }

    $conn->commit();
    unset($_SESSION['cart']);
    header("Location: receipt.php?order_id=$order_id");
    exit();

} catch (Exception $e) {
    $conn->rollback();
    die("Checkout failed: " . $e->getMessage());
}
?>
