<?php
if($user) {
    header('Location: index.php');
    exit;
}
?>
<div class="grid grid-2" style="margin-top: 40px;">
    <div>
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">🔐 Login</h2>
            </div>

            <?php if(isset($login_error)): ?>
                <div class="alert alert-error">
                    <?= Security::escape($login_error) ?>
                </div>
            <?php endif; ?>

            <form method="post" action="index.php">
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" placeholder="your@email.com" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="pass" class="form-control" placeholder="••••••••" required>
                </div>

                <div class="form-group">
                    <label>
                        <input type="checkbox" name="remember"> Remember me
                    </label>
                </div>

                <button type="submit" name="login" class="btn btn-primary btn-block">Login</button>
            </form>

            <p style="text-align: center; margin-top: 20px;">
                Don't have an account? <a href="?page=register" style="color: var(--primary);">Register here</a>
            </p>
        </div>
    </div>

    <div>
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">📝 Demo Account</h2>
            </div>
            <p style="margin-bottom: 20px;">
                Use these credentials to test the application:
            </p>
            <ul style="list-style: none;">
                <li><strong>Email:</strong> admin@admin.com</li>
                <li><strong>Password:</strong> Admin123</li>
            </ul>
            <p style="margin-top: 20px; font-size: 12px; color: var(--text-secondary);">
                ℹ️ This is a demo account with admin privileges.
            </p>
        </div>
    </div>
</div>