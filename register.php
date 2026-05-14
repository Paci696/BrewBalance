<?php 
include 'header.php'; 
require_once 'connection/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = $_POST['username'];
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    try {
        $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $stmt->execute([$user, $pass, $role]);
        
        // Show success and redirect after 2 seconds
        echo "<div class='alert alert-success mt-3 container text-center'>
                Registration Successful! Redirecting to login...
              </div>";
        header("refresh:2;url=login.php"); 
    } catch (PDOException $e) {
        echo "<div class='alert alert-danger mt-3 container'>Error: " . $e->getMessage() . "</div>";
    }
}
?>

<div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="card shadow" style="width: 450px;">
        <div class="card-body p-5">
            <h3 class="text-center mb-4">Create Account</h3>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-select">
                        <option value="staff">Staff (POS User)</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-coffee w-100 mb-3">Register User</button>
                <div class="text-center">
                    <a href="login.php" class="text-muted text-decoration-none small">Already have an account? Login</a>
                </div>
            </form>
        </div>
    </div>
</div>