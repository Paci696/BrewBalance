<?php
require_once 'auth_check.php';
require_once 'connection/db_connect.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php?error=Unauthorized.");
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    try {
        $pdo->beginTransaction();

        // 1. Remove this ingredient from all recipes first
        $stmt1 = $pdo->prepare("DELETE FROM recipes WHERE ingredient_id = ?");
        $stmt1->execute([$id]);

        // 2. Delete the ingredient from inventory
        $stmt2 = $pdo->prepare("DELETE FROM ingredients WHERE id = ?");
        $stmt2->execute([$id]);

        $pdo->commit();
        header("Location: inventory.php?success=Ingredient deleted and recipes updated.");
    } catch (Exception $e) {
        $pdo->rollBack();
        header("Location: inventory.php?error=Failed to delete ingredient.");
    }
} else {
    header("Location: inventory.php");
}
?>