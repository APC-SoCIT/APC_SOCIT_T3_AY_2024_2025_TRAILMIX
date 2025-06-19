<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['Role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: admin_manage_inventory.php");
    exit();
}

$product_id = intval($_POST['product_id']);
$new_stock  = intval($_POST['new_stock']);

if ($product_id <= 0 || $new_stock < 0) {
    header("Location: admin_manage_inventory.php?error=invalid_input");
    exit();
}

$stmt = $conn->prepare("SELECT StockQuantity FROM products WHERE ProductID = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$currentRow = $stmt->get_result()->fetch_assoc();

if (!$currentRow) {
    header("Location: admin_manage_inventory.php?error=product_not_found");
    exit();
}

$current_stock = (int)$currentRow['StockQuantity'];
$added_stock   = $new_stock - $current_stock;          

$conn->begin_transaction();

try {

    if ($added_stock > 0) {
        $recipeSQL =
            "SELECT ri.IngredientID,
                    i.Name            AS IngredientName,
                    ri.QuantityUsed   AS QtyPerProduct,
                    i.StockQuantity   AS IngStock
             FROM   recipe_ingredients ri
             JOIN   ingredients i ON i.IngredientID = ri.IngredientID
             WHERE  ri.ProductID = ?";

        $check = $conn->prepare($recipeSQL);
        $check->bind_param("i", $product_id);
        $check->execute();
        $recipe = $check->get_result();

        while ($row = $recipe->fetch_assoc()) {
            $required = $row['QtyPerProduct'] * $added_stock;
            if ($row['IngStock'] < $required) {
                $conn->rollback();
                $insufficient = urlencode($row['IngredientName']);
                header("Location: admin_manage_inventory.php?error=lowstock&item={$insufficient}");
                exit();
            }
        }

        $recipe->data_seek(0);               
        $deduct = $conn->prepare(
            "UPDATE ingredients
               SET StockQuantity = StockQuantity - ?
             WHERE IngredientID  = ?"
        );
        while ($row = $recipe->fetch_assoc()) {
            $required = $row['QtyPerProduct'] * $added_stock;
            $deduct->bind_param("di", $required, $row['IngredientID']);
            $deduct->execute();
        }
    }

    $update = $conn->prepare("UPDATE products SET StockQuantity = ? WHERE ProductID = ?");
    $update->bind_param("ii", $new_stock, $product_id);
    $update->execute();

    $conn->commit();
    header("Location: admin_manage_inventory.php?success=1");
    exit();

} catch (Exception $e) {
    $conn->rollback();
    error_log("Stock update failed: " . $e->getMessage());
    header("Location: admin_manage_inventory.php?error=server_error");
    exit();
}
