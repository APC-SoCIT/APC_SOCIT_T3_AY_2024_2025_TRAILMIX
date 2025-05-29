<?php
session_start();
include 'config.php';

$id = intval($_GET['id']);
$conn->query("DELETE FROM products WHERE ProductID = $id");

header("Location: admin_manage_products.php");
exit();
