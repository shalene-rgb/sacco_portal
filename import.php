<?php
require_once "config.php";

// Clean out the old mismatched table variations
mysqli_query($link, "DROP TABLE IF EXISTS loan_applications;");
mysqli_query($link, "DROP TABLE IF EXISTS blocked_attacks;");

// Structure 1: Loan Applications (Sorted by submission_time)
$sql1 = "
CREATE TABLE loan_applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    status VARCHAR(50) DEFAULT 'Pending',
    submission_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
";

// Structure 2: Blocked Attacks Logs (Sorted by blocked_at)
$sql2 = "
CREATE TABLE blocked_attacks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(45) NOT NULL,
    attack_type VARCHAR(100) NOT NULL,
    payload TEXT,
    blocked_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
";

// Execute both table setups
if (mysqli_query($link, $sql1) && mysqli_query($link, $sql2)) {
    echo "<h1>Success! Both database tables aligned and created perfectly.</h1>";
} else {
    echo "<h1>Database Error:</h1> " . mysqli_error($link);
}
?>
