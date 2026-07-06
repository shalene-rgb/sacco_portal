<?php
define('DB_SERVER', 'mysql-22719b9d-wagurashalene-8fb2.k.aivencloud.com');
define('DB_USERNAME', 'avnadmin');
define('DB_PASSWORD', 'AVNS_3rGSzYmpfheAKGHJVKQ');
define('DB_NAME', 'defaultdb');
define('DB_PORT', '11340');

// Connect to the Aiven cloud MySQL database instance using the explicit port
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME, DB_PORT);

if($link === false){
    die("ERROR: Could not connect to the cloud database. " . mysqli_connect_error());
}
?>
