<?php
// Read credentials from Render Environment Variables (or fallback to defaults)
define('DB_SERVER', getenv('DB_SERVER') ?: 'mysql-22719b9d-wagurashalene-8fb2.k.aivencloud.com');
define('DB_USERNAME', getenv('DB_USERNAME') ?: 'avnadmin');
define('DB_PASSWORD', getenv('DB_PASSWORD') ?: 'AVNS_3rGSzYmpfheAKGHJVKQ');
define('DB_NAME', getenv('DB_NAME') ?: 'defaultdb');
define('DB_PORT', getenv('DB_PORT') ? (int)getenv('DB_PORT') : 11340);

// Initialize MySQLi
$link = mysqli_init();

if (!$link) {
    die("ERROR: mysqli_init failed");
}

// Enable SSL encryption required by Aiven Cloud
mysqli_ssl_set($link, NULL, NULL, NULL, NULL, NULL);

// Connect using SSL
$connected = mysqli_real_connect(
    $link,
    DB_SERVER,
    DB_USERNAME,
    DB_PASSWORD,
    DB_NAME,
    DB_PORT,
    NULL,
    MYSQLI_CLIENT_SSL
);

if (!$connected) {
    die("ERROR: Could not connect to database. " . mysqli_connect_error());
}
?>
