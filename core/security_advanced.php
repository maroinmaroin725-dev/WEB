<?php
/* ================================
   ADVANCED SECURITY SYSTEM
   ================================ */

class SecurityAdvanced {
    
    /* ================= IP BLACKLIST ================= */
    public static function checkIPBlacklist() {
        $blacklist = Database::read('security/ip_blacklist');
        $clientIP = self::getClientIP();
        
        foreach($blacklist as $entry) {
            if($entry['ip'] === $clientIP && $entry['status'] === 'active') {
                http_response_code(403);
                exit('Your IP has been blocked.');
            }
        }
    }
    
    /* ================= SQL INJECTION PROTECTION ================= */
    public static function preventSQLInjection($input) {
        $dangerous = ['DROP', 'DELETE', 'INSERT', 'UPDATE', 'UNION', 'SELECT', 'EXEC', 'SCRIPT'];
        $input_upper = strtoupper($input);
        
        foreach($dangerous as $keyword) {
            if(strpos($input_upper, $keyword) !== false) {
                self::logSecurityEvent('sql_injection_attempt', $input);
                return false;
            }
        }
        return true;
    }
    
    /* ================= XSS PROTECTION ================= */
    public static function preventXSS($input) {
        $dangerous_tags = ['<script', '<iframe', '<object', '<embed', 'javascript:', 'onerror=', 'onload='];
        $input_lower = strtolower($input);
        
        foreach($dangerous_tags as $tag) {
            if(strpos($input_lower, $tag) !== false) {
                self::logSecurityEvent('xss_attempt', $input);
                return false;
            }
        }
        return true;
    }
    
    /* ================= BRUTE FORCE DETECTION ================= */
    public static function detectBruteForce($identifier) {
        $file = DATA_DIR . '/security/brute_force.json';
        $attempts = [];
        
        if(file_exists($file)) {
            $attempts = json_decode(file_get_contents($file), true) ?: [];
        }
        
        if(!isset($attempts[$identifier])) {
            $attempts[$identifier] = [];
        }
        
        $now = time();
        $attempts[$identifier] = array_filter($attempts[$identifier], function($time) use($now) {
            return $now - $time < 3600; // 1 hour window
        });
        
        if(count($attempts[$identifier]) >= 10) {
            self::logSecurityEvent('brute_force_detected', $identifier);
            self::blockIP(self::getClientIP(), 'Brute force detected');
            return false;
        }
        
        $attempts[$identifier][] = $now;
        file_put_contents($file, json_encode($attempts));
        return true;
    }
    
    /* ================= SESSION HIJACKING PREVENTION ================= */
    public static function validateSessionIntegrity() {
        if(!isset($_SESSION['user_agent']) || !isset($_SESSION['ip_address'])) {
            return true; // First time
        }
        
        $current_ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $current_ip = self::getClientIP();
        
        if($_SESSION['user_agent'] !== $current_ua || $_SESSION['ip_address'] !== $current_ip) {
            self::logSecurityEvent('session_hijacking_attempt', $current_ip);
            session_destroy();
            return false;
        }
        
        return true;
    }
    
    /* ================= BLOCK IP ================= */
    public static function blockIP($ip, $reason = '') {
        $blacklist = Database::read('security/ip_blacklist');
        
        $entry = [
            'ip' => $ip,
            'reason' => $reason,
            'status' => 'active',
            'blocked_at' => date('Y-m-d H:i:s'),
            'blocked_until' => date('Y-m-d H:i:s', strtotime('+24 hours'))
        ];
        
        $blacklist[] = $entry;
        Database::write('security/ip_blacklist', $blacklist);
    }
    
    /* ================= GET CLIENT IP ================= */
    public static function getClientIP() {
        if(!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return filter_var($ip, FILTER_VALIDATE_IP) ?: '0.0.0.0';
    }
    
    /* ================= LOG SECURITY EVENT ================= */
    public static function logSecurityEvent($type, $details) {
        $event = [
            'id' => Security::generateID(),
            'type' => $type,
            'details' => $details,
            'ip' => self::getClientIP(),
            'timestamp' => date('Y-m-d H:i:s'),
            'severity' => 'high'
        ];
        
        $events = Database::read('security/events');
        $events[] = $event;
        Database::write('security/events', $events);
    }
    
    /* ================= TWO FACTOR AUTHENTICATION ================= */
    public static function generateTOTP() {
        return random_int(100000, 999999);
    }
    
    public static function verifyTOTP($user_id, $code) {
        $user = Database::find('users', 'id', $user_id);
        if(!$user || !isset($user['totp_code'])) {
            return false;
        }
        
        if($user['totp_code'] !== $code) {
            return false;
        }
        
        // Clear TOTP code after verification
        Database::update('users', 'id', $user_id, ['totp_code' => null]);
        return true;
    }
    
    /* ================= FILE INTEGRITY CHECK ================= */
    public static function checkFileIntegrity() {
        $critical_files = [
            'index.php',
            'core/auth.php',
            'core/security.php',
            'core/db.php'
        ];
        
        foreach($critical_files as $file) {
            if(!file_exists($file)) {
                self::logSecurityEvent('file_missing', $file);
                return false;
            }
        }
        return true;
    }
    
    /* ================= ENCRYPT SENSITIVE DATA ================= */
    public static function encryptData($data, $key = null) {
        $key = $key ?? hash('sha256', APP_NAME, true);
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('AES-256-CBC'));
        $encrypted = openssl_encrypt($data, 'AES-256-CBC', $key, 0, $iv);
        return base64_encode($iv . $encrypted);
    }
    
    public static function decryptData($data, $key = null) {
        $key = $key ?? hash('sha256', APP_NAME, true);
        $data = base64_decode($data);
        $iv = substr($data, 0, openssl_cipher_iv_length('AES-256-CBC'));
        $encrypted = substr($data, openssl_cipher_iv_length('AES-256-CBC'));
        return openssl_decrypt($encrypted, 'AES-256-CBC', $key, 0, $iv);
    }
}

?>