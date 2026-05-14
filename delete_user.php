<?php
require_once 'auth_check.php';
require_once 'connection/db_connect.php';

// Security check: Only admins can delete
if ($_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php?error=Access Denied.");
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Safety check: Cannot delete yourself
    if ($id == $_SESSION['user_id']) {
        header("Location: dashboard.php?error=You cannot delete your own account.");
        exit();
    }

    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    if ($stmt->execute([$id])) {
        header("Location: dashboard.php?success=User deleted successfully.");
    } else {
        header("Location: dashboard.php?error=Failed to delete user.");
    }
}
?>