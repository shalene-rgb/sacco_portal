<?php
// Database configuration credentials
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', ''); // XAMPP default password is blank
define('DB_NAME', 'sacco_db');

// Attempt to connect to the MySQL database
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check the connection
if($link === false){
    die("ERROR: Could not connect to the database. " . mysqli_connect_error());
}
?>