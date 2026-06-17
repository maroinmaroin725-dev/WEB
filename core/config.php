<?php
/* ================================
   CONFIGURATION FILE
   ================================ */

define('APP_NAME', 'Config System Pro');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'http://localhost/WEB');
define('DATA_DIR', __DIR__ . '/../data');
define('CONFIGS_DIR', __DIR__ . '/../configs');

/* ================= ENVIRONMENT ================= */
define('ENV', 'development'); // production, development
define('DEBUG', true);

/* ================= SESSION ================= */
define('SESSION_TIMEOUT', 3600 * 24); // 24 hours
define('REMEMBER_TIMEOUT', 3600 * 24 * 30); // 30 days

/* ================= SECURITY ================= */
define('CSRF_TOKEN_LIFETIME', 3600);
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOCKOUT_TIME', 900); // 15 minutes

/* ================= POINTS SYSTEM ================= */
define('INITIAL_POINTS', 5);
define('DAILY_POINTS', 5);
define('DOWNLOAD_COST', 1);
define('POINTS_RESET_TIME', 24); // hours

/* ================= PAGINATION ================= */
define('ITEMS_PER_PAGE', 12);

/* ================= DATABASE PATHS ================= */
define('DB_USERS', DATA_DIR . '/users.json');
define('DB_SETTINGS', DATA_DIR . '/settings.json');
define('DB_DOWNLOADS', DATA_DIR . '/downloads.json');
define('DB_FAVORITES', DATA_DIR . '/favorites.json');
define('DB_LOGS', DATA_DIR . '/logs.json');

/* ================= UI SETTINGS ================= */
define('DARK_MODE_DEFAULT', true);
define('LANGUAGE', 'ar'); // ar, en

/* ================= Error Handler ================= */
if(DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

/* ================= Timezone ================= */
date_default_timezone_set('Africa/Casablanca');

?>