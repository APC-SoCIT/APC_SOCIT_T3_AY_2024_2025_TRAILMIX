<?php
session_start();

if (isset($_GET['action']) && isset($_GET['product_id'])) {
    $product_id = intval($_GET['product_id']);
    $action = $_GET['action'];

    if ($action === 'increase') {
        $_SESSION['cart'][$product_id]++;
    } elseif ($action === 'decrease') {
        $_SESSION['cart'][$product_id]--;
        if ($_SESSION['cart'][$product_id] <= 0) {
            unset($_SESSION['cart'][$product_id]);
        }
    } elseif ($action === 'remove') {
        unset($_SESSION['cart'][$product_id]);
    }
}

header('Location: cart.php');
exit();
