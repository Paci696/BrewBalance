<?php 
require_once 'auth_check.php'; 
require_once 'connection/db_connect.php';
include 'header.php'; 

if ($_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php?error=Unauthorized.");
    exit();
}

$msg = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_product'])) {
    $name = trim($_POST['name']);
    $price = $_POST['price'];
    $category = $_POST['category'];
    
    // 1. DUPLICATE CHECK
    $check = $pdo->prepare("SELECT id FROM products WHERE name = ?");
    $check->execute([$name]);
    
    if ($check->rowCount() > 0) {
        $msg = "<div class='alert alert-danger'>Error: '$name' already exists in the menu!</div>";
    } else {
        // 2. IMAGE UPLOAD LOGIC
        $target_dir = "pictures/";
        // Create a unique filename to prevent overwriting
        $file_ext = pathinfo($_FILES["product_image"]["name"], PATHINFO_EXTENSION);
        $file_name = time() . "_" . preg_replace("/[^a-zA-Z0-9]/", "_", $name) . "." . $file_ext;
        $target_file = $target_dir . $file_name;
        $image_path = "pictures/default_coffee.jpg"; // Fallback

        if (move_uploaded_file($_FILES["product_image"]["tmp_name"], $target_file)) {
            $image_path = $target_file;
        }

        // 3. DATABASE INSERTION
        $stmt = $pdo->prepare("INSERT INTO products (name, price, category, image_path) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$name, $price, $category, $image_path])) {
            $msg = "<div class='alert alert-success alert-dismissible fade show' role='alert'>
            Product added successfully!
            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
        </div>";
        } else {
            $msg = "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
            Error: This product already exists!
            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
        </div>";
        }
    }
}
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Menu Management</h2>
        <a href="dashboard.php" class="btn btn-secondary btn-sm">Back to Dashboard</a>
    </div>

    <?= $msg ?>

    <div class="row">
        <!-- Add Product Form -->
        <div class="col-md-4">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-dark text-white fw-bold">Add New Menu Item</div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Product Name</label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Spanish Latte" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Price (PHP)</label>
                            <input type="number" step="0.01" name="price" class="form-control" placeholder="0.00" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Category</label>
                            <select name="category" class="form-select" required>
                                <option value="Coffee">Coffee</option>
                                <option value="Non-Coffee">Non-Coffee</option>
                                <option value="Pastry">Pastry</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Product Image</label>
                            <input type="file" name="product_image" class="form-control" accept="image/*" required>
                        </div>
                        <button type="submit" name="add_product" class="btn btn-primary w-100 fw-bold">Save Product</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Product List Table -->
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Image</th>
                                <th>Details</th>
                                <th>Price</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
                            while ($p = $stmt->fetch()): ?>
                                <tr>
                                    <td>
                                        <img src="<?= htmlspecialchars($p['image_path']) ?>" 
                                             width="60" height="60" style="object-fit: cover;" 
                                             class="rounded shadow-sm border">
                                    </td>
                                    <td>
                                        <strong class="d-block"><?= htmlspecialchars($p['name']) ?></strong>
                                        <span class="badge bg-secondary" style="font-size: 0.7rem;"><?= $p['category'] ?></span>
                                    </td>
                                    <td class="fw-bold">₱<?= number_format($p['price'], 2) ?></td>
                                    <td class="text-end">
                                        <a href="delete_product.php?id=<?= $p['id'] ?>" 
                                           class="btn btn-outline-danger btn-sm" 
                                           onclick="return confirm('Deleting this will also remove linked recipes. Continue?')">
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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
</body>
</html>