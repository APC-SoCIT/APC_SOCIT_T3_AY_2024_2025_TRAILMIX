<?php
session_start();
include 'config.php';

$product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($product_id > 0) {
    if (!isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] = 1;
    } else {
        $_SESSION['cart'][$product_id]++;
    }
}

header('Location: cart.php');
exit();
?>
