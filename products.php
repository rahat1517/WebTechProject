<?php
require_once 'includes/auth_check.php';

$pageTitle = "Products";
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_product'])) {
        $stmt = $pdo->prepare("INSERT INTO products (name, category, quantity, price, reorder_level, supplier_id) VALUES (?, ?, ?, ?, ?, ?)");
        $supplierId = $_POST['supplier_id'] !== '' ? $_POST['supplier_id'] : null;
        $stmt->execute([
            trim($_POST['name']),
            trim($_POST['category']),
            (int)$_POST['quantity'],
            (float)$_POST['price'],
            (int)$_POST['reorder_level'],
            $supplierId
        ]);
        $message = "Product added successfully.";
    }

    if (isset($_POST['delete_product'])) {
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$_POST['product_id']]);
        $message = "Product deleted successfully.";
    }
}

$products = $pdo->query("SELECT p.*, s.name AS supplier_name FROM products p LEFT JOIN suppliers s ON p.supplier_id = s.id ORDER BY p.id DESC")->fetchAll();
$suppliers = $pdo->query("SELECT * FROM suppliers ORDER BY name ASC")->fetchAll();

require_once 'includes/header.php';
?>
<h1>Products</h1>
<p class="info"><?= htmlspecialchars($message) ?></p>

<section class="panel">
    <h2>Add Product</h2>
    <form method="POST" class="grid-form">
        <input type="hidden" name="add_product" value="1">
        <input type="text" name="name" placeholder="Product Name" required>
        <input type="text" name="category" placeholder="Category" required>
        <input type="number" name="quantity" placeholder="Quantity" min="0" required>
        <input type="number" step="0.01" name="price" placeholder="Price" min="0" required>
        <input type="number" name="reorder_level" placeholder="Reorder Level" min="0" required>
        <select name="supplier_id">
            <option value="">Select Supplier</option>
            <?php foreach ($suppliers as $supplier): ?>
                <option value="<?= $supplier['id'] ?>"><?= htmlspecialchars($supplier['name']) ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Add Product</button>
    </form>
</section>

<section class="panel">
    <h2>All Products</h2>
    <input type="text" id="tableSearch" placeholder="Search product...">
    <table id="dataTable">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Category</th>
            <th>Quantity</th>
            <th>Price</th>
            <th>Supplier</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        <?php foreach ($products as $product): ?>
        <tr>
            <td><?= $product['id'] ?></td>
            <td><?= htmlspecialchars($product['name']) ?></td>
            <td><?= htmlspecialchars($product['category']) ?></td>
            <td><?= $product['quantity'] ?></td>
            <td>$<?= number_format($product['price'], 2) ?></td>
            <td><?= htmlspecialchars($product['supplier_name'] ?? 'N/A') ?></td>
            <td><?= ($product['quantity'] <= $product['reorder_level']) ? 'Low Stock' : 'Available' ?></td>
            <td>
                <form method="POST" onsubmit="return confirmDelete();" class="inline-form">
                    <input type="hidden" name="delete_product" value="1">
                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                    <button type="submit" class="danger-btn">Delete</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</section>
<?php require_once 'includes/footer.php'; ?>