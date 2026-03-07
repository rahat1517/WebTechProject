<?php
require_once 'includes/auth_check.php';

$pageTitle = "Sales Products";
$message = "";

// Add payment_method column if it doesn't exist (safe migration)
try {
    $pdo->exec("ALTER TABLE orders ADD COLUMN payment_method TEXT DEFAULT 'COD'");
} catch (Exception $e) {
    // Column already exists — ignore
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['add_order'])) {

        $customer       = trim($_POST['customer_name']);
        $phone_number   = trim($_POST['phone_number']);
        $product_id     = (int)$_POST['product_id'];
        $quantity       = (int)$_POST['quantity'];
        $payment_method = trim($_POST['payment_method']);

        // Validate payment method
        $allowed_payments = ['bKash', 'Nagad', 'Rocket', 'Card', 'COD', 'Other'];
        if (!in_array($payment_method, $allowed_payments)) {
            $payment_method = 'COD';
        }

        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch();

        if (!$product) {
            $message = "Product not found!";
        } elseif ($quantity <= 0) {
            $message = "Quantity must be greater than 0!";
        } elseif ($quantity > $product['quantity']) {
            $message = "Not enough stock! Available: " . $product['quantity'];
        } else {

            $price = $product['price'];
            $total = $price * $quantity;

            $pdo->beginTransaction();

            try {
                $stmt = $pdo->prepare("INSERT INTO orders (customer_name, product_id, phone_number, quantity, unit_price, total_price, status, payment_method)
                VALUES (?, ?, ?, ?, ?, ?, 'Completed', ?)");
                $stmt->execute([$customer, $product_id, $phone_number, $quantity, $price, $total, $payment_method]);

                $stmt = $pdo->prepare("UPDATE products SET quantity = quantity - ? WHERE id = ?");
                $stmt->execute([$quantity, $product_id]);

                $pdo->commit();

                $message = "Order created successfully via " . htmlspecialchars($payment_method) . "!";
            } catch (Exception $e) {
                $pdo->rollBack();
                $message = "Order failed! Please try again.";
            }
        }
    }
}

$products = $pdo->query("SELECT * FROM products ORDER BY name ASC")->fetchAll();

// Try fetching with payment_method column
try {
    $orders = $pdo->query("SELECT o.*, p.name AS product_name FROM orders o JOIN products p ON o.product_id = p.id ORDER BY o.id DESC")->fetchAll();
} catch (Exception $e) {
    $orders = [];
}

// Helper: payment badge CSS class
function paymentBadgeClass($method) {
    $map = [
        'bKash'  => 'badge-bkash',
        'Nagad'  => 'badge-nagad',
        'Rocket' => 'badge-rocket',
        'Card'   => 'badge-card',
        'COD'    => 'badge-cod',
        'Other'  => 'badge-other',
    ];
    return $map[$method] ?? 'badge-other';
}

// Helper: payment emoji icon
function paymentIcon($method) {
    $icons = [
        'bKash'  => '📱',
        'Nagad'  => '🟠',
        'Rocket' => '🚀',
        'Card'   => '💳',
        'COD'    => '💵',
        'Other'  => '💰',
    ];
    return $icons[$method] ?? '💰';
}

require_once 'includes/header.php';
?>

<h1>Sales Products</h1>

<p class="info <?= strpos($message, 'failed') !== false ? 'error' : '' ?>"><?= htmlspecialchars($message) ?></p>

<section class="panel">
    <h2>Create New Order</h2>

    <form method="POST" class="grid-form">
        <input type="hidden" name="add_order" value="1">

        <input type="text" name="customer_name" placeholder="Customer Name" required>

        <input type="text" name="phone_number" placeholder="Phone Number" required>

        <select name="product_id" required>
            <option value="">Select Product</option>
            <?php foreach ($products as $p): ?>
                <option value="<?= $p['id'] ?>">
                    <?= htmlspecialchars($p['name']) ?> (Stock: <?= $p['quantity'] ?>)
                </option>
            <?php endforeach; ?>
        </select>

        <input type="number" name="quantity" placeholder="Quantity" min="1" required>

        <select name="payment_method" required>
            <option value="">Payment Method</option>
            <option value="bKash">📱 bKash</option>
            <option value="Nagad">🟠 Nagad</option>
            <option value="Rocket">🚀 Rocket</option>
            <option value="Card">💳 Card</option>
            <option value="COD">💵 Cash on Delivery</option>
            <option value="Other">💰 Other</option>
        </select>

        <button type="submit">Create Order</button>
    </form>
</section>

<section class="panel">
    <h2>Order List</h2>

    <input type="text" id="tableSearch" placeholder="Search orders...">

    <table id="dataTable">
        <tr>
            <th>ID</th>
            <th>Customer</th>
            <th>Mobile</th>
            <th>Product</th>
            <th>Qty</th>
            <th>Unit Price</th>
            <th>Total</th>
            <th>Payment</th>
            <th>Status</th>
        </tr>

        <?php foreach ($orders as $o): 
            $pm = $o['payment_method'] ?? 'COD';
        ?>
        <tr>
            <td><?= $o['id'] ?></td>
            <td><?= htmlspecialchars($o['customer_name']) ?></td>
            <td><?= htmlspecialchars($o['phone_number']) ?></td>
            <td><?= htmlspecialchars($o['product_name']) ?></td>
            <td><?= $o['quantity'] ?></td>
            <td>৳<?= number_format($o['unit_price'], 2) ?></td>
            <td>৳<?= number_format($o['total_price'], 2) ?></td>
            <td>
                <span class="badge-payment <?= paymentBadgeClass($pm) ?>">
                    <?= paymentIcon($pm) ?> <?= htmlspecialchars($pm) ?>
                </span>
            </td>
            <td>
                <span class="badge-status badge-completed">
                    <?= htmlspecialchars($o['status']) ?>
                </span>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</section>

<?php require_once 'includes/footer.php'; ?>
