<?php
Auth::requireAdmin();

$settings = Database::read('settings');
?>

<div class="card" style="margin-top: 20px;">
    <div class="card-header">
        <h2 class="card-title">⚙️ Site Settings</h2>
    </div>
    <p>Configure all site settings and features.</p>
</div>

<div class="card" style="margin-top: 30px;">
    <form method="post" action="/api/admin_settings.php?action=update">
        <h3 style="margin-bottom: 20px;">General Settings</h3>
        
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Site Name</label>
                <input type="text" name="site_name" class="form-control" value="<?= $settings['site_name'] ?? '' ?>" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Site URL</label>
                <input type="url" name="site_url" class="form-control" value="<?= $settings['site_url'] ?? '' ?>" required>
            </div>
        </div>
        
        <h3 style="margin-bottom: 20px; margin-top: 30px;">Points System</h3>
        
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Daily Points</label>
                <input type="number" name="daily_points" class="form-control" value="<?= $settings['daily_points'] ?? 5 ?>" min="1">
            </div>
            
            <div class="form-group">
                <label class="form-label">Download Cost (Points)</label>
                <input type="number" name="download_cost" class="form-control" value="<?= $settings['download_cost'] ?? 1 ?>" min="0">
            </div>
        </div>
        
        <h3 style="margin-bottom: 20px; margin-top: 30px;">Security Settings</h3>
        
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Max Login Attempts</label>
                <input type="number" name="max_login_attempts" class="form-control" value="<?= $settings['max_login_attempts'] ?? 5 ?>" min="3">
            </div>
            
            <div class="form-group">
                <label class="form-label">Session Timeout (seconds)</label>
                <input type="number" name="session_timeout" class="form-control" value="<?= $settings['session_timeout'] ?? 3600 ?>" min="300">
            </div>
        </div>
        
        <h3 style="margin-bottom: 20px; margin-top: 30px;">Features</h3>
        
        <div class="form-group">
            <label>
                <input type="checkbox" name="maintenance" <?= ($settings['maintenance'] ?? false) ? 'checked' : '' ?>> 
                <strong>Maintenance Mode</strong> (Disable site for maintenance)
            </label>
        </div>
        
        <div class="form-group">
            <label>
                <input type="checkbox" name="require_2fa" <?= ($settings['require_2fa'] ?? false) ? 'checked' : '' ?>>
                <strong>Require 2FA</strong> (Force two-factor authentication)
            </label>
        </div>
        
        <button type="submit" class="btn btn-primary btn-block" style="margin-top: 30px;">💾 Save Settings</button>
    </form>
</div>