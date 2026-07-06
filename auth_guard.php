<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Structural Gatekeeper Check: Is the visitor authenticated?
if (!isset($_SESSION['admin_authenticated']) || $_SESSION['admin_authenticated'] !== true) {
    // If not authenticated, instantly throw them out to the login screen
    header("Location: login.php");
    exit();
}

// 2. Idle Session Handling
$timeout_duration = 600; // 10 minutes

if (isset($_SESSION['LAST_ACTIVITY'])) {
    $elapsed_time = time() - $_SESSION['LAST_ACTIVITY'];
    
    if ($elapsed_time > $timeout_duration) {
        session_unset();
        session_destroy();
        header("Location: index.php?timeout=1");
        exit();
    }
}
$_SESSION['LAST_ACTIVITY'] = time();
?>