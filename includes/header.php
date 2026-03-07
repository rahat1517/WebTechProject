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
   
    <a href="logout.php" class="logout-btn" onclick="return confirmLogout()">Logout</a>

<div class="app">
    <aside class="sidebar" id="sidebar">
        <h2>IMS</h2>
        <nav>
            <a href="dashboard.php" class="<?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>">Dashboard</a>
            <a href="products.php" class="<?= basename($_SERVER['PHP_SELF']) == 'products.php' ? 'active' : '' ?>">Products</a>
            <a href="suppliers.php" class="<?= basename($_SERVER['PHP_SELF']) == 'suppliers.php' ? 'active' : '' ?>">Suppliers</a>
            <a href="orders.php" class="<?= basename($_SERVER['PHP_SELF']) == 'orders.php' ? 'active' : '' ?>">Orders</a>
            <a href="stock_in.php" class="<?= basename($_SERVER['PHP_SELF']) == 'stock_in.php' ? 'active' : '' ?>">Stock In</a>
            <a href="stock_out.php" class="<?= basename($_SERVER['PHP_SELF']) == 'stock_out.php' ? 'active' : '' ?>">Stock Out</a>
            <a href="reports.php" class="<?= basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'active' : '' ?>">Reports</a>

        </nav>
    </aside>
    <main class="main-content">
