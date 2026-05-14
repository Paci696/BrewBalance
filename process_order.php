<?php
require_once 'auth_check.php';
require_once 'connection/db_connect.php';

// Check if the correct POST key is used (cart_json matches your dashboard)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cart_json'])) {
    
    $cart = json_decode($_POST['cart_json'], true);
    $total_order_price = $_POST['total_price'];
    $user_id = $_SESSION['user_id'];

    if (empty($cart)) {
        header("Location: dashboard.php?error=Cart is empty.");
        exit();
    }

    try {
        $pdo->beginTransaction();

        foreach ($cart as $item) {
            $product_id = $item['id'];
            $qty_ordered = $item['qty'];

            // 1. Get Recipe ingredients for this product
            $stmt = $pdo->prepare("SELECT ingredient_id, required_amount FROM recipes WHERE product_id = ?");
            $stmt->execute([$product_id]);
            $ingredients = $stmt->fetchAll();

            // 2. Deduct Stock for each ingredient
            foreach ($ingredients as $ing) {
                $total_needed = $ing['required_amount'] * $qty_ordered;
                
                // Attempt to update stock only if enough exists
                $update = $pdo->prepare("UPDATE ingredients SET stock_quantity = stock_quantity - ? WHERE id = ? AND stock_quantity >= ?");
                $update->execute([$total_needed, $ing['ingredient_id'], $total_needed]);

                if ($update->rowCount() == 0) {
                    // Fetch ingredient name for better error reporting
                    $name_stmt = $pdo->prepare("SELECT name FROM ingredients WHERE id = ?");
                    $name_stmt->execute([$ing['ingredient_id']]);
                    $ing_name = $name_stmt->fetchColumn();
                    throw new Exception("Insufficient stock for: " . $ing_name);
                }
            }

            // 3. Log Sale for this specific item
            $sale = $pdo->prepare("INSERT INTO sales (user_id, product_id, total_price) VALUES (?, ?, ?)");
            $sale->execute([$user_id, $product_id, ($item['price'] * $qty_ordered)]);
        }

        $pdo->commit();
        header("Location: dashboard.php?success=Order processed successfully!");
        exit();

    } catch (Exception $e) {
        $pdo->rollBack();
        header("Location: dashboard.php?error=" . urlencode($e->getMessage()));
        exit();
    }
} else {
    // If someone tries to access this file directly without POSTing
    header("Location: dashboard.php");
    exit();
}