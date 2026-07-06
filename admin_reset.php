<?php
session_start();
// 1. Ensure only authenticated administrators can run this script
require_once "auth_guard.php";
require_once "config.php";

// 2. Clear out old database streams and reset counters
if (isset($_POST['reset_system'])) {
    // Clear human applications table
    mysqli_query($link, "TRUNCATE TABLE loan_applications");
    
    // Clear security bot logs table
    mysqli_query($link, "TRUNCATE TABLE blocked_attacks");
    
    // Redirect back to the report dashboard with a success trigger
    header("Location: admin_report.php?reset=success");
    exit();
} else {
    // Unauthorized access fallback
    header("Location: admin_report.php");
    exit();
}
?>