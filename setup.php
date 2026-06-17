<?php
/* ================================
   PROJECT SETUP
   ================================ */

// Create directories
$dirs = [
    'data',
    'configs/inwi',
    'configs/iam',
    'configs/orange',
    'assets/css',
    'assets/js',
    'assets/images',
    'api',
    'pages',
    'admin'
];

foreach($dirs as $d) {
    if(!is_dir($d)) {
        mkdir($d, 0777, true);
    }
}

// Create JSON files
$files = [
    'data/users.json' => [
        [
            'id' => 'admin_001',
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'pass' => password_hash('Admin123', PASSWORD_BCRYPT),
            'avatar' => 'https://ui-avatars.com/api/?name=Admin&background=2563eb',
            'role' => 'admin',
            'points' => 999,
            'status' => 'active',
            'created_at' => date('Y-m-d H:i:s'),
            'last_login' => null,
            'last_ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
        ]
    ],
    'data/settings.json' => [
        'site_name' => 'Config System Pro',
        'site_url' => 'http://localhost/WEB',
        'daily_points' => 5,
        'download_cost' => 1,
        'maintenance' => false
    ],
    'data/downloads.json' => [],
    'data/favorites.json' => [],
    'data/logs.json' => [],
];

foreach($files as $file => $content) {
    file_put_contents($file, json_encode($content, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

// Sample configs
$configs = [
    'configs/inwi/inwi_main.json' => [
        'name' => 'Inwi Main Config',
        'provider' => 'Inwi',
        'protocol' => 'SSH',
        'host' => 'config.inwi.ma',
        'port' => 22,
        'country' => 'Morocco',
        'description' => 'Main configuration for Inwi provider'
    ],
    'configs/iam/iam_premium.json' => [
        'name' => 'IAM Premium',
        'provider' => 'IAM',
        'protocol' => 'SSH',
        'host' => 'config.iam.ma',
        'port' => 22,
        'country' => 'Morocco',
        'description' => 'Premium configuration for IAM'
    ],
    'configs/orange/orange_business.json' => [
        'name' => 'Orange Business',
        'provider' => 'Orange',
        'protocol' => 'SSH',
        'host' => 'config.orange.ma',
        'port' => 22,
        'country' => 'Morocco',
        'description' => 'Business configuration for Orange'
    ]
];

foreach($configs as $file => $content) {
    file_put_contents($file, json_encode($content, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

echo "<h1 style='color: green; font-family: Arial;'>✓ PROJECT SETUP COMPLETED!</h1>";
echo "<p style='font-family: Arial;'>All files and directories have been created successfully.</p>";
echo "<p style='font-family: Arial;'><a href='index.php' style='color: blue;'>Go to Application</a></p>";
?>