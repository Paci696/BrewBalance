<?php
session_start();

// If the user is not logged in, kick them back to login.php
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>