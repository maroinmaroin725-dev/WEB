<?php
if(!$user) {
    include 'login.php';
    exit;
}
?>
<div class="card" style="margin-top: 20px;">
    <div class="card-header">
        <h2 class="card-title">👋 Welcome <?= Security::escape($user['name']) ?>!</h2>
    </div>
    <p>This is your Config System dashboard. Here you can manage and download configurations.</p>
</div>

<div class="grid grid-3" style="margin-top: 30px;">
    <?php 
    $stats = getAdminStats();
    $configs = getAllConfigs();
    $userStats = getUserStats($user['id']);
    ?>
    
    <div class="card">
        <div style="text-align: center;">
            <h3 style="font-size: 32px; color: var(--primary);">⚙️</h3>
            <h3><?= count($configs) ?></h3>
            <p class="card-subtitle">Total Configs</p>
        </div>
    </div>

    <div class="card">
        <div style="text-align: center;">
            <h3 style="font-size: 32px; color: var(--success);">📥</h3>
            <h3><?= $userStats['downloads'] ?></h3>
            <p class="card-subtitle">Your Downloads</p>
        </div>
    </div>

    <div class="card">
        <div style="text-align: center;">
            <h3 style="font-size: 32px; color: var(--primary);">⭐</h3>
            <h3><?= $user['points'] ?></h3>
            <p class="card-subtitle">Your Points</p>
        </div>
    </div>
</div>