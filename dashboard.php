<?php
require_once 'includes/auth_check.php';

$pageTitle = "Dashboard";
$totalProducts = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$totalSuppliers = $pdo->query("SELECT COUNT(*) FROM suppliers")->fetchColumn();
$totalStock = $pdo->query("SELECT COALESCE(SUM(quantity), 0) FROM products")->fetchColumn();
$lowStock = $pdo->query("SELECT COUNT(*) FROM products WHERE quantity <= reorder_level")->fetchColumn();

require_once 'includes/header.php';
?>
<h1>Welcome, <?= htmlspecialchars($_SESSION['user_name']) ?></h1>
<p class="info">Logged in as: <?= htmlspecialchars(ucfirst($_SESSION['user_role'])) ?></p>
<div class="cards">
    <div class="card">
        <h3>Total Products</h3>
        <p><?= $totalProducts ?></p>
    </div>
    <div class="card">
        <h3>Total Suppliers</h3>
        <p><?= $totalSuppliers ?></p>
    </div>
    <div class="card">
        <h3>Total Stock</h3>
        <p><?= $totalStock ?></p>
    </div>
    <div class="card danger">
        <h3>Low Stock Alerts</h3>
        <p><?= $lowStock ?></p>
    </div>
</div>

<section class="panel">
    <h2>Low Stock Products</h2>
    <table>
        <tr>
            <th>Name</th>
            <th>Category</th>
            <th>Quantity</th>
            <th>Reorder Level</th>
        </tr>
        <?php
        $stmt = $pdo->query("SELECT name, category, quantity, reorder_level FROM products WHERE quantity <= reorder_level ORDER BY quantity ASC");
        foreach ($stmt as $row): ?>
        <tr>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= htmlspecialchars($row['category']) ?></td>
            <td><?= $row['quantity'] ?></td>
            <td><?= $row['reorder_level'] ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</section>
<?php require_once 'includes/footer.php'; ?>