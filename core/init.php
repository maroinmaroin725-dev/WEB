<?php
/* ================================
   APPLICATION INITIALIZATION
   ================================ */

session_start();

// Load all core files
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/security.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/helpers.php';

// Create data directory if not exists
if(!is_dir(DATA_DIR)) {
    mkdir(DATA_DIR, 0777, true);
}

// Initialize databases if not exist
$required_files = ['users', 'settings', 'downloads', 'favorites', 'logs'];
foreach($required_files as $file) {
    $path = DATA_DIR . '/' . $file . '.json';
    if(!file_exists($path)) {
        file_put_contents($path, json_encode([], JSON_PRETTY_PRINT));
    }
}

?>