<?php
Auth::requireAuth();

$all_configs = getAllConfigs();
$stats = getConfigStats();
?>
<div class="card" style="margin-top: 20px;">
    <div class="card-header">
        <h2 class="card-title">⚙️ Configurations</h2>
        <div>
            <input type="text" placeholder="Search configs..." class="form-control" style="width: 200px; display: inline-block;">
        </div>
    </div>
</div>

<div class="grid grid-3" style="margin-top: 20px;">
    <div class="card">
        <div style="text-align: center;">
            <h3 style="font-size: 28px; color: var(--primary);">📦</h3>
            <h3><?= $stats['total'] ?></h3>
            <p class="card-subtitle">Total Configs</p>
        </div>
    </div>
    
    <div class="card">
        <div style="text-align: center;">
            <h3 style="font-size: 28px; color: #f59e0b;">🌐</h3>
            <h3><?= $stats['inwi'] ?></h3>
            <p class="card-subtitle">Inwi Configs</p>
        </div>
    </div>
    
    <div class="card">
        <div style="text-align: center;">
            <h3 style="font-size: 28px; color: #10b981;">🔧</h3>
            <h3><?= $stats['iam'] + $stats['orange'] ?></h3>
            <p class="card-subtitle">Other Providers</p>
        </div>
    </div>
</div>

<div class="grid grid-2" style="margin-top: 30px;">
    <?php foreach($all_configs as $config): ?>
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><?= Security::escape($config['data']['name'] ?? 'Unknown') ?></h3>
            </div>
            <table class="table" style="font-size: 13px;">
                <tr>
                    <td><strong>Provider:</strong></td>
                    <td><?= Security::escape($config['data']['provider'] ?? 'N/A') ?></td>
                </tr>
                <tr>
                    <td><strong>Protocol:</strong></td>
                    <td><span class="badge badge-primary"><?= Security::escape($config['data']['protocol'] ?? 'N/A') ?></span></td>
                </tr>
                <tr>
                    <td><strong>Country:</strong></td>
                    <td><?= Security::escape($config['data']['country'] ?? 'N/A') ?></td>
                </tr>
            </table>
            <p style="margin: 15px 0; font-size: 12px; color: var(--text-secondary);">
                <?= Security::escape(truncate($config['data']['description'] ?? '', 80)) ?>
            </p>
            <div style="display: flex; gap: 10px;">
                <button class="btn btn-sm btn-primary">📥 Download</button>
                <button class="btn btn-sm btn-outline">⭐ Favorite</button>
            </div>
        </div>
    <?php endforeach; ?>
</div>