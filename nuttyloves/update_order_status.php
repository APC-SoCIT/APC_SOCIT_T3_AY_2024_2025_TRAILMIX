<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['new_status'])) {
    $order_id = intval($_POST['order_id']);
    $status = $_POST['new_status'];

    $stmt = $conn->prepare("UPDATE orders SET Status = ? WHERE OrderID = ?");
    $stmt->bind_param("si", $status, $order_id);
    $stmt->execute();
}

header("Location: admin_manage_orders.php");
exit();
