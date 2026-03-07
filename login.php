<?php
require_once 'config.php';

$error = "";

if (!file_exists(__DIR__ . '/data/inventory.sqlite')) {
    redirect('init_db.php');
}

// Redirect if already logged in
if (isLoggedIn()) {
    redirect('dashboard.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //  Sanitize inputs
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    //  Basic format validation before hitting the DB
    if ($email === '' || $password === '') {
        $error = "Please fill in all fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif (strlen($password) > 200) {
        $error = "Invalid credentials.";
    } else {
        //  Prepared statement — SQL injection impossible
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        //  password_verify — timing-safe comparison, no plaintext storage
        if ($user && password_verify($password, $user['password'])) {
            // Regenerate session ID to prevent session fixation
            session_regenerate_id(true);

            $_SESSION['user_id']   = (int)$user['id'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['user_role'] = $user['role'];
            redirect('dashboard.php');
        } else {
            // Generic error — don't reveal whether email exists
            $error = "Invalid email or password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login -IIT SuperShop</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body class="auth-body">
<div class="auth-card">
    <h1>IIT SuperShop</h1>
    <?php if ($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <form method="POST">
        <input type="email"    name="email"    placeholder="Email"    required maxlength="254" autocomplete="email">
        <input type="password" name="password" placeholder="Password" required maxlength="200" autocomplete="current-password">
        <button type="submit">Login</button>
    </form>
</div>
</body>
</html>
