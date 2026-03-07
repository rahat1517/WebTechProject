<?php
require_once 'includes/auth_check.php';

$pageTitle = "Orders";
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['add_order'])) {

        $customer = trim($_POST['customer_name']);
        $phone_number = trim($_POST['phone_number']);
        $product_id = (int)$_POST['product_id'];
        $quantity = (int)$_POST['quantity'];

        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch();

        if (!$product) {
            $message = "Product not found!";
        } elseif ($quantity <= 0) {
            $message = "Quantity must be greater than 0!";
        } elseif ($quantity > $product['quantity']) {
            $message = "Not enough stock!";
        } else {

            $price = $product['price'];
            $total = $price * $quantity;

            $pdo->beginTransaction();

            try {
                $stmt = $pdo->prepare("INSERT INTO orders (customer_name, product_id, phone_number, quantity, unit_price, total_price, status)
                VALUES (?, ?, ?, ?, ?, ?, 'Completed')");
                $stmt->execute([$customer, $product_id, $phone_number, $quantity, $price, $total]);

                $stmt = $pdo->prepare("UPDATE products SET quantity = quantity - ? WHERE id = ?");
                $stmt->execute([$quantity, $product_id]);

                $pdo->commit();

                $message = "Order created successfully";
            } catch (Exception $e) {
                $pdo->rollBack();
                $message = "Order failed!";
            }
        }
    }
}

$products = $pdo->query("SELECT * FROM products")->fetchAll();
$orders = $pdo->query("SELECT o.*, p.name AS product_name FROM orders o JOIN products p ON o.product_id = p.id ORDER BY o.id DESC")->fetchAll();

require_once 'includes/header.php';
?>

<h1>Orders</h1>

<p class="info"><?= htmlspecialchars($message) ?></p>

<section class="panel">

<h2>Create Order</h2>

<form method="POST" class="grid-form">

<input type="hidden" name="add_order" value="1">

<input type="text" name="customer_name" placeholder="Customer Name" required>

<input type="text" name="phone_number" placeholder="Phone Number" required>

<select name="product_id" required>
<option value="">Select Product</option>

<?php foreach($products as $p): ?>
<option value="<?= $p['id'] ?>">
<?= htmlspecialchars($p['name']) ?> (Stock: <?= $p['quantity'] ?>)
</option>
<?php endforeach; ?>

</select>

<input type="number" name="quantity" placeholder="Quantity" min="1" required>

<button type="submit">Create Order</button>

</form>

</section>

<section class="panel">

<h2>Order List</h2>

<table>

<tr>
<th>ID</th>
<th>Customer</th>
<th>Mobile Number</th>
<th>Product</th>
<th>Qty</th>
<th>Price</th>
<th>Total</th>
<th>Status</th>
</tr>

<?php foreach($orders as $o): ?>

<tr>
<td><?= $o['id'] ?></td>
<td><?= htmlspecialchars($o['customer_name']) ?></td>
<td><?= htmlspecialchars($o['phone_number']) ?></td>
<td><?= htmlspecialchars($o['product_name']) ?></td>
<td><?= $o['quantity'] ?></td>
<td>$<?= number_format($o['unit_price'], 2) ?></td>
<td>$<?= number_format($o['total_price'], 2) ?></td>
<td><?= htmlspecialchars($o['status']) ?></td>
</tr>

<?php endforeach; ?>

</table>

</section>

<?php require_once 'includes/footer.php'; ?>