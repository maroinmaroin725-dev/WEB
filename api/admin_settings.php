<?php
require_once '../core/init.php';

AdminController::requireAdmin();

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? null;

switch($action) {
    case 'get':
        $settings = Database::read('settings');
        jsonResponse(['success' => true, 'settings' => $settings]);
        break;
    
    case 'update':
        if($method !== 'POST') exit;
        
        $settings = [
            'site_name' => $_POST['site_name'] ?? '',
            'site_url' => $_POST['site_url'] ?? '',
            'daily_points' => intval($_POST['daily_points'] ?? 5),
            'download_cost' => intval($_POST['download_cost'] ?? 1),
            'maintenance' => isset($_POST['maintenance']),
            'require_2fa' => isset($_POST['require_2fa']),
            'max_login_attempts' => intval($_POST['max_login_attempts'] ?? 5),
            'session_timeout' => intval($_POST['session_timeout'] ?? 3600)
        ];
        
        $result = AdminController::updateSettings($settings);
        jsonResponse($result);
        break;
    
    case 'security':
        $security = AdminController::getSecuritySettings();
        jsonResponse(['success' => true, 'security' => $security]);
        break;
    
    case 'block_ip':
        if($method !== 'POST') exit;
        
        SecurityAdvanced::blockIP(
            $_POST['ip'],
            $_POST['reason'] ?? 'Manual block'
        );
        jsonResponse(['success' => true, 'message' => 'IP blocked']);
        break;
    
    case 'unblock_ip':
        if($method !== 'POST') exit;
        
        $blacklist = Database::read('security/ip_blacklist');
        $blacklist = array_filter($blacklist, function($entry) {
            return $entry['ip'] !== $_POST['ip'];
        });
        Database::write('security/ip_blacklist', array_values($blacklist));
        
        jsonResponse(['success' => true, 'message' => 'IP unblocked']);
        break;
    
    default:
        jsonResponse(['error' => 'Invalid action'], 400);
}

?>