<?php
require_once "config.php";

// Drop the old version to apply the column fix
mysqli_query($link, "DROP TABLE IF EXISTS loan_applications;");

// Recreate with 'member_name' instead of 'username'
$sql = "
CREATE TABLE loan_applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    member_name VARCHAR(100) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    status VARCHAR(50) DEFAULT 'Pending',
    submission_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
";

if (mysqli_query($link, $sql)) {
    echo "<h1>Success! loan_applications table updated with 'member_name'.</h1>";
} else {
    echo "<h1>Database Error:</h1> " . mysqli_error($link);
}
?>
