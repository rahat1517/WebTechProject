<?php
require_once 'includes/auth_check.php';
if ($_SESSION['user_role'] !== 'admin') {
    die("Access denied.");
}
$pageTitle = "Suppliers";
$message = "";
$isError = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_supplier'])) {
        $name    = trim($_POST['name']    ?? '');
        $phone   = trim($_POST['phone']   ?? '');
        $email   = trim($_POST['email']   ?? '');
        $address = trim($_POST['address'] ?? '');

        //  Validation
        if ($name === '' || $phone === '') {
            $message = "Name and phone are required.";
            $isError = true;
        } elseif (strlen($name) > 200) {
            $message = "Supplier name is too long.";
            $isError = true;
        } elseif (strlen($phone) > 20) {
            $message = "Phone number is too long.";
            $isError = true;
        } elseif ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message = "Invalid email format.";
            $isError = true;
        } elseif (strlen($address) > 500) {
            $message = "Address is too long.";
            $isError = true;
        } else {
            //  Prepared statement
            $stmt = $pdo->prepare("INSERT INTO suppliers (name, phone, email, address) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $phone, $email ?: null, $address ?: null]);
            $message = "Supplier added successfully.";
        }
    }
}

$suppliers = $pdo->query("SELECT * FROM suppliers ORDER BY id DESC")->fetchAll();

require_once 'includes/header.php';
?>
<h1>Suppliers</h1>
<p class="<?= $isError ? 'error' : 'info' ?>"><?= htmlspecialchars($message) ?></p>

<section class="panel">
    <h2>Add Supplier</h2>
    <form method="POST" class="grid-form">
        <input type="hidden" name="add_supplier" value="1">
        <input type="text"  name="name"    placeholder="Supplier Name" required maxlength="200">
        <input type="text"  name="phone"   placeholder="Phone"         required maxlength="20">
        <input type="email" name="email"   placeholder="Email"                  maxlength="254">
        <input type="text"  name="address" placeholder="Address"                maxlength="500">
        <button type="submit">Add Supplier</button>
    </form>
</section>

<section class="panel">
    <h2>Supplier List</h2>
    <table>
        <tr>
            <th>ID</th><th>Name</th><th>Phone</th><th>Email</th><th>Address</th>
        </tr>
        <?php foreach ($suppliers as $supplier): ?>
        <tr>
            <td><?= (int)$supplier['id'] ?></td>
            <td><?= htmlspecialchars($supplier['name']) ?></td>
            <td><?= htmlspecialchars($supplier['phone']) ?></td>
            <td><?= htmlspecialchars($supplier['email'] ?? '') ?></td>
            <td><?= htmlspecialchars($supplier['address'] ?? '') ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</section>
<?php require_once 'includes/footer.php'; ?>
