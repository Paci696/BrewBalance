<?php 
require_once 'auth_check.php'; 
require_once 'connection/db_connect.php';
include 'header.php'; 

// Only Admins should access this
if ($_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php?error=Unauthorized access.");
    exit();
}

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_recipe'])) {
    $product_id = $_POST['product_id'];
    $ingredient_id = $_POST['ingredient_id'];
    $amount = $_POST['amount'];

    try {
        $stmt = $pdo->prepare("INSERT INTO recipes (product_id, ingredient_id, required_amount) VALUES (?, ?, ?)");
        $stmt->execute([$product_id, $ingredient_id, $amount]);
        $msg = "<div class='alert alert-success'>Recipe link added!</div>";
    } catch (PDOException $e) {
        $msg = "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
    }
}
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Recipe Master</h2>
        <a href="dashboard.php" class="btn btn-secondary btn-sm">Back to Dashboard</a>
    </div>

    <?= isset($msg) ? $msg : '' ?>

    <div class="row">
        <!-- LEFT: Add Recipe Form -->
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white fw-bold">Link Ingredient to Product</div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Select Product</label>
                            <select name="product_id" class="form-select" required>
                                <option value="">-- Choose Product --</option>
                                <?php
                                $prods = $pdo->query("SELECT id, name FROM products ORDER BY name ASC");
                                while($p = $prods->fetch()) echo "<option value='{$p['id']}'>{$p['name']}</option>";
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Select Ingredient</label>
                            <select name="ingredient_id" class="form-select" required>
                                <option value="">-- Choose Ingredient --</option>
                                <?php
                                $ings = $pdo->query("SELECT id, name, unit FROM ingredients ORDER BY name ASC");
                                while($i = $ings->fetch()) echo "<option value='{$i['id']}'>{$i['name']} ({$i['unit']})</option>";
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Required Amount</label>
                            <input type="number" step="0.01" name="amount" class="form-control" placeholder="e.g. 18.00" required>
                        </div>
                        <button type="submit" name="add_recipe" class="btn btn-primary w-100">Add to Recipe</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- RIGHT: Current Recipes List -->
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white fw-bold">Current Recipe Matrix</div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Ingredient</th>
                                <th>Amount</th>
                                <th>Action</th>
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
                            while ($row = $stmt->fetch()): ?>
                                <tr>
                                    <td><strong><?= $row['product_name'] ?></strong></td>
                                    <td><?= $row['ingredient_name'] ?></td>
                                    <td><?= $row['required_amount'] ?> <?= $row['unit'] ?></td>
                                    <td>
                                        <a href="delete_recipe.php?id=<?= $row['id'] ?>" class="text-danger" onclick="return confirm('Remove this ingredient from recipe?')">Remove</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>