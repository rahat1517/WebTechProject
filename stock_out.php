<?php
require_once 'includes/auth_check.php';

$pageTitle = "Stock Out";
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = (int)$_POST['product_id'];
    $quantity = (int)$_POST['quantity'];
    $note = trim($_POST['note']);

    $stmt = $pdo->prepare("SELECT quantity FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $currentStock = (int)$stmt->fetchColumn();

    if ($quantity > $currentStock) {
        $message = "Not enough stock available.";
    } else {
        $stmt = $pdo->prepare("UPDATE products SET quantity = quantity - ? WHERE id = ?");
        $stmt->execute([$quantity, $product_id]);

        $stmt = $pdo->prepare("INSERT INTO stock_movements (product_id, movement_type, quantity, note) VALUES (?, 'OUT', ?, ?)");
        $stmt->execute([$product_id, $quantity, $note]);

        $message = "Stock removed successfully.";
    }
}

$products = $pdo->query("SELECT * FROM products ORDER BY name ASC")->fetchAll();
$history = $pdo->query("SELECT sm.*, p.name AS product_name FROM stock_movements sm JOIN products p ON sm.product_id = p.id WHERE sm.movement_type = 'OUT' ORDER BY sm.id DESC")->fetchAll();

require_once 'includes/header.php';
?>
<h1>Stock Out</h1>
<p class="info"><?= htmlspecialchars($message) ?></p>

<section class="panel">
    <h2>Issue Stock</h2>
    <form method="POST" class="grid-form">
        <select name="product_id" required>
            <option value="">Select Product</option>
            <?php foreach ($products as $product): ?>
                <option value="<?= $product['id'] ?>"><?= htmlspecialchars($product['name']) ?> (<?= $product['quantity'] ?>)</option>
            <?php endforeach; ?>
        </select>
        <input type="number" name="quantity" placeholder="Quantity" min="1" required>
        <input type="text" name="note" placeholder="Note / Customer / Department">
        <button type="submit">Save</button>
    </form>
</section>

<section class="panel">
    <h2>Stock Out History</h2>
    <table>
        <tr>
            <th>Product</th>
            <th>Quantity</th>
            <th>Note</th>
            <th>Date</th>
        </tr>
        <?php foreach ($history as $row): ?>
        <tr>
            <td><?= htmlspecialchars($row['product_name']) ?></td>
            <td><?= $row['quantity'] ?></td>
            <td><?= htmlspecialchars($row['note']) ?></td>
            <td><?= $row['created_at'] ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</section>
<?php require_once 'includes/footer.php'; ?>