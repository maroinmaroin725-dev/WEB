<?php
Auth::requireAuth();

$favorites = Database::findAll('favorites', 'user_id', $user['id']);
?>
<div class="card" style="margin-top: 20px;">
    <div class="card-header">
        <h2 class="card-title">⭐ My Favorite Configurations</h2>
    </div>
    <p>Your saved configurations appear here.</p>
</div>

<?php if(empty($favorites)): ?>
    <div class="card" style="text-align: center; padding: 60px 20px;">
        <h3 style="margin-bottom: 10px;">No favorites yet</h3>
        <p class="card-subtitle">Start adding your favorite configs!</p>
        <a href="?page=configs" class="btn btn-primary" style="margin-top: 20px;">Browse Configs</a>
    </div>
<?php else: ?>
    <div class="grid grid-2" style="margin-top: 30px;">
        <?php foreach($favorites as $fav): ?>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><?= Security::escape($fav['config_name'] ?? 'Unknown') ?></h3>
                </div>
                <p class="card-subtitle">Added: <?= formatDate($fav['created_at']) ?></p>
                <div style="display: flex; gap: 10px; margin-top: 15px;">
                    <button class="btn btn-sm btn-primary">📥 Download</button>
                    <button class="btn btn-sm btn-danger">🗑️ Remove</button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>