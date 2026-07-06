<?php
// ==========================================
// PHASE 3 & 5: INTEGRATED DEFENSE & DATABASE SYSTEM
// ==========================================

// 1. Start a server session to track user request behavior
session_start();

// 2. Include the database configuration bridge
require_once "config.php";

$time_window = 5; // Time window tracking interval in seconds
$limit = 3;       // Maximum allowed requests within the window
$current_time = time();

// Initialize the request tracking session array if it doesn't exist
if (!isset($_SESSION['request_timestamps'])) {
    $_SESSION['request_timestamps'] = [];
}

// Clean up old timestamps that fall outside the active 5-second window
$_SESSION['request_timestamps'] = array_filter($_SESSION['request_timestamps'], function($timestamp) use ($current_time, $time_window) {
    return ($current_time - $timestamp) < $time_window;
});

// Log the timestamp of the current incoming request
$_SESSION['request_timestamps'][] = $current_time;

// Check if request velocity exceeds the established mitigation threshold
if (count($_SESSION['request_timestamps']) > $limit) {
    // If this is the exact request that triggers the block, log it into the database
    if (!isset($_SESSION['is_flagged'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
        mysqli_query($link, "INSERT INTO blocked_attacks (ip_address) VALUES ('$ip')");
    }
    $_SESSION['is_flagged'] = true;
}

// Handle Math Challenge validation logic if the session has been flagged
$challenge_error = false;
if (isset($_POST['verify_challenge'])) {
    $user_answer = intval($_POST['challenge_answer']);
    // Defensive check: Verify if the user solved the bot-trap math equation (5 + 4 = 9)
    if ($user_answer === 9) {
        unset($_SESSION['is_flagged']);               // Remove the suspicious session flag
        $_SESSION['request_timestamps'] = [];         // Reset the velocity log counter
    } else {
        $challenge_error = true;
    }
}

// Handle the Data Insertion Logic for legitimate user submissions
$database_success = false;
$database_error_msg = "";

if (!isset($_SESSION['is_flagged']) && isset($_POST['apply'])) {
    // 1. Trim trailing whitespace characters from input strings
    $name = trim($_POST['member_name']);
    $amount = intval($_POST['amount']); // Enforce absolute integer conversion
    
    // 2. Advanced Security Boundary Checking (Regex + Numeric Constraints)
    if (!preg_match("/^[a-zA-Z\s]{3,50}$/", $name)) {
        // Blocks special characters, script tags, numbers, or invalid length structures
        $database_error_msg = "Security Exception: Member Name contains illegal characters or invalid length syntax.";
    } elseif ($amount < 1000 || $amount > 500000) {
        $database_error_msg = "Validation Error: Loan limits must be between Ksh 1,000 and Ksh 500,000.";
    } else {
        // Sanitize string parameters explicitly right before SQL statement commitment
        $safe_name = mysqli_real_escape_string($link, $name);
        
        // Prepare the SQL INSERT query targeting your loan_applications table
        $sql = "INSERT INTO loan_applications (member_name, loan_amount) VALUES ('$safe_name', '$amount')";
        
        if (mysqli_query($link, $sql)) {
            $database_success = true;
        } else {
            $database_error_msg = "Database Error: " . mysqli_error($link);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Naivasha Sacco Portal</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; margin: 40px; background-color: #f4f4f9; }
        .container { max-width: 500px; background: white; padding: 30px; border-radius: 8px; box-shadow: 0px 4px 15px rgba(0,0,0,0.08); margin: 0 auto; }
        h2 { color: #1e3a8a; margin-top: 0; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px; margin-bottom: 20px; }
        .challenge-box { background-color: #fff3cd; border: 1px solid #ffeeba; padding: 20px; border-radius: 6px; margin: 20px 0; }
        .alert-success { background-color: #d1e7dd; border: 1px solid #badbcc; color: #0f5132; padding: 15px; border-radius: 6px; margin-top: 20px; }
        .alert-danger { background-color: #f8d7da; border: 1px solid #f5c2c7; color: #842029; padding: 15px; border-radius: 6px; margin-top: 20px; }
        label { display: block; margin-bottom: 5px; color: #475569; font-weight: 600; }
        input { width: 100%; padding: 10px; margin-bottom: 20px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box; font-size: 14px; }
        input:focus { border-color: #3b82f6; outline: none; }
        button { background-color: #2563eb; color: white; padding: 12px 15px; border: none; border-radius: 6px; cursor: pointer; width: 100%; font-size: 16px; font-weight: 600; transition: background 0.2s; }
        button.verify-btn { background-color: #dc3545; }
        button:hover { opacity: 0.95; }
        .dashboard-link { display: block; text-align: center; margin-top: 20px; color: #2563eb; text-decoration: none; font-weight: bold; }
        .dashboard-link:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Emergency School-Fee Loan Portal</h2>

        <!-- SESSION HARDENING TIMEOUT ALERT -->
        <?php if (isset($_GET['timeout']) && $_GET['timeout'] == 1): ?>
            <div style="background-color: #f8d7da; border: 1px solid #f5c2c7; color: #842029; padding: 12px; border-radius: 6px; margin-bottom: 20px; font-weight: bold; font-size: 14px;">
                🔒 Security Notice: Administrative session closed due to inactivity timeout.
            </div>
        <?php endif; ?>

        <?php 
        // INTERFACE STATE 1: If session is flagged, output the Security CAPTCHA Challenge
        if (isset($_SESSION['is_flagged']) && $_SESSION['is_flagged'] === true): 
        ?>
            <div class="challenge-box">
                <h3 style="color: #b91c1c; margin-top:0;">Security Challenge Triggered</h3>
                <p>Suspiciously rapid requests detected from your session. Please prove you are human:</p>
                
                <form action="index.php" method="POST">
                    <label>What is 5 + 4?</label>
                    <input type="number" name="challenge_answer" placeholder="Your answer" required autofocus>
                    <button type="submit" name="verify_challenge" class="verify-btn">Verify Identity</button>
                </form>
                <?php if ($challenge_error): ?>
                    <p style="color: #b91c1c; margin-top: 10px; font-weight: bold;">Incorrect answer. Automated scripts are restricted.</p>
                <?php endif; ?>
            </div>

        <?php 
        // INTERFACE STATE 2: Standard Clean State available to human traffic
        else: 
        ?>
            <form action="index.php" method="POST">
                <label>Member Name:</label>
                <input type="text" name="member_name" placeholder="e.g. Member Full Name" required>
                
                <label>Loan Amount (Ksh):</label>
                <input type="number" name="amount" min="1000" max="500000" placeholder="Min 1,000 - Max 500,000" required>
                
                <button type="submit" name="apply">Submit Application</button>
            </form>

            <?php
            // Output confirmation alerts depending on database execution states
            if ($database_success) {
                $safe_name = htmlspecialchars($name);
                $safe_amount = htmlspecialchars($amount);
                echo "<div class='alert-success'><b>Success:</b> Application for " . $safe_name . " of Ksh " . number_format($safe_amount) . " has been securely committed to the database!</div>";
            }
            if (!empty($database_error_msg)) {
                echo "<div class='alert-danger'><b>System Fault:</b> " . htmlspecialchars($database_error_msg) . "</div>";
            }
            ?>
        <?php endif; ?>
        
        <a href="login.php" class="dashboard-link">Go to Admin Dashboard 🔑</a>
    </div>
</body>
</html>