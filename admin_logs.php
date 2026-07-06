<?php
session_start();
// Include the automated session timeout security layer
require_once "auth_guard.php";
require_once "config.php";

// 1. Fetch the latest clean entries recorded in the database (Human Pass)
$human_query = "SELECT * FROM loan_applications ORDER BY submission_time DESC LIMIT 10";
$human_result = mysqli_query($link, $human_query);

// 2. Fetch the latest security incidents recorded in the database (Bot Blocks)
$bot_query = "SELECT * FROM blocked_attacks ORDER BY blocked_at DESC LIMIT 10";
$bot_result = mysqli_query($link, $bot_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sacco Security Dashboard - Logs & Incidents</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; margin: 40px; background-color: #0f172a; color: #cbd5e1; }
        .container { max-width: 950px; margin: 0 auto; background: #1e293b; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.5); }
        h2 { color: #38bdf8; border-bottom: 2px solid #334155; padding-bottom: 10px; margin-top: 0; }
        h3 { color: #f8fafc; margin-top: 30px; border-left: 4px solid #38bdf8; padding-left: 10px; }
        .bot-heading { border-left-color: #ef4444; }
        .nav-links { margin-bottom: 20px; }
        .nav-links a { color: #38bdf8; text-decoration: none; margin-right: 15px; font-weight: bold; }
        .nav-links a:hover { text-decoration: underline; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; background: #0f172a; border-radius: 6px; overflow: hidden; }
        th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #334155; }
        th { background-color: #111827; color: #f8fafc; font-weight: 600; font-size: 14px; }
        tr:hover { background-color: #1e293b; }
        .badge { color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; text-transform: uppercase; }
        .badge-clean { background: #10b981; }
        .badge-blocked { background: #ef4444; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Sacco Forensic Traffic Monitor</h2>
        <div class="nav-links">
            <a href="index.php">◀ Public Portal</a>
            <a href="admin_logs.php" style="color: #f8fafc;">Live Logs</a>
            <a href="admin_report.php">Analytics Report 📊</a>
        </div>

        <!-- TABLE 1: HUMAN LOGS -->
        <h3>Active Database Commits (Human Requests)</h3>
        <table>
            <thead>
                <tr>
                    <th>Log ID</th>
                    <th>Member Name</th>
                    <th>Requested Amount</th>
                    <th>Timestamp</th>
                    <th>Status Flag</th>
                </tr>
            </thead>
            <tbody>
                <?php if(mysqli_num_rows($human_result) > 0): ?>
                    <?php while($row = mysqli_fetch_assoc($human_result)): ?>
                        <tr>
                            <td>#<?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['member_name']); ?></td>
                            <td>Ksh <?php echo number_format($row['loan_amount'], 2); ?></td>
                            <td><?php echo $row['submission_time']; ?></td>
                            <td><span class="badge badge-clean">CLEAN PASS</span></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5" style="text-align:center; color:#64748b;">No human traffic logged yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- TABLE 2: MITIGATED INCIDENTS -->
        <h3 class="bot-heading">Mitigated Anomalies & Bot Signatures</h3>
        <table>
            <thead>
                <tr>
                    <th>Incident ID</th>
                    <th>Attacker IP Address</th>
                    <th>Threat Signature</th>
                    <th>Mitigation Timestamp</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if(mysqli_num_rows($bot_result) > 0): ?>
                    <?php while($bot = mysqli_fetch_assoc($bot_result)): ?>
                        <tr>
                            <td>#<?php echo $bot['id']; ?></td>
                            <td style="color: #f8fafc; font-family: monospace; font-weight: 600;"><?php echo $bot['ip_address']; ?></td>
                            <td style="color: #f59e0b; font-size: 14px;"><?php echo htmlspecialchars($bot['attack_type']); ?></td>
                            <td><?php echo $bot['blocked_at']; ?></td>
                            <td><span class="badge badge-blocked">BLOCKED</span></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5" style="text-align:center; color:#64748b;">No security incidents recorded. System stable.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>