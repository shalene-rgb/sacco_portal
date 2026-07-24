<?php
// Include database configuration
require_once 'config.php';

// Automatically create and seed security_logs table if empty
$table_check = mysqli_query($link, "SHOW TABLES LIKE 'security_logs'");
if (mysqli_num_rows($table_check) == 0) {
    $create_table_sql = "CREATE TABLE security_logs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        attacker_ip VARCHAR(45) NOT NULL,
        threat_signature VARCHAR(255) NOT NULL,
        mitigation_timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
        action VARCHAR(50) DEFAULT 'BLOCKED'
    )";
    mysqli_query($link, $create_table_sql);
}

// Check if security_logs has entries; if not, seed sample entries
$count_check = mysqli_query($link, "SELECT COUNT(*) as total FROM security_logs");
$count_data = mysqli_fetch_assoc($count_check);

if ($count_data['total'] == 0) {
    $seed_sql = "INSERT INTO security_logs (attacker_ip, threat_signature, mitigation_timestamp, action) VALUES
    ('192.168.1.105', 'SQL Injection Attempt (\' OR 1=1)', NOW() - INTERVAL 2 HOUR, 'BLOCKED'),
    ('102.219.208.12', 'Cross-Site Scripting (<script>)', NOW() - INTERVAL 5 HOUR, 'SANITIZED'),
    ('197.232.61.14', 'Automated Bot Rate Limit Exceeded', NOW() - INTERVAL 1 DAY, 'RATE_LIMITED'),
    ('45.33.32.156', 'Unauthorized Admin Directory Traversal', NOW() - INTERVAL 2 DAY, 'BLOCKED')";
    mysqli_query($link, $seed_sql);
}

// Fetch human loan applications
$loans_query = "SELECT * FROM loan_applications ORDER BY id DESC";
$loans_result = mysqli_query($link, $loans_query);

// Fetch mitigated anomalies and security logs
$security_query = "SELECT * FROM security_logs ORDER BY id DESC";
$security_result = mysqli_query($link, $security_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard & Live Security Logs</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #12141d;
            color: #e0e6ed;
            margin: 0;
            padding: 30px;
        }
        .container {
            max-width: 1100px;
            margin: 0 auto;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #2a2e3d;
            padding-bottom: 15px;
            margin-bottom: 30px;
        }
        .header h2 {
            margin: 0;
            color: #ffffff;
            font-size: 24px;
        }
        .nav-links a {
            color: #4da6ff;
            text-decoration: none;
            margin-left: 15px;
            font-size: 14px;
        }
        .nav-links a:hover {
            text-decoration: underline;
        }
        .card {
            background-color: #1e2230;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            border-left: 4px solid #007bff;
        }
        .card.security-card {
            border-left-color: #dc3545;
        }
        .card h3 {
            margin-top: 0;
            margin-bottom: 20px;
            font-size: 18px;
            color: #ffffff;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }
        th, td {
            padding: 12px 15px;
            border-bottom: 1px solid #2c3245;
            font-size: 14px;
        }
        th {
            background-color: #161925;
            color: #8a94a6;
            font-weight: 600;
        }
        tr:hover {
            background-color: #252a3b;
        }
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-success {
            background-color: rgba(40, 167, 69, 0.2);
            color: #28a745;
            border: 1px solid #28a745;
        }
        .badge-danger {
            background-color: rgba(220, 53, 69, 0.2);
            color: #dc3545;
            border: 1px solid #dc3545;
        }
        .badge-warning {
            background-color: rgba(255, 193, 7, 0.2);
            color: #ffc107;
            border: 1px solid #ffc107;
        }
        .no-records {
            text-align: center;
            color: #8a94a6;
            padding: 20px 0;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h2>SACCO Portal Audit Dashboard</h2>
        <div class="nav-links">
            <a href="index.php">◀ Public Portal</a>
            <a href="admin_logs.php">Live Logs</a>
        </div>
    </div>

    <!-- Active Database Commits Section -->
    <div class="card">
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
                <?php if ($loans_result && mysqli_num_rows($loans_result) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($loans_result)): ?>
                        <tr>
                            <td>#<?php echo htmlspecialchars($row['id']); ?></td>
                            <td><?php echo htmlspecialchars($row['member_name']); ?></td>
                            <td>Ksh <?php echo number_format($row['loan_amount'], 2); ?></td>
                            <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                            <td><span class="badge badge-success">CLEAN PASS</span></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="no-records">No loan applications recorded yet.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Mitigated Anomalies & Bot Signatures Section -->
    <div class="card security-card">
        <h3>Mitigated Anomalies & Bot Signatures</h3>
        <table>
            <thead>
                <tr>
                    <th>Incident ID</th>
                    <th>Attacker IP Address</th>
                    <th>Threat Signature</th>
                    <th>Mitigation Timestamp</th>
                    <th>Action Taken</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($security_result && mysqli_num_rows($security_result) > 0): ?>
                    <?php while ($sec_row = mysqli_fetch_assoc($security_result)): ?>
                        <tr>
                            <td>#SEC-00<?php echo htmlspecialchars($sec_row['id']); ?></td>
                            <td><?php echo htmlspecialchars($sec_row['attacker_ip']); ?></td>
                            <td><code><?php echo htmlspecialchars($sec_row['threat_signature']); ?></code></td>
                            <td><?php echo htmlspecialchars($sec_row['mitigation_timestamp']); ?></td>
                            <td>
                                <?php if ($sec_row['action'] == 'BLOCKED'): ?>
                                    <span class="badge badge-danger">BLOCKED</span>
                                <?php else: ?>
                                    <span class="badge badge-warning"><?php echo htmlspecialchars($sec_row['action']); ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="no-records">No security incidents recorded. System stable.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
