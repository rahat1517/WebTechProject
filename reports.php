<?php
require_once 'includes/auth_check.php';

$pageTitle = "Reports";

$inventoryValue = $pdo->query("SELECT COALESCE(SUM(quantity * price), 0) FROM products")->fetchColumn();
$inCount = $pdo->query("SELECT COALESCE(SUM(quantity), 0) FROM stock_movements WHERE movement_type = 'IN'")->fetchColumn();
$outCount = $pdo->query("SELECT COALESCE(SUM(quantity), 0) FROM stock_movements WHERE movement_type = 'OUT'")->fetchColumn();
$products = $pdo->query("SELECT * FROM products ORDER BY quantity ASC")->fetchAll();

require_once 'includes/header.php';
?>
<h1>Reports</h1>

<div class="cards">
    <div class="card">
        <h3>Total Inventory Value</h3>
        <p>$<?= number_format($inventoryValue, 2) ?></p>
    </div>
    <div class="card">
        <h3>Total Stock In</h3>
        <p><?= $inCount ?></p>
    </div>
    <div class="card">
        <h3>Total Stock Out</h3>
        <p><?= $outCount ?></p>
    </div>
</div>

<section class="panel">
    <h2>Current Inventory Report</h2>
    <button onclick="window.print()">Print Report</button>
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Category</th>
            <th>Quantity</th>
            <th>Price</th>
            <th>Total Value</th>
            <th>Status</th>
        </tr>
        <?php foreach ($products as $product): ?>
        <tr>
            <td><?= $product['id'] ?></td>
            <td><?= htmlspecialchars($product['name']) ?></td>
            <td><?= htmlspecialchars($product['category']) ?></td>
            <td><?= $product['quantity'] ?></td>
            <td>$<?= number_format($product['price'], 2) ?></td>
            <td>$<?= number_format($product['price'] * $product['quantity'], 2) ?></td>
            <td><?= ($product['quantity'] <= $product['reorder_level']) ? 'Low Stock' : 'OK' ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</section>
<?php require_once 'includes/footer.php'; ?>