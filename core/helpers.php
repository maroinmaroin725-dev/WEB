<?php
/* ================================
   HELPER FUNCTIONS
   ================================ */

/* ================= FORMAT HELPERS ================= */
function formatDate($date) {
    return date('d M Y', strtotime($date));
}

function formatTime($date) {
    return date('H:i', strtotime($date));
}

function formatDateTime($date) {
    return date('d M Y H:i', strtotime($date));
}

function timeAgo($date) {
    $now = new DateTime();
    $past = new DateTime($date);
    $diff = $now->diff($past);
    
    if($diff->days > 0) {
        return $diff->days . ' days ago';
    }
    if($diff->h > 0) {
        return $diff->h . ' hours ago';
    }
    if($diff->i > 0) {
        return $diff->i . ' minutes ago';
    }
    return 'Just now';
}

function formatBytes($bytes) {
    $units = ['B', 'KB', 'MB', 'GB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= (1 << (10 * $pow));
    
    return round($bytes, 2) . ' ' . $units[$pow];
}

function formatNumber($number) {
    return number_format($number, 0, '.', ',');
}

function truncate($text, $length = 50) {
    return strlen($text) > $length ? substr($text, 0, $length) . '...' : $text;
}

/* ================= VALIDATION HELPERS ================= */
function isValidURL($url) {
    return filter_var($url, FILTER_VALIDATE_URL) !== false;
}

function isValidJSON($json) {
    json_decode($json);
    return json_last_error() === JSON_ERROR_NONE;
}

/* ================= CONFIG HELPERS ================= */
function getConfigs($provider) {
    $dir = CONFIGS_DIR . '/' . strtolower($provider);
    
    if(!is_dir($dir)) {
        return [];
    }
    
    $files = array_filter(scandir($dir), function($file) {
        return $file !== '.' && $file !== '..' && pathinfo($file, PATHINFO_EXTENSION) === 'json';
    });
    
    $configs = [];
    foreach($files as $file) {
        $path = $dir . '/' . $file;
        $content = json_decode(file_get_contents($path), true);
        
        $configs[] = [
            'id' => pathinfo($file, PATHINFO_FILENAME),
            'file' => $file,
            'path' => $path,
            'data' => $content,
            'created' => filemtime($path)
        ];
    }
    
    return $configs;
}

function getAllConfigs() {
    return array_merge(
        getConfigs('inwi'),
        getConfigs('iam'),
        getConfigs('orange')
    );
}

function getConfigStats() {
    return [
        'total' => count(getAllConfigs()),
        'inwi' => count(getConfigs('inwi')),
        'iam' => count(getConfigs('iam')),
        'orange' => count(getConfigs('orange'))
    ];
}

function getProviderFromPath($path) {
    if(strpos($path, 'inwi') !== false) return 'inwi';
    if(strpos($path, 'iam') !== false) return 'iam';
    if(strpos($path, 'orange') !== false) return 'orange';
    return 'unknown';
}

/* ================= USER HELPERS ================= */
function getUserStats($userId) {
    $downloads = Database::findAll('downloads', 'user_id', $userId);
    $favorites = Database::findAll('favorites', 'user_id', $userId);
    
    return [
        'downloads' => count($downloads),
        'favorites' => count($favorites),
        'lastDownload' => count($downloads) > 0 ? $downloads[count($downloads)-1]['created_at'] : null
    ];
}

function checkPoints($userId, $points = DOWNLOAD_COST) {
    $user = Database::find('users', 'id', $userId);
    return $user && $user['points'] >= $points;
}

function deductPoints($userId, $points = DOWNLOAD_COST) {
    $user = Database::find('users', 'id', $userId);
    if($user) {
        $newPoints = max(0, $user['points'] - $points);
        Database::update('users', 'id', $userId, ['points' => $newPoints]);
        return true;
    }
    return false;
}

function resetDailyPoints($userId) {
    $user = Database::find('users', 'id', $userId);
    if($user) {
        $lastReset = strtotime($user['last_points_reset'] ?? '2000-01-01');
        $now = time();
        
        if($now - $lastReset >= POINTS_RESET_TIME * 3600) {
            Database::update('users', 'id', $userId, [
                'points' => DAILY_POINTS,
                'last_points_reset' => date('Y-m-d H:i:s')
            ]);
            return true;
        }
    }
    return false;
}

/* ================= STATS HELPERS ================= */
function getAdminStats() {
    return [
        'totalUsers' => Database::count('users'),
        'activeUsers' => count(array_filter(Database::read('users'), function($u) {
            return $u['status'] === 'active';
        })),
        'totalConfigs' => count(getAllConfigs()),
        'totalDownloads' => Database::count('downloads'),
        'totalFavorites' => Database::count('favorites'),
        'recentActivity' => array_slice(Database::read('logs'), -10)
    ];
}

/* ================= RESPONSE HELPERS ================= */
function jsonResponse($data = [], $status = 200) {
    header('Content-Type: application/json');
    http_response_code($status);
    echo json_encode($data);
    exit;
}

function redirect($url) {
    header('Location: ' . $url);
    exit;
}

?>