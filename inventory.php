<?php 
require_once 'auth_check.php'; 
require_once 'connection/db_connect.php';
include 'header.php'; 

if ($_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php?error=Unauthorized.");
    exit();
}

$msg = "";

// 1. HANDLE ADDING NEW INGREDIENT (With Duplicate Check)
if (isset($_POST['add_ingredient'])) {
    $name = trim($_POST['name']);
    $qty = $_POST['qty'];
    $unit = $_POST['unit']; // Restricted to g, ml, pcs via dropdown
    $reorder = $_POST['reorder'];

    // Duplicate Check
    $check = $pdo->prepare("SELECT id FROM ingredients WHERE name = ?");
    $check->execute([$name]);

    if ($check->rowCount() > 0) {
        $msg = "<div class='alert alert-danger'>Error: '$name' is already in your inventory!</div>";
    } else {
        $stmt = $pdo->prepare("INSERT INTO ingredients (name, stock_quantity, unit, reorder_level) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$name, $qty, $unit, $reorder])) {
            $msg = "<div class='alert alert-success'>Ingredient '$name' added successfully.</div>";
        } else {
            $msg = "<div class='alert alert-danger'>Database error: Could not save ingredient.</div>";
        }
    }
}

// 2. HANDLE STOCK UPDATE
if (isset($_POST['update_stock'])) {
    $id = $_POST['ing_id'];
    $add_val = $_POST['add_val'];

    $stmt = $pdo->prepare("UPDATE ingredients SET stock_quantity = stock_quantity + ? WHERE id = ?");
    $stmt->execute([$add_val, $id]);
    $msg = "<div class='alert alert-success'>Stock replenished successfully.</div>";
}
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Inventory Hub</h2>
        <a href="dashboard.php" class="btn btn-secondary btn-sm shadow-sm">Back to Dashboard</a>
    </div>

    <?= $msg ?>

    <div class="row">
        <!-- Add New Ingredient Form -->
        <div class="col-md-4">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-success text-white fw-bold">Add New Raw Material</div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-2">
                            <label class="small fw-bold">Ingredient Name</label>
                            <input type="text" name="name" class="form-control form-control-sm" placeholder="e.g. Fresh Milk" required>
                        </div>
                        <div class="mb-2">
                            <label class="small fw-bold">Initial Stock Level</label>
                            <input type="number" step="0.01" name="qty" class="form-control form-control-sm" placeholder="0.00" required>
                        </div>
                        <div class="mb-2">
                            <label class="small fw-bold">Unit of Measurement</label>
                            <select name="unit" class="form-select form-select-sm" required>
                                <option value="" disabled selected>Select Unit</option>
                                <option value="g">Grams (g)</option>
                                <option value="ml">Milliliters (ml)</option>
                                <option value="pcs">Pieces (pcs)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="small fw-bold">Low Stock Alert Level</label>
                            <input type="number" step="0.01" name="reorder" class="form-control form-control-sm" placeholder="e.g. 500" required>
                        </div>
                        <button type="submit" name="add_ingredient" class="btn btn-success w-100 btn-sm fw-bold">Save Ingredient</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Inventory List -->
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th class="ps-3">Ingredient</th>
                                <th>Current Stock</th>
                                <th>Restock</th>
                                <th class="text-end pe-3">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stmt = $pdo->query("SELECT * FROM ingredients ORDER BY name ASC");
                            while ($ing = $stmt->fetch()): 
                                $is_low = $ing['stock_quantity'] <= $ing['reorder_level'];
                            ?>
                                <tr class="<?= $is_low ? 'table-danger' : '' ?>">
                                    <td class="ps-3">
                                        <strong class="text-dark"><?= htmlspecialchars($ing['name']) ?></strong><br>
                                        <small class="text-muted">Threshold: <?= number_format($ing['reorder_level'], 2) ?><?= $ing['unit'] ?></small>
                                    </td>
                                    <td>
                                        <span class="badge <?= $is_low ? 'bg-danger animate-pulse' : 'bg-primary' ?> fs-6 shadow-sm">
                                            <?= number_format($ing['stock_quantity'], 2) ?> <?= $ing['unit'] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <form method="POST" class="input-group input-group-sm" style="max-width: 160px;">
                                            <input type="hidden" name="ing_id" value="<?= $ing['id'] ?>">
                                            <input type="number" step="0.01" name="add_val" class="form-control" placeholder="Qty" required>
                                            <button type="submit" name="update_stock" class="btn btn-outline-success">Update</button>
                                        </form>
                                    </td>
                                    <td class="text-end pe-3">
                                        <a href="delete_ingredient.php?id=<?= $ing['id'] ?>" 
                                           class="btn btn-sm btn-outline-danger shadow-sm" 
                                           onclick="return confirm('Deleting this will remove it from any linked recipes. Continue?')">
                                            Delete
                                        </a>
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
<script>
    // Wait for the DOM to be fully loaded
    document.addEventListener("DOMContentLoaded", function() {
        // Find all elements with the 'alert' class
        const alerts = document.querySelectorAll('.alert');
        
        alerts.forEach(function(alert) {
            // Set a timer to start the fade out after 5000ms (5 seconds)
            setTimeout(function() {
                // Use Bootstrap's transition to fade out
                alert.style.transition = "opacity 0.5s ease";
                alert.style.opacity = "0";
                
                // Physically remove the element from the DOM after it fades
                setTimeout(function() {
                    alert.remove();
                }, 500);
            }, 5000);
        });
    });
</script>