<?php
Auth::requireAdmin();

$users = Database::read('users');
?>

<div class="card" style="margin-top: 20px;">
    <div class="card-header">
        <h2 class="card-title">👥 User Management</h2>
    </div>
    <p>Control all users: ban, promote, set points, delete.</p>
</div>

<div class="card" style="margin-top: 30px;">
    <table class="table">
        <thead>
            <tr>
                <th>Avatar</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Points</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($users as $u): ?>
                <tr>
                    <td>
                        <img src="<?= $u['avatar'] ?>" alt="Avatar" style="width: 30px; height: 30px; border-radius: 50%;">
                    </td>
                    <td><?= Security::escape($u['name']) ?></td>
                    <td><?= Security::escape($u['email']) ?></td>
                    <td>
                        <span class="badge badge-primary"><?= ucfirst($u['role']) ?></span>
                    </td>
                    <td>
                        <input type="number" value="<?= $u['points'] ?>" id="points_<?= $u['id'] ?>" style="width: 60px; padding: 4px;">
                    </td>
                    <td>
                        <span class="badge badge-<?= $u['status'] === 'active' ? 'success' : 'danger' ?>">
                            <?= ucfirst($u['status']) ?>
                        </span>
                    </td>
                    <td style="font-size: 12px;">
                        <div style="display: flex; gap: 5px; flex-wrap: wrap;">
                            <button class="btn btn-sm btn-primary" onclick="setPoints('<?= $u['id'] ?>')">💾 Save</button>
                            
                            <?php if($u['role'] !== 'admin'): ?>
                                <button class="btn btn-sm btn-warning" onclick="promoteUser('<?= $u['id'] ?>')">⬆️ Promote</button>
                            <?php endif; ?>
                            
                            <?php if($u['status'] === 'active'): ?>
                                <button class="btn btn-sm btn-danger" onclick="banUser('<?= $u['id'] ?>')">🚫 Ban</button>
                            <?php else: ?>
                                <button class="btn btn-sm btn-success" onclick="unbanUser('<?= $u['id'] ?>')">✅ Unban</button>
                            <?php endif; ?>
                            
                            <button class="btn btn-sm btn-danger" onclick="deleteUser('<?= $u['id'] ?>')">🗑️ Delete</button>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
function setPoints(userId) {
    const points = document.getElementById('points_' + userId).value;
    fetch('/api/admin_users.php?action=set_points', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'user_id=' + userId + '&points=' + points
    })
    .then(r => r.json())
    .then(d => {
        if(d.success) app.showNotification('Success', d.message, 'success');
        else app.showNotification('Error', d.error, 'error');
    });
}

function banUser(userId) {
    const reason = prompt('Ban reason:');
    if(reason) {
        fetch('/api/admin_users.php?action=ban', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'user_id=' + userId + '&reason=' + reason
        })
        .then(r => r.json())
        .then(d => {
            if(d.success) {
                app.showNotification('Success', d.message, 'success');
                setTimeout(() => location.reload(), 1000);
            }
        });
    }
}

function unbanUser(userId) {
    fetch('/api/admin_users.php?action=unban', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'user_id=' + userId
    })
    .then(r => r.json())
    .then(d => {
        if(d.success) {
            app.showNotification('Success', d.message, 'success');
            setTimeout(() => location.reload(), 1000);
        }
    });
}

function promoteUser(userId) {
    if(confirm('Promote this user to admin?')) {
        fetch('/api/admin_users.php?action=promote', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'user_id=' + userId
        })
        .then(r => r.json())
        .then(d => {
            if(d.success) {
                app.showNotification('Success', d.message, 'success');
                setTimeout(() => location.reload(), 1000);
            }
        });
    }
}

function deleteUser(userId) {
    if(confirm('Delete this user? This action cannot be undone!')) {
        fetch('/api/admin_users.php?action=delete', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'user_id=' + userId
        })
        .then(r => r.json())
        .then(d => {
            if(d.success) {
                app.showNotification('Success', d.message, 'success');
                setTimeout(() => location.reload(), 1000);
            }
        });
    }
}
</script>