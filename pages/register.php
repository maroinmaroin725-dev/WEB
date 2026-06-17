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
                <h2 class="card-title">✨ Create Account</h2>
            </div>

            <?php if(isset($register_success)): ?>
                <div class="alert alert-success">
                    ✓ <?= Security::escape($register_success) ?>
                    <p>Redirecting to login...</p>
                </div>
            <?php endif; ?>

            <?php if(isset($register_error) && !empty($register_error)): ?>
                <div class="alert alert-error">
                    <?= Security::escape($register_error) ?>
                </div>
            <?php endif; ?>

            <form method="post" action="index.php?page=register">
                <div class="form-group">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="name" class="form-control" placeholder="Your name" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" placeholder="your@email.com" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="pass" class="form-control" placeholder="Min 6 chars with uppercase & numbers" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Confirm Password</label>
                    <input type="password" name="confirm_pass" class="form-control" placeholder="Repeat password" required>
                </div>

                <button type="submit" name="register" class="btn btn-success btn-block">Create Account</button>
            </form>

            <p style="text-align: center; margin-top: 20px;">
                Already have an account? <a href="?page=login" style="color: var(--primary);">Login here</a>
            </p>
        </div>
    </div>

    <div>
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">✅ Requirements</h2>
            </div>
            <ul style="list-style: none;">
                <li style="margin-bottom: 10px;">✓ Name: 2-100 characters</li>
                <li style="margin-bottom: 10px;">✓ Email: Valid format</li>
                <li style="margin-bottom: 10px;">✓ Password: Minimum 6 characters</li>
                <li style="margin-bottom: 10px;">✓ Must contain uppercase & lowercase</li>
                <li style="margin-bottom: 10px;">✓ Must contain at least one number</li>
            </ul>
        </div>
    </div>
</div>