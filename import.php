<?php
require_once "config.php";

// SQL script to create your system tables
$sql = "
CREATE TABLE IF NOT EXISTS loan_applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    status VARCHAR(50) DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS blocked_attacks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(45) NOT NULL,
    attack_type VARCHAR(100) NOT NULL,
    payload TEXT,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);
";

// If you have a specific table structure from your project, paste its CREATE statements here!

if (mysqli_multi_query($link, $sql)) {
    echo "<h1>Success! Tables created in Aiven Cloud successfully.</h1>";
} else {
    echo "<h1>Error creating tables:</h1> " . mysqli_error($link);
}
?>
