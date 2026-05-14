<?php 
require_once 'auth_check.php'; 
require_once 'connection/db_connect.php';
include 'header.php'; 

if ($_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php?error=Unauthorized access.");
    exit();
}

// Handle the Update Logic
if (isset($_POST['update_amount'])) {
    $recipe_id = $_POST['recipe_id'];
    $new_amount = $_POST['new_amount'];

    try {
        $stmt = $pdo->prepare("UPDATE recipes SET required_amount = ? WHERE id = ?");
        $stmt->execute([$new_amount, $recipe_id]);
        $msg = "<div class='alert alert-success'>Recipe updated successfully!</div>";
    } catch (PDOException $e) {
        $msg = "<div class='alert alert-danger'>Update failed: " . $e->getMessage() . "</div>";
    }
}
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Recipe Editor</h2>
        <a href="dashboard.php" class="btn btn-secondary btn-sm">Back to Dashboard</a>
    </div>

    <?= isset($msg) ? $msg : '' ?>

    <div class="card shadow-sm">
        <div class="card-header bg-warning text-dark fw-bold">Modify Ingredient Portions</div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Menu Item</th>
                        <th>Ingredient</th>
                        <th>Current Amount</th>
                        <th style="width: 250px;">New Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT r.id, p.name as product_name, i.name as ingredient_name, r.required_amount, i.unit 
                            FROM recipes r
                            JOIN products p ON r.product_id = p.id
                            JOIN ingredients i ON r.ingredient_id = i.id
                            ORDER BY p.name ASC";
                    $stmt = $pdo->query($sql);
                    
                    if ($stmt->rowCount() > 0):
                        while ($row = $stmt->fetch()): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($row['product_name']) ?></strong></td>
                                <td><?= htmlspecialchars($row['ingredient_name']) ?></td>
                                <td><?= $row['required_amount'] ?> <?= $row['unit'] ?></td>
                                <td>
                                    <form method="POST" class="input-group input-group-sm">
                                        <input type="hidden" name="recipe_id" value="<?= $row['id'] ?>">
                                        <input type="number" step="0.01" name="new_amount" class="form-control" placeholder="New Qty" required>
                                        <span class="input-group-text"><?= $row['unit'] ?></span>
                                        <button type="submit" name="update_amount" class="btn btn-warning">Update</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; 
                    else: ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">No recipes found. Go to "Recipe Master" to create one.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>