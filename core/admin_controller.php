<?php
/* ================================
   ADMIN CONTROLLER
   ================================ */

class AdminController {
    
    /* ================= CHECK ADMIN PERMISSION ================= */
    public static function requireAdmin() {
        if(!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            jsonResponse(['error' => 'Admin access required'], 403);
        }
    }
    
    /* ================= MANAGE USERS ================= */
    public static function manageUser($userId, $action, $data) {
        self::requireAdmin();
        
        $user = Database::find('users', 'id', $userId);
        if(!$user) {
            return ['error' => 'User not found'];
        }
        
        switch($action) {
            case 'ban':
                Database::update('users', 'id', $userId, [
                    'status' => 'banned',
                    'banned_at' => date('Y-m-d H:i:s'),
                    'ban_reason' => $data['reason'] ?? ''
                ]);
                SecurityAdvanced::logSecurityEvent('user_banned', $userId);
                return ['success' => true, 'message' => 'User banned'];
            
            case 'unban':
                Database::update('users', 'id', $userId, [
                    'status' => 'active',
                    'ban_reason' => null
                ]);
                return ['success' => true, 'message' => 'User unbanned'];
            
            case 'promote':
                Database::update('users', 'id', $userId, ['role' => 'admin']);
                SecurityAdvanced::logSecurityEvent('user_promoted', $userId);
                return ['success' => true, 'message' => 'User promoted to admin'];
            
            case 'demote':
                Database::update('users', 'id', $userId, ['role' => 'user']);
                return ['success' => true, 'message' => 'User demoted to user'];
            
            case 'set_points':
                Database::update('users', 'id', $userId, [
                    'points' => intval($data['points'])
                ]);
                return ['success' => true, 'message' => 'Points updated'];
            
            case 'delete':
                Database::delete('users', 'id', $userId);
                SecurityAdvanced::logSecurityEvent('user_deleted', $userId);
                return ['success' => true, 'message' => 'User deleted'];
            
            default:
                return ['error' => 'Invalid action'];
        }
    }
    
    /* ================= SITE SETTINGS ================= */
    public static function updateSettings($settings) {
        self::requireAdmin();
        
        $current = Database::read('settings');
        $updated = array_merge($current, $settings);
        Database::write('settings', $updated);
        
        SecurityAdvanced::logSecurityEvent('settings_changed', json_encode($settings));
        return ['success' => true];
    }
    
    /* ================= SECURITY SETTINGS ================= */
    public static function getSecuritySettings() {
        self::requireAdmin();
        
        return [
            'ip_blacklist' => Database::read('security/ip_blacklist'),
            'security_events' => array_slice(Database::read('security/events'), -50),
            'brute_force_attempts' => file_exists(DATA_DIR . '/security/brute_force.json') ? 
                json_decode(file_get_contents(DATA_DIR . '/security/brute_force.json'), true) : []
        ];
    }
    
    /* ================= MANAGE CONFIGS ================= */
    public static function deleteConfig($configId) {
        self::requireAdmin();
        
        $providers = ['inwi', 'iam', 'orange'];
        foreach($providers as $provider) {
            $file = CONFIGS_DIR . '/' . $provider . '/' . $configId . '.json';
            if(file_exists($file)) {
                unlink($file);
                SecurityAdvanced::logSecurityEvent('config_deleted', $configId);
                return ['success' => true];
            }
        }
        
        return ['error' => 'Config not found'];
    }
    
    /* ================= VIEW LOGS ================= */
    public static function getLogs($limit = 100) {
        self::requireAdmin();
        
        $logs = Database::read('logs');
        return array_slice($logs, -$limit);
    }
    
    /* ================= GENERATE REPORT ================= */
    public static function generateReport() {
        self::requireAdmin();
        
        $stats = getAdminStats();
        $security = self::getSecuritySettings();
        
        return [
            'generated_at' => date('Y-m-d H:i:s'),
            'stats' => $stats,
            'security' => $security,
            'recent_logs' => array_slice(Database::read('logs'), -20)
        ];
    }
}

?>