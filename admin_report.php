<?php
if (session_status() === PHP_SESSION_NONE) { 
    session_start(); 
}
// Include the automated session timeout security layer
require_once "auth_guard.php";
require_once "config.php";

// 1. Query database to count live, allowed human applications
$clean_query = mysqli_query($link, "SELECT COUNT(*) as total FROM loan_applications");
$clean_row = mysqli_fetch_assoc($clean_query);
$clean_count = $clean_row['total'];

// 2. Query database to count live, blocked automated bot attempts
$bot_query = mysqli_query($link, "SELECT COUNT(*) as total FROM blocked_attacks");
$bot_row = mysqli_fetch_assoc($bot_query);
$blocked_count = $bot_row['total'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sacco Security Dashboard - Reports</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; margin: 40px; background-color: #0f172a; color: #cbd5e1; }
        .container { max-width: 900px; margin: 0 auto; background: #1e293b; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.5); }
        h2 { color: #38bdf8; border-bottom: 2px solid #334155; padding-bottom: 10px; margin-top: 0; }
        .nav-links { margin-bottom: 30px; }
        .nav-links a { color: #38bdf8; text-decoration: none; margin-right: 15px; font-weight: bold; }
        .nav-links a:hover { text-decoration: underline; }
        .stats-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: #0f172a; padding: 20px; border-radius: 6px; border-left: 4px solid #10b981; }
        .stat-card.blocked { border-left-color: #ef4444; }
        .stat-num { font-size: 36px; font-weight: bold; margin-top: 5px; color: #f8fafc; }
    </style>
</head>
<body>
    <div class="container">
        <h2>System Mitigation Analytics Report</h2>
        <div class="nav-links">
            <a href="index.php">◀ Public Portal</a>
            <a href="admin_logs.php">Live Logs</a>
            <a href="admin_report.php" style="color: #f8fafc;">Analytics Report 📊</a>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div style="color: #10b981; font-weight: bold;">Allowed Human Applications</div>
                <div class="stat-num"><?php echo $clean_count; ?></div>
            </div>
            <div class="stat-card blocked">
                <div style="color: #ef4444; font-weight: bold;">Blocked Bot Attacks (Mitigated)</div>
                <div class="stat-num"><?php echo $blocked_count; ?></div>
            </div>
        </div>

        <!-- SYSTEM MAINTENANCE SECTION -->
        <?php if (isset($_GET['reset']) && $_GET['reset'] === 'success'): ?>
            <div style="background-color: #d1e7dd; border: 1px solid #badbcc; color: #0f5132; padding: 12px; border-radius: 6px; margin-bottom: 20px; font-weight: bold; font-size: 14px; text-align: center;">
                ♻️ System Environment Reset: All live database tables truncated and auto-increments reset to #1.
            </div>
        <?php endif; ?>

        <div style="background: #0f172a; padding: 20px; border-radius: 6px; border: 1px solid #334155; margin-top: 10px;">
            <h4 style="margin-top: 0; color: #ef4444; text-transform: uppercase; font-size: 14px; letter-spacing: 0.5px;">System Maintenance & Forensics Utility</h4>
            <p style="color: #94a3b8; font-size: 13px; margin-bottom: 15px;">Wipe all human database commitments and security incident signature counters to start a fresh presentation testing phase.</p>
            <form action="admin_reset.php" method="POST" onsubmit="return confirm('Are you absolutely sure you want to flush all forensic database metrics? This cannot be undone.');">
                <button type="submit" name="reset_system" style="background-color: #dc2626; color: white; border: none; padding: 10px 15px; border-radius: 4px; font-weight: bold; cursor: pointer; font-size: 13px;">
                    Flush Application Logs & Reset Tables
                </button>
            </form>
        </div>

        <p style="color: #94a3b8; font-size: 12px; margin-top: 25px;">Metrics dynamically computed from database log event counters.</p>
    </div>
</body>
</html>