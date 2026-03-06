<?php
require_once 'config.php';

if (!file_exists(__DIR__ . '/data/inventory.sqlite')) {
    redirect('init_db.php');
}

if (isLoggedIn()) {
    redirect('dashboard.php');
} else {
    redirect('login.php');
}
?>