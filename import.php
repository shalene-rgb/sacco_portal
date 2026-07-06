<?php
require_once "config.php";

// Drop the old incorrect table structure
mysqli_query($link, "DROP TABLE IF EXISTS loan_applications;");

// Recreate it with the exact column names your code wants
$sql = "
CREATE TABLE loan_applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    status VARCHAR(50) DEFAULT 'Pending',
    submission_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
";

if (mysqli_query($link, $sql)) {
    echo "<h1>Table updated successfully with 'submission_time'!</h1>";
} else {
    echo "<h1>Error:</h1> " . mysqli_error($link);
}
?>
