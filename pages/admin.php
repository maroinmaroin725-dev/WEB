<?php
Auth::requireAdmin();

$stats = getAdminStats();
?>
<div class="card" style="margin-top: 20px;">
    <div class="card-header">
        <h2 class="card-title">🛡️ Admin Dashboard</h2>
    </div>
</div>

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
            <h3 style="font-size: 32px; color: #10b981;">📥</h3>
            <h3><?= $stats['totalDownloads'] ?></h3>
            <p class="card-subtitle">Total Downloads</p>
        </div>
    </div>
</div>

<div class="card" style="margin-top: 30px;">
    <div class="card-header">
        <h2 class="card-title">👨‍💼 User Management</h2>
    </div>
    
    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Points</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach(Database::read('users') as $u): ?>
                <tr>
                    <td><?= Security::escape($u['name']) ?></td>
                    <td><?= Security::escape($u['email']) ?></td>
                    <td><span class="badge badge-primary"><?= ucfirst($u['role']) ?></span></td>
                    <td><?= $u['points'] ?></td>
                    <td><span class="badge badge-success"><?= ucfirst($u['status']) ?></span></td>
                    <td>
                        <button class="btn btn-sm btn-warning">Edit</button>
                        <button class="btn btn-sm btn-danger">Ban</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>