<?php
require_once 'includes/auth_check.php';
if ($_SESSION['user_role'] !== 'admin') {
    die("Access denied.");
}
$pageTitle = "Stock Out";
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //  Cast to int — prevents injection
    $product_id = (int)($_POST['product_id'] ?? 0);
    $quantity   = (int)($_POST['quantity']   ?? 0);
    $note       = trim($_POST['note']        ?? '');

    //  Server-side validation
    if ($product_id <= 0) {
        $message = "Please select a valid product.";
    } elseif ($quantity <= 0) {
        $message = "Quantity must be greater than 0.";
    } elseif ($quantity > 100000) {
        $message = "Quantity value is too large.";
    } elseif (strlen($note) > 500) {
        $message = "Note is too long.";
    } else {
        $stmt = $pdo->prepare("SELECT quantity FROM products WHERE id = ?");
        $stmt->execute([$product_id]);
        $row = $stmt->fetch();

        if (!$row) {
            $message = "Product not found.";
        } elseif ($quantity > (int)$row['quantity']) {
            $message = "Not enough stock. Available: " . (int)$row['quantity'];
        } else {
            $stmt = $pdo->prepare("UPDATE products SET quantity = quantity - ? WHERE id = ?");
            $stmt->execute([$quantity, $product_id]);

            $stmt = $pdo->prepare("INSERT INTO stock_movements (product_id, movement_type, quantity, note) VALUES (?, 'OUT', ?, ?)");
            $stmt->execute([$product_id, $quantity, $note]);

            $message = "Stock removed successfully.";
        }
    }
}

$products = $pdo->query("SELECT * FROM products ORDER BY name ASC")->fetchAll();
$history  = $pdo->query("SELECT sm.*, p.name AS product_name FROM stock_movements sm JOIN products p ON sm.product_id = p.id WHERE sm.movement_type = 'OUT' ORDER BY sm.id DESC")->fetchAll();

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
                <option value="<?= (int)$product['id'] ?>"><?= htmlspecialchars($product['name']) ?> (<?= (int)$product['quantity'] ?>)</option>
            <?php endforeach; ?>
        </select>
        <input type="number" name="quantity" placeholder="Quantity" min="1" max="100000" required>
        <input type="text"   name="note"     placeholder="Note / Customer / Department" maxlength="500">
        <button type="submit">Save</button>
    </form>
</section>

<section class="panel">
    <h2>Stock Out History</h2>
    <table>
        <tr>
            <th>Product</th><th>Quantity</th><th>Note</th><th>Date</th>
        </tr>
        <?php foreach ($history as $row): ?>
        <tr>
            <td><?= htmlspecialchars($row['product_name']) ?></td>
            <td><?= (int)$row['quantity'] ?></td>
            <td><?= htmlspecialchars($row['note']) ?></td>
            <td><?= htmlspecialchars($row['created_at']) ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</section>
<?php require_once 'includes/footer.php'; ?>
