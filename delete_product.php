<?php
require_once 'auth_check.php';
require_once 'connection/db_connect.php';

// Only admins can delete products
if ($_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php?error=Unauthorized.");
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    try {
        $pdo->beginTransaction();

        // 1. Delete linked recipes first (Referential Integrity)
        $stmt1 = $pdo->prepare("DELETE FROM recipes WHERE product_id = ?");
        $stmt1->execute([$id]);

        // 2. Delete the product
        $stmt2 = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $stmt2->execute([$id]);

        $pdo->commit();
        header("Location: products.php?success=Product and linked recipes removed.");
    } catch (Exception $e) {
        $pdo->rollBack();
        header("Location: products.php?error=Failed to delete product.");
    }
} else {
    header("Location: products.php");
}
?>