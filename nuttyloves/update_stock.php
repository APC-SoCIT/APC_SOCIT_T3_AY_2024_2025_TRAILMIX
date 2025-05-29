<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = intval($_POST['product_id']);
    $new_stock = intval($_POST['new_stock']);

    // Validate inputs
    if ($product_id > 0 && $new_stock >= 0) {
        $stmt = $conn->prepare("UPDATE products SET StockQuantity = ? WHERE ProductID = ?");
        $stmt->bind_param("ii", $new_stock, $product_id);
        $stmt->execute();
    }
}

header("Location: admin_manage_inventory.php");
exit();
