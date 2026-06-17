<?php
Auth::requireAdmin();

$security = AdminController::getSecuritySettings();
?>

<div class="card" style="margin-top: 20px;">
    <div class="card-header">
        <h2 class="card-title">🔒 Security Center</h2>
    </div>
    <p>Monitor and control security settings.</p>
</div>

<!-- BLOCK IP SECTION -->
<div class="card" style="margin-top: 30px;">
    <div class="card-header">
        <h3 class="card-title">Block IP Address</h3>
    </div>
    
    <form id="blockIPForm">
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">IP Address</label>
                <input type="text" id="blockIP" class="form-control" placeholder="192.168.1.1" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Reason</label>
                <input type="text" id="blockReason" class="form-control" placeholder="Suspicious activity">
            </div>
        </div>
        
        <button type="submit" class="btn btn-danger">🚫 Block IP</button>
    </form>
</div>

<!-- BLOCKED IPS -->
<div class="card" style="margin-top: 30px;">
    <div class="card-header">
        <h3 class="card-title">🚫 Blocked IP Addresses</h3>
    </div>
    
    <?php if(!empty($security['ip_blacklist'])): ?>
        <table class="table" style="font-size: 12px;">
            <thead>
                <tr>
                    <th>IP</th>
                    <th>Reason</th>
                    <th>Blocked At</th>
                    <th>Expires</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($security['ip_blacklist'] as $ip): ?>
                    <tr>
                        <td><code><?= $ip['ip'] ?></code></td>
                        <td><?= $ip['reason'] ?></td>
                        <td><?= formatDateTime($ip['blocked_at']) ?></td>
                        <td><?= formatDateTime($ip['blocked_until']) ?></td>
                        <td>
                            <button class="btn btn-sm btn-success" onclick="unblockIP('<?= $ip['ip'] ?>')">
                                ✅ Unblock
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No blocked IP addresses.</p>
    <?php endif; ?>
</div>

<!-- BRUTE FORCE ATTEMPTS -->
<div class="card" style="margin-top: 30px;">
    <div class="card-header">
        <h3 class="card-title">⚠️ Brute Force Attempts</h3>
    </div>
    
    <?php if(!empty($security['brute_force_attempts'])): ?>
        <table class="table" style="font-size: 12px;">
            <thead>
                <tr>
                    <th>Identifier</th>
                    <th>Attempts</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($security['brute_force_attempts'] as $id => $attempts): ?>
                    <tr>
                        <td><?= $id ?></td>
                        <td><?= count($attempts) ?> attempts</td>
                        <td>
                            <button class="btn btn-sm btn-danger" onclick="blockIdentifier('<?= $id ?>')">🚫 Block</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No brute force attempts detected.</p>
    <?php endif; ?>
</div>

<script>
document.getElementById('blockIPForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const ip = document.getElementById('blockIP').value;
    const reason = document.getElementById('blockReason').value;
    
    fetch('/api/admin_settings.php?action=block_ip', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'ip=' + ip + '&reason=' + reason
    })
    .then(r => r.json())
    .then(d => {
        if(d.success) {
            app.showNotification('Success', 'IP blocked successfully', 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            app.showNotification('Error', d.error, 'error');
        }
    });
});

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
                app.showNotification('Success', 'IP unblocked', 'success');
                setTimeout(() => location.reload(), 1000);
            }
        });
    }
}
</script>