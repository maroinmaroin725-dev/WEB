<?php
Auth::requireAdmin();

$logs = AdminController::getLogs(100);
$security = AdminController::getSecuritySettings();
$report = AdminController::generateReport();
?>

<div class="card" style="margin-top: 20px;">
    <div class="card-header">
        <h2 class="card-title">📊 Logs & Reports</h2>
    </div>
    <p>View system logs, security events, and generate reports.</p>
</div>

<!-- STATISTICS -->
<div class="grid grid-4" style="margin-top: 30px;">
    <div class="card">
        <div style="text-align: center;">
            <h3 style="font-size: 32px; color: var(--primary);">📝</h3>
            <h3><?= count($logs) ?></h3>
            <p class="card-subtitle">Total Logs</p>
        </div>
    </div>
    
    <div class="card">
        <div style="text-align: center;">
            <h3 style="font-size: 32px; color: var(--danger);">🚨</h3>
            <h3><?= count($security['security_events']) ?></h3>
            <p class="card-subtitle">Security Events</p>
        </div>
    </div>
    
    <div class="card">
        <div style="text-align: center;">
            <h3 style="font-size: 32px; color: #f59e0b;">🚫</h3>
            <h3><?= count($security['ip_blacklist']) ?></h3>
            <p class="card-subtitle">Blocked IPs</p>
        </div>
    </div>
    
    <div class="card">
        <div style="text-align: center;">
            <h3 style="font-size: 32px; color: var(--success);">✅</h3>
            <h3><?= $report['stats']['activeUsers'] ?></h3>
            <p class="card-subtitle">Active Users</p>
        </div>
    </div>
</div>

<!-- RECENT LOGS -->
<div class="card" style="margin-top: 30px;">
    <div class="card-header">
        <h3 class="card-title">📋 Recent Activity Logs</h3>
        <button class="btn btn-sm btn-danger" onclick="clearLogs()">🗑️ Clear Logs</button>
    </div>
    
    <?php if(!empty($logs)): ?>
        <table class="table" style="font-size: 12px;">
            <thead>
                <tr>
                    <th>Action</th>
                    <th>User ID</th>
                    <th>IP Address</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach(array_slice($logs, -50) as $log): ?>
                    <tr>
                        <td><span class="badge badge-primary"><?= $log['action'] ?></span></td>
                        <td><?= $log['user_id'] ?></td>
                        <td><?= $log['ip'] ?></td>
                        <td><?= formatDateTime($log['timestamp']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No logs available.</p>
    <?php endif; ?>
</div>

<!-- SECURITY EVENTS -->
<div class="card" style="margin-top: 30px;">
    <div class="card-header">
        <h3 class="card-title">🚨 Security Events</h3>
    </div>
    
    <?php if(!empty($security['security_events'])): ?>
        <table class="table" style="font-size: 12px;">
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Details</th>
                    <th>IP Address</th>
                    <th>Severity</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach(array_slice($security['security_events'], -30) as $event): ?>
                    <tr>
                        <td>
                            <span class="badge badge-danger">
                                <?= ucfirst(str_replace('_', ' ', $event['type'])) ?>
                            </span>
                        </td>
                        <td><?= truncate($event['details'], 50) ?></td>
                        <td><?= $event['ip'] ?></td>
                        <td>
                            <span class="badge badge-<?= $event['severity'] === 'high' ? 'danger' : 'warning' ?>">
                                <?= ucfirst($event['severity']) ?>
                            </span>
                        </td>
                        <td><?= formatDateTime($event['timestamp']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No security events.</p>
    <?php endif; ?>
</div>

<script>
function clearLogs() {
    if(confirm('Clear all logs? This cannot be undone!')) {
        fetch('/api/admin_logs.php?action=clear', {
            method: 'POST'
        })
        .then(r => r.json())
        .then(d => {
            if(d.success) {
                app.showNotification('Success', 'Logs cleared', 'success');
                setTimeout(() => location.reload(), 1000);
            }
        });
    }
}
</script>