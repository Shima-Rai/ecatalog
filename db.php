<?php
// Database Credentials
$host = '127.0.0.1';
$user = 'root';
$pass = '';
$db   = 'e_catalog';

// Connect to MySQL Server
$conn = new mysqli($host, $user, $pass);

if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

// Create database if not exists
$conn->query("CREATE DATABASE IF NOT EXISTS $db 
    CHARACTER SET utf8mb4 
    COLLATE utf8mb4_general_ci");

$conn->select_db($db);

// Create products table (NO created_at)
$conn->query("
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    price DECIMAL(10,2) NOT NULL
) ENGINE=InnoDB;
");

// Create orders table (NO order_date)
$conn->query("
CREATE TABLE IF NOT EXISTS orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB;
");

// Sanitize helper
function safe($conn, $value) {
    return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
}
?>
