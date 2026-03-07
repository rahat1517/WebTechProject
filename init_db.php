<?php
require_once 'config.php';

$pdo->exec("
CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    full_name TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL,
    role TEXT NOT NULL DEFAULT 'salesman'
);

CREATE TABLE IF NOT EXISTS suppliers (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    phone TEXT NOT NULL,
    email TEXT,
    address TEXT
);

CREATE TABLE IF NOT EXISTS products (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    category TEXT NOT NULL,
    quantity INTEGER NOT NULL DEFAULT 0,
    price REAL NOT NULL DEFAULT 0,
    reorder_level INTEGER NOT NULL DEFAULT 5,
    supplier_id INTEGER NULL,
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS stock_movements (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    product_id INTEGER NOT NULL,
    movement_type TEXT NOT NULL CHECK (movement_type IN ('IN', 'OUT')),
    quantity INTEGER NOT NULL,
    note TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS orders (

id INTEGER PRIMARY KEY AUTOINCREMENT,
customer_name TEXT,
product_id INTEGER,
phone_number TEXT,
quantity INTEGER,
unit_price REAL,
total_price REAL,
status TEXT,
created_at DATETIME DEFAULT CURRENT_TIMESTAMP

);
");

$userCount = (int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
if ($userCount === 0) {
    $adminPassword = password_hash('123456', PASSWORD_DEFAULT);
    $salesmanPassword = password_hash('123456', PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->execute(['Admin User', 'admin@example.com', $adminPassword, 'admin']);
    $stmt->execute(['Salesman User', 'salesman@example.com', $salesmanPassword, 'salesman']);
}

$supplierCount = (int)$pdo->query("SELECT COUNT(*) FROM suppliers")->fetchColumn();
if ($supplierCount === 0) {
    $pdo->exec("
    INSERT INTO suppliers (name, phone, email, address) VALUES
    ('ABC Traders', '01700000001', 'abc@example.com', 'Dhaka'),
    ('Smart Supply', '01700000002', 'smart@example.com', 'Chattogram');
    ");
}

$productCount = (int)$pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
if ($productCount === 0) {
    $pdo->exec("
    INSERT INTO products (name, category, quantity, price, reorder_level, supplier_id) VALUES
    ('Keyboard', 'Electronics', 25, 15.00, 5, 1),
    ('Mouse', 'Electronics', 18, 10.00, 5, 1),
    ('Notebook', 'Stationery', 50, 2.50, 10, 2),
    ('Pen Box', 'Stationery', 8, 4.00, 10, 2);
    ");
}

echo "SQLite database initialized successfully.<br>";
echo "Default login: admin@example.com / 123456<br>";
echo "<a href='login.php'>Go to Login</a>";
?>