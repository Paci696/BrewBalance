<?php
require_once 'auth_check.php';
require_once 'connection/db_connect.php';

// Security: Only admins can remove recipe links
if ($_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php?error=Unauthorized.");
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $pdo->prepare("DELETE FROM recipes WHERE id = ?");
    
    if ($stmt->execute([$id])) {
        header("Location: manage_recipes.php?success=Ingredient removed from recipe.");
    } else {
        header("Location: manage_recipes.php?error=Failed to remove ingredient.");
    }
}
?>