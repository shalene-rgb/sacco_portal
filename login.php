<?php
session_start();
require_once "config.php";

$error_msg = "";

if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    // In a production system, we match against hashed DB records. 
    // Hardcoded credentials for structural verification purposes:
    if ($username === "admin" && $password === "SaccoAdmin@2026") {
        $_SESSION['admin_authenticated'] = true;
        $_SESSION['LAST_ACTIVITY'] = time();
        header("Location: admin_logs.php");
        exit();
    } else {
        $error_msg = "Access Denied: Invalid cryptographic token credentials.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sacco Secure Gateway Login</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #0f172a; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-card { background: #1e293b; padding: 40px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.3); width: 100%; max-width: 400px; }
        h2 { color: #38bdf8; text-align: center; margin-top: 0; margin-bottom: 25px; }
        label { display: block; margin-bottom: 8px; color: #cbd5e1; font-weight: bold; }
        input { width: 100%; padding: 10px; margin-bottom: 20px; border: 1px solid #334155; background: #0f172a; color: white; border-radius: 6px; box-sizing: border-box; }
        button { background: #2563eb; color: white; border: none; padding: 12px; width: 100%; border-radius: 6px; font-weight: bold; cursor: pointer; font-size: 16px; }
        button:hover { background: #1d4ed8; }
        .error { color: #ef4444; background: rgba(239, 68, 68, 0.1); padding: 10px; border-radius: 6px; border: 1px solid #ef4444; margin-bottom: 20px; font-size: 14px; text-align: center; }
    </style>
</head>
<body>
    <div class="login-card">
        <h2>Security Gateway</h2>
        <?php if (!empty($error_msg)): ?>
            <div class="error"><?php echo htmlspecialchars($error_msg); ?></div>
        <?php endif; ?>
        <form action="login.php" method="POST">
            <label>Username Account:</label>
            <input type="text" name="username" required autocomplete="off">
            
            <label>Access Password Key:</label>
            <input type="password" name="password" required>
            
            <button type="submit" name="login">Authorize Session</button>
        </form>
    </div>
</body>
</html>