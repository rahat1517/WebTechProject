<?php
require_once 'includes/auth_check.php';

$pageTitle = "Suppliers";
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_supplier'])) {
        $stmt = $pdo->prepare("INSERT INTO suppliers (name, phone, email, address) VALUES (?, ?, ?, ?)");
        $stmt->execute([
            trim($_POST['name']),
            trim($_POST['phone']),
            trim($_POST['email']),
            trim($_POST['address'])
        ]);
        $message = "Supplier added successfully.";
    }
}

$suppliers = $pdo->query("SELECT * FROM suppliers ORDER BY id DESC")->fetchAll();

require_once 'includes/header.php';
?>
<h1>Suppliers</h1>
<p class="info"><?= htmlspecialchars($message) ?></p>

<section class="panel">
    <h2>Add Supplier</h2>
    <form method="POST" class="grid-form">
        <input type="hidden" name="add_supplier" value="1">
        <input type="text" name="name" placeholder="Supplier Name" required>
        <input type="text" name="phone" placeholder="Phone" required>
        <input type="email" name="email" placeholder="Email">
        <input type="text" name="address" placeholder="Address">
        <button type="submit">Add Supplier</button>
    </form>
</section>

<section class="panel">
    <h2>Supplier List</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Phone</th>
            <th>Email</th>
            <th>Address</th>
        </tr>
        <?php foreach ($suppliers as $supplier): ?>
        <tr>
            <td><?= $supplier['id'] ?></td>
            <td><?= htmlspecialchars($supplier['name']) ?></td>
            <td><?= htmlspecialchars($supplier['phone']) ?></td>
            <td><?= htmlspecialchars($supplier['email']) ?></td>
            <td><?= htmlspecialchars($supplier['address']) ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</section>
<?php require_once 'includes/footer.php'; ?>