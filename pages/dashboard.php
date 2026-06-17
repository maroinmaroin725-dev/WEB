<?php
Auth::requireAuth();
?>
<div class="card" style="margin-top: 20px;">
    <div class="card-header">
        <h2 class="card-title">📊 Dashboard</h2>
    </div>
    <p>Welcome to your personal dashboard.</p>
</div>

<div class="grid grid-2" style="margin-top: 30px;">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">👤 Profile Information</h3>
        </div>
        <table class="table">
            <tr>
                <td><strong>Name:</strong></td>
                <td><?= Security::escape($user['name']) ?></td>
            </tr>
            <tr>
                <td><strong>Email:</strong></td>
                <td><?= Security::escape($user['email']) ?></td>
            </tr>
            <tr>
                <td><strong>Role:</strong></td>
                <td><span class="badge badge-primary"><?= ucfirst($user['role']) ?></span></td>
            </tr>
            <tr>
                <td><strong>Status:</strong></td>
                <td><span class="badge badge-success"><?= ucfirst($user['status']) ?></span></td>
            </tr>
        </table>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">📈 Statistics</h3>
        </div>
        <?php $stats = getUserStats($user['id']); ?>
        <table class="table">
            <tr>
                <td><strong>Total Downloads:</strong></td>
                <td><?= $stats['downloads'] ?></td>
            </tr>
            <tr>
                <td><strong>Favorite Configs:</strong></td>
                <td><?= $stats['favorites'] ?></td>
            </tr>
            <tr>
                <td><strong>Points:</strong></td>
                <td><?= $user['points'] ?></td>
            </tr>
            <tr>
                <td><strong>Member Since:</strong></td>
                <td><?= formatDate($user['created_at'] ?? 'N/A') ?></td>
            </tr>
        </table>
    </div>
</div>