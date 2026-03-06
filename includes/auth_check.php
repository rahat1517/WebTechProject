<?php
require_once __DIR__ . '/../config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}
?>