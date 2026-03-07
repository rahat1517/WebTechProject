<?php if (!isset($pageTitle)) { $pageTitle = "Inventory Management System"; } ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
   
    <a href="#" class="logout-btn" onclick="openLogoutModal()">Logout</a>
<div id="logoutModal" class="modal">
    <div class="modal-content">
        <h2>Confirm Logout</h2>
        <p>Are you sure you want to logout?</p>

        <div class="modal-buttons">
            <button class="btn-cancel" onclick="closeLogoutModal()">No</button>
            <a href="logout.php" class="btn-logout">Yes, Logout</a>
        </div>
    </div>
</div>
    <div class="app">
    <aside class="sidebar" id="sidebar">
        <h2>IMS</h2>
        <nav>
    <a href="dashboard.php" class="<?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>">Dashboard</a>

    <?php if ($_SESSION['user_role'] === 'admin'): ?>
        <a href="products.php" class="<?= basename($_SERVER['PHP_SELF']) == 'products.php' ? 'active' : '' ?>">Products</a>
        <a href="suppliers.php" class="<?= basename($_SERVER['PHP_SELF']) == 'suppliers.php' ? 'active' : '' ?>">Suppliers</a>
        <a href="stock_in.php" class="<?= basename($_SERVER['PHP_SELF']) == 'stock_in.php' ? 'active' : '' ?>">Stock In</a>
        <a href="stock_out.php" class="<?= basename($_SERVER['PHP_SELF']) == 'stock_out.php' ? 'active' : '' ?>">Stock Out</a>
        <a href="orders.php" class="<?= basename($_SERVER['PHP_SELF']) == 'orders.php' ? 'active' : '' ?>">Sales Products</a>
        <a href="reports.php" class="<?= basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'active' : '' ?>">Reports</a>
    <?php else: ?>
        <a href="orders.php" class="<?= basename($_SERVER['PHP_SELF']) == 'orders.php' ? 'active' : '' ?>">Sales Products</a>
    <?php endif; ?>
</nav>
    </aside>
    <main class="main-content">
