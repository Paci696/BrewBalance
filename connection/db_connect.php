<?php
$host = 'localhost';
$db   = 'brew_balance'; 
$user = 'root';
$pass = ''; 
$charset = 'utf8mb4';

// Set up the Data Source Name (DSN)
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

// Options for PDO: 
// 1. Throw exceptions on error
// 2. Return data as associative arrays
// 3. Disable emulated prepared statements for better security
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     // If connection fails, stop everything and show the error
     die("Database connection failed: " . $e->getMessage());
}
?>

