<?php
require_once 'includes/auth_check.php';
if ($_SESSION['user_role'] !== 'admin') {
    die("Access denied.");
}
$pageTitle = "Stock In";
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Cast to int — prevents injection
    $product_id = (int)($_POST['product_id'] ?? 0);
    $quantity   = (int)($_POST['quantity']   ?? 0);
    $note       = trim($_POST['note']        ?? '');

    // Server-side validation
    if ($product_id <= 0) {
        $message = "Please select a valid product.";
    } elseif ($quantity <= 0) {
        $message = "Quantity must be greater than 0.";
    } elseif ($quantity > 100000) {
        $message = "Quantity value is too large.";
    } elseif (strlen($note) > 500) {
        $message = "Note is too long.";
    } else {
        //  Verify product actually exists
        $check = $pdo->prepare("SELECT id FROM products WHERE id = ?");
        $check->execute([$product_id]);
        if (!$check->fetch()) {
            $message = "Product not found.";
        } else {
            $stmt = $pdo->prepare("UPDATE products SET quantity = quantity + ? WHERE id = ?");
            $stmt->execute([$quantity, $product_id]);

            $stmt = $pdo->prepare("INSERT INTO stock_movements (product_id, movement_type, quantity, note) VALUES (?, 'IN', ?, ?)");
            $stmt->execute([$product_id, $quantity, $note]);

            $message = "Stock added successfully.";
        }
    }
}

$products = $pdo->query("SELECT * FROM products ORDER BY name ASC")->fetchAll();
$history  = $pdo->query("SELECT sm.*, p.name AS product_name FROM stock_movements sm JOIN products p ON sm.product_id = p.id WHERE sm.movement_type = 'IN' ORDER BY sm.id DESC")->fetchAll();

require_once 'includes/header.php';
?>
<h1>Stock In</h1>
<p class="info"><?= htmlspecialchars($message) ?></p>

<section class="panel">
    <h2>Add Incoming Stock</h2>
    <form method="POST" class="grid-form">
        <select name="product_id" required>
            <option value="">Select Product</option>
            <?php foreach ($products as $product): ?>
                <option value="<?= (int)$product['id'] ?>"><?= htmlspecialchars($product['name']) ?></option>
            <?php endforeach; ?>
        </select>
        <input type="number" name="quantity" placeholder="Quantity" min="1" max="100000" required>
        <input type="text"   name="note"     placeholder="Note / Source" maxlength="500">
        <button type="submit">Save</button>
    </form>
</section>

<section class="panel">
    <h2>Stock In History</h2>
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
