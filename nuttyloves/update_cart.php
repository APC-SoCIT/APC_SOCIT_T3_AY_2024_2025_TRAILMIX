<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'], $_POST['quantity'])) {
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);

    if ($quantity > 0) {
        $_SESSION['cart'][$product_id] = $quantity;
    } else {
        unset($_SESSION['cart'][$product_id]);
    }

    header("Location: cart.php");
    exit();
}

if (isset($_GET['action']) && isset($_GET['product_id'])) {
    $product_id = intval($_GET['product_id']);
    $action = $_GET['action'];

    switch ($action) {
        case 'increase':
            $_SESSION['cart'][$product_id] = isset($_SESSION['cart'][$product_id])
                ? $_SESSION['cart'][$product_id] + 1
                : 1;
            break;

        case 'decrease':
            if (isset($_SESSION['cart'][$product_id])) {
                $_SESSION['cart'][$product_id]--;
                if ($_SESSION['cart'][$product_id] <= 0) {
                    unset($_SESSION['cart'][$product_id]);
                }
            }
            break;

        case 'remove':
            unset($_SESSION['cart'][$product_id]);
            break;
    }

    header("Location: cart.php");
    exit();
}

header("Location: cart.php");
exit();
