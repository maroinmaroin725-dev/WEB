<?php
Auth::requireAdmin();

$stats = getAdminStats();
$security = AdminController::getSecuritySettings();
?>

<div class="card" style="margin-top: 20px;">
    <div class="card-header">
        <h2 class="card-title">🛡️ Admin Dashboard - Full Control</h2>
    </div>
    <p>Complete control panel for site administration and security.</p>
</div>

<!-- STATS SECTION -->
<div class="grid grid-4" style="margin-top: 30px;">
    <div class="card">
        <div style="text-align: center;">
            <h3 style="font-size: 32px; color: var(--primary);">👥</h3>
            <h3><?= $stats['totalUsers'] ?></h3>
            <p class="card-subtitle">Total Users</p>
        </div>
    </div>

    <div class="card">
        <div style="text-align: center;">
            <h3 style="font-size: 32px; color: var(--success);">✅</h3>
            <h3><?= $stats['activeUsers'] ?></h3>
            <p class="card-subtitle">Active Users</p>
        </div>
    </div>

    <div class="card">
        <div style="text-align: center;">
            <h3 style="font-size: 32px; color: #f59e0b;">⚙️</h3>
            <h3><?= $stats['totalConfigs'] ?></h3>
            <p class="card-subtitle">Total Configs</p>
        </div>
    </div>

    <div class="card">
        <div style="text-align: center;">
            <h3 style="font-size: 32px; color: var(--danger);">🚨</h3>
            <h3><?= count($security['security_events']) ?></h3>
            <p class="card-subtitle">Security Events</p>
        </div>
    </div>
</div>

<!-- ADMIN MENU -->
<div class="grid grid-4" style="margin-top: 30px;">
    <div class="card" style="text-align: center; cursor: pointer;" onclick="location.href='?page=admin_users'">
        <h3 style="font-size: 36px; margin-bottom: 10px;">👥</h3>
        <h4>User Management</h4>
        <p class="card-subtitle" style="margin-top: 10px;">Ban, Promote, Edit</p>
    </div>

    <div class="card" style="text-align: center; cursor: pointer;" onclick="location.href='?page=admin_settings'">
        <h3 style="font-size: 36px; margin-bottom: 10px;">⚙️</h3>
        <h4>Site Settings</h4>
        <p class="card-subtitle" style="margin-top: 10px;">Configure Site</p>
    </div>

    <div class="card" style="text-align: center; cursor: pointer;" onclick="location.href='?page=admin_security'">
        <h3 style="font-size: 36px; margin-bottom: 10px;">🔒</h3>
        <h4>Security Center</h4>
        <p class="card-subtitle" style="margin-top: 10px;">IP Blocking, Logs</p>
    </div>

    <div class="card" style="text-align: center; cursor: pointer;" onclick="location.href='?page=admin_logs'">
        <h3 style="font-size: 36px; margin-bottom: 10px;">📊</h3>
        <h4>Logs & Reports</h4>
        <p class="card-subtitle" style="margin-top: 10px;">Activity Logs</p>
    </div>
</div>

<!-- RECENT SECURITY EVENTS -->
<div class="card" style="margin-top: 30px;">
    <div class="card-header">
        <h3 class="card-title">🚨 Recent Security Events</h3>
    </div>
    
    <?php if(!empty($security['security_events'])): ?>
        <table class="table" style="font-size: 12px;">
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Details</th>
                    <th>IP</th>
                    <th>Timestamp</th>
                    <th>Severity</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach(array_slice($security['security_events'], -10) as $event): ?>
                    <tr>
                        <td><span class="badge badge-danger"><?= ucfirst(str_replace('_', ' ', $event['type'])) ?></span></td>
                        <td><?= truncate($event['details'], 40) ?></td>
                        <td><?= $event['ip'] ?></td>
                        <td><?= formatDateTime($event['timestamp']) ?></td>
                        <td><span class="badge badge-danger"><?= ucfirst($event['severity']) ?></span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No security events recorded.</p>
    <?php endif; ?>
</div>

<!-- IP BLACKLIST -->
<div class="card" style="margin-top: 30px;">
    <div class="card-header">
        <h3 class="card-title">🚫 IP Blacklist</h3>
    </div>
    
    <?php if(!empty($security['ip_blacklist'])): ?>
        <table class="table" style="font-size: 12px;">
            <thead>
                <tr>
                    <th>IP Address</th>
                    <th>Reason</th>
                    <th>Blocked At</th>
                    <th>Until</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($security['ip_blacklist'] as $entry): ?>
                    <tr>
                        <td><?= $entry['ip'] ?></td>
                        <td><?= $entry['reason'] ?></td>
                        <td><?= formatDateTime($entry['blocked_at']) ?></td>
                        <td><?= formatDateTime($entry['blocked_until']) ?></td>
                        <td>
                            <button class="btn btn-sm btn-success" onclick="unblockIP('<?= $entry['ip'] ?>')">
                                ✅ Unblock
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No blocked IPs.</p>
    <?php endif; ?>
</div>

<script>
function unblockIP(ip) {
    if(confirm('Unblock this IP?')) {
        fetch('/api/admin_settings.php?action=unblock_ip', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'ip=' + ip
        })
        .then(r => r.json())
        .then(d => {
            if(d.success) {
                alert('IP unblocked!');
                location.reload();
            }
        });
    }
}
</script>