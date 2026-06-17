<?php
require_once 'core/init.php';

// Handle authentication
if(isset($_POST['login'])) {
    $result = Auth::login($_POST['email'] ?? '', $_POST['pass'] ?? '', $_POST['remember'] ?? false);
    if($result['success'] ?? false) {
        header('Location: index.php');
        exit;
    }
    $login_error = $result['error'] ?? 'Login failed';
}

if(isset($_POST['register'])) {
    $result = Auth::register(
        $_POST['name'] ?? '',
        $_POST['email'] ?? '',
        $_POST['pass'] ?? '',
        $_POST['confirm_pass'] ?? ''
    );
    if($result['success'] ?? false) {
        $register_success = $result['message'];
    }
    $register_error = $result['error'] ?? '';
}

if(isset($_GET['logout'])) {
    Auth::logout();
    header('Location: index.php');
    exit;
}

$user = Auth::user();
$page = $_GET['page'] ?? 'home';
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/dark-mode.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
</head>
<body>
    <!-- NAVBAR -->
    <nav class="navbar">
        <div class="container">
            <div class="navbar-content">
                <a href="index.php" class="navbar-brand">⚙️ <?= APP_NAME ?></a>
                
                <ul class="navbar-menu">
                    <?php if($user): ?>
                        <li><a href="index.php?page=dashboard" class="<?= $page === 'dashboard' ? 'active' : '' ?>">Dashboard</a></li>
                        <li><a href="index.php?page=configs" class="<?= $page === 'configs' ? 'active' : '' ?>">Configs</a></li>
                        <li><a href="index.php?page=favorites" class="<?= $page === 'favorites' ? 'active' : '' ?>">Favorites</a></li>
                        <?php if($user['role'] === 'admin'): ?>
                            <li><a href="index.php?page=admin" class="<?= $page === 'admin' ? 'active' : '' ?>">Admin</a></li>
                        <?php endif; ?>
                    <?php else: ?>
                        <li><a href="index.php?page=login">Login</a></li>
                        <li><a href="index.php?page=register">Register</a></li>
                    <?php endif; ?>
                </ul>
                
                <div class="navbar-right">
                    <button id="theme-toggle" class="btn btn-sm btn-outline">🌙 Theme</button>
                    <?php if($user): ?>
                        <div class="user-info">
                            <img src="<?= $user['avatar'] ?>" alt="Avatar" class="user-avatar">
                            <span><?= Security::escape($user['name']) ?></span>
                            <a href="?logout=1" class="btn btn-sm btn-danger">Logout</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- MAIN CONTENT -->
    <main class="container">
        <?php
        if(!$user && $page !== 'login' && $page !== 'register') {
            $page = 'login';
        }

        include 'pages/' . $page . '.php';
        ?>
    </main>

    <!-- FOOTER -->
    <footer>
        <div class="container">
            <p>&copy; 2026 <?= APP_NAME ?>. All rights reserved.</p>
        </div>
    </footer>

    <script src="assets/js/main.js"></script>
    <script src="assets/js/search.js"></script>
</body>
</html>