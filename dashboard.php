<?php 
require_once 'auth_check.php'; 
require_once 'connection/db_connect.php';
include 'header.php'; 

// Status Messages
$status_msg = "";
if (isset($_GET['success'])) $status_msg = "<div class='alert alert-success alert-dismissible fade show' role='alert'>{$_GET['success']}<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
if (isset($_GET['error'])) $status_msg = "<div class='alert alert-danger alert-dismissible fade show' role='alert'>{$_GET['error']}<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
?>

<nav class="navbar navbar-expand-lg navbar-dark shadow-sm px-4 py-2 mb-4" style="background-color: #6f4e37;">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="dashboard.php">BrewBalance</a>
        <div class="d-flex align-items-center">
            <span class="text-white me-3 d-none d-md-block"><?= ucfirst($_SESSION['role']) ?>: <strong><?= htmlspecialchars($_SESSION['username']) ?></strong></span>
            <a href="logout.php" class="btn btn-sm btn-outline-light">Logout</a>
        </div>
    </div>
</nav>

<div class="container-fluid px-4 pb-5">
    <?= $status_msg ?>

    <?php if ($_SESSION['role'] == 'admin'): ?>
        <!-- ================= ADMIN SECTION ================= -->
        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <div class="card shadow-sm h-100 border-0">
                    <div class="card-header bg-primary text-white fw-bold">Revenue Analytics</div>
                    <div class="card-body text-center">
                        <?php $sales_today = $pdo->query("SELECT SUM(total_price) FROM sales WHERE DATE(sale_date) = CURDATE()")->fetchColumn() ?: 0; ?>
                        <h6 class="text-muted small">TOTAL REVENUE TODAY</h6>
                        <h2 class="fw-bold">₱<?= number_format($sales_today, 2) ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow-sm h-100 border-0">
                    <div class="card-header bg-dark text-white fw-bold">Inventory Health</div>
                    <div class="card-body text-center">
                        <?php $low_stock = $pdo->query("SELECT COUNT(*) FROM ingredients WHERE stock_quantity <= reorder_level")->fetchColumn(); ?>
                        <h6 class="text-muted small">LOW STOCK ITEMS</h6>
                        <h2 class="fw-bold text-danger"><?= $low_stock ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-md-3"><div class="card shadow-sm h-100 text-center border-0"><div class="card-body d-flex flex-column"><h6 class="text-uppercase text-muted small fw-bold">Step 1</h6><h5 class="fw-bold">Menu Items</h5><a href="products.php" class="btn btn-dark mt-auto btn-sm">Manage Products</a></div></div></div>
            <div class="col-md-3"><div class="card shadow-sm h-100 text-center border-0"><div class="card-body d-flex flex-column"><h6 class="text-uppercase text-muted small fw-bold">Step 2</h6><h5 class="fw-bold">Inventory Hub</h5><a href="inventory.php" class="btn btn-dark mt-auto btn-sm">Check Stocks</a></div></div></div>
            <div class="col-md-3"><div class="card shadow-sm h-100 text-center border-0"><div class="card-body d-flex flex-column"><h6 class="text-uppercase text-muted small fw-bold">Step 3</h6><h5 class="fw-bold">Recipe Master</h5><a href="manage_recipes.php" class="btn btn-dark mt-auto btn-sm">Link Recipes</a></div></div></div>
            <div class="col-md-3"><div class="card shadow-sm h-100 text-center border-0"><div class="card-body d-flex flex-column"><h6 class="text-uppercase text-muted small fw-bold">Maintenance</h6><h5 class="fw-bold">Recipe Editor</h5><a href="edit_recipes.php" class="btn btn-dark mt-auto btn-sm">Edit Recipes</a></div></div></div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-light fw-bold d-flex justify-content-between align-items-center">User Management <a href="register.php" class="btn btn-sm btn-primary">Add New User</a></div>
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead><tr><th>Username</th><th>Role</th><th>Date Created</th><th class="text-end">Action</th></tr></thead>
                    <tbody>
                        <?php $user_stmt = $pdo->query("SELECT id, username, role, created_at FROM users ORDER BY created_at DESC");
                        while ($user = $user_stmt->fetch()): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($user['username']) ?></strong></td>
                                <td><span class="badge <?= $user['role'] == 'admin' ? 'bg-dark' : 'bg-secondary' ?>"><?= ucfirst($user['role']) ?></span></td>
                                <td><?= date('M d, Y', strtotime($user['created_at'])) ?></td>
                                <td class="text-end"><?php if ($user['id'] != $_SESSION['user_id']): ?><a href="delete_user.php?id=<?= $user['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete user?')">Delete</a><?php else: ?><small class="text-muted">Logged In</small><?php endif; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

    <?php else: ?>
        <!-- ================= STAFF SECTION (POS WITH CART) ================= -->
        <div class="row">
            <div class="col-lg-8">
                <h3 class="mb-4 fw-bold">Point of Sale</h3>
                
                <ul class="nav nav-pills mb-4" id="menuTabs" role="tablist">
                    <li class="nav-item"><button class="nav-link active" data-bs-toggle="pill" data-bs-target="#coffee">Coffee</button></li>
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#non-coffee">Non-Coffee</button></li>
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#pastries">Pastries</button></li>
                </ul>

                <div class="tab-content">
                    <?php 
                    $categories = ['Coffee' => 'coffee', 'Non-Coffee' => 'non-coffee', 'Pastry' => 'pastries'];
                    foreach ($categories as $db_cat => $tab_id): 
                    ?>
                    <div class="tab-pane fade <?= $tab_id == 'coffee' ? 'show active' : '' ?>" id="<?= $tab_id ?>">
                        <div class="row row-cols-2 row-cols-md-3 row-cols-xl-4 g-3">
                            <?php
                            $stmt = $pdo->prepare("SELECT * FROM products WHERE category = ? AND is_available = 1");
                            $stmt->execute([$db_cat]);
                            while ($p = $stmt->fetch()): ?>
                                <div class="col">
                                    <div class="card h-100 shadow-sm border-0 text-center menu-card">
                                        <img src="<?= htmlspecialchars($p['image_path']) ?>" class="card-img-top" style="height: 120px; object-fit: cover;">
                                        <div class="card-body p-2 d-flex flex-column">
                                            <small class="fw-bold mb-1"><?= htmlspecialchars($p['name']) ?></small>
                                            <h6 class="text-success mb-2">₱<?= number_format($p['price'], 2) ?></h6>
                                            <button class="btn btn-dark btn-sm w-100 mt-auto" onclick="addToCart(<?= $p['id'] ?>, '<?= addslashes($p['name']) ?>', <?= $p['price'] ?>)">
                                                + Add to Cart
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- RIGHT SIDE: CART SYSTEM -->
            <div class="col-lg-4 mt-4 mt-lg-0">
                <div class="card shadow border-0 sticky-top" style="top: 20px; height: 85vh;">
                    <div class="card-header bg-dark text-white fw-bold d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-cart3 me-2"></i> Current Order</span>
                        <button class="btn btn-sm btn-outline-light" onclick="clearCart()">Clear</button>
                    </div>
                    <div class="card-body d-flex flex-column p-0">
                        <div id="cartItems" class="p-3 overflow-auto flex-grow-1" style="max-height: 60vh;">
                            <div class="text-center text-muted py-5">
                                <p>Cart is empty</p>
                            </div>
                        </div>
                        <div class="p-3 border-top bg-light">
                            <div class="d-flex justify-content-between fs-5 fw-bold mb-3">
                                <span>Total:</span>
                                <span class="text-success">₱<span id="cartTotalDisplay">0.00</span></span>
                            </div>
                            <button class="btn btn-success btn-lg w-100 fw-bold" onclick="showCheckoutModal()" id="checkoutBtn" disabled>
                                CHECKOUT
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Checkout Modal -->
        <div class="modal fade" id="checkoutModal" tabindex="-1">
            <div class="modal-dialog">
                <form action="process_order.php" method="POST" class="modal-content border-0 shadow">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title fw-bold">Finalize Order</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div id="modalOrderList" class="mb-3 small border-bottom pb-2"></div>
                        <div class="text-center my-3">
                            <p class="text-muted mb-0 small">TOTAL DUE</p>
                            <h1 class="fw-bold text-success">₱<span id="displayModalTotal">0.00</span></h1>
                        </div>
                        
                        <input type="hidden" name="cart_json" id="cart_json">
                        <input type="hidden" name="total_price" id="modal_total_val">

                        <div class="mb-3">
                            <label class="form-label fw-bold">Cash Received</label>
                            <input type="number" step="0.01" class="form-control form-control-lg fw-bold text-center" id="cashInput" oninput="updateChange()" required>
                        </div>
                        <div class="p-3 bg-light rounded text-center">
                            <span class="text-muted small">CHANGE</span>
                            <h2 class="fw-bold text-danger mb-0">₱<span id="changeDisplay">0.00</span></h2>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="submit" class="btn btn-success btn-lg w-100 py-3 fw-bold">PRINT & COMPLETE</button>
                    </div>
                </form>
            </div>
        </div>

        <script>
        let cart = [];

        function addToCart(id, name, price) {
            const existing = cart.find(item => item.id === id);
            if (existing) {
                existing.qty++;
            } else {
                cart.push({ id, name, price, qty: 1 });
            }
            renderCart();
        }

        function removeFromCart(index) {
            cart.splice(index, 1);
            renderCart();
        }

        function clearCart() {
            cart = [];
            renderCart();
        }

        function renderCart() {
            const container = document.getElementById('cartItems');
            const totalDisplay = document.getElementById('cartTotalDisplay');
            const btn = document.getElementById('checkoutBtn');
            
            if (cart.length === 0) {
                container.innerHTML = '<div class="text-center text-muted py-5"><p>Cart is empty</p></div>';
                totalDisplay.innerText = '0.00';
                btn.disabled = true;
                return;
            }

            btn.disabled = false;
            let total = 0;
            container.innerHTML = cart.map((item, i) => {
                total += item.price * item.qty;
                return `
                <div class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded bg-white">
                    <div>
                        <div class="fw-bold small">${item.name}</div>
                        <div class="text-muted small">₱${item.price.toFixed(2)} x ${item.qty}</div>
                    </div>
                    <div class="text-end">
                        <div class="fw-bold text-success small">₱${(item.price * item.qty).toFixed(2)}</div>
                        <button class="btn btn-sm text-danger p-0" onclick="removeFromCart(${i})">Remove</button>
                    </div>
                </div>`;
            }).join('');
            totalDisplay.innerText = total.toFixed(2);
        }

        function showCheckoutModal() {
            let total = cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
            document.getElementById('displayModalTotal').innerText = total.toFixed(2);
            document.getElementById('modal_total_val').value = total;
            document.getElementById('cart_json').value = JSON.stringify(cart);
            
            document.getElementById('modalOrderList').innerHTML = cart.map(item => 
                `<div class="d-flex justify-content-between"><span>${item.qty}x ${item.name}</span><span>₱${(item.price * item.qty).toFixed(2)}</span></div>`
            ).join('');

            const modal = new bootstrap.Modal(document.getElementById('checkoutModal'));
            modal.show();
        }

        function updateChange() {
            const total = parseFloat(document.getElementById('modal_total_val').value);
            const cash = parseFloat(document.getElementById('cashInput').value) || 0;
            const change = cash - total;
            document.getElementById('changeDisplay').innerText = change >= 0 ? change.toFixed(2) : '0.00';
        }
        </script>
    <?php endif; ?>
</div>

<style>
    .menu-card { cursor: pointer; transition: 0.2s; }
    .menu-card:hover { transform: scale(1.02); }
    .nav-pills .nav-link.active { background-color: #6f4e37; }
    .nav-pills .nav-link { color: #6f4e37; font-weight: bold; }
</style>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>