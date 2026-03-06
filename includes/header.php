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
<div class="app">
    <aside class="sidebar">
        <h2>IMS</h2>
        <nav>
            <a href="dashboard.php">Dashboard</a>
            <a href="products.php">Products</a>
            <a href="suppliers.php">Suppliers</a>
            <a href="stock_in.php">Stock In</a>
            <a href="stock_out.php">Stock Out</a>
            <a href="reports.php">Reports</a>
            <a href="logout.php">Logout</a>
        </nav>
    </aside>
    <main class="main-content">
