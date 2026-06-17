<?php
/* ================================
   SECURITY LAYER
   ================================ */

class Security {
    
    /* ================= CSRF TOKEN ================= */
    public static function generateCSRF() {
        if(empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            $_SESSION['csrf_time'] = time();
        }
        return $_SESSION['csrf_token'];
    }
    
    public static function validateCSRF($token) {
        if(empty($_SESSION['csrf_token']) || empty($_SESSION['csrf_time'])) {
            return false;
        }
        
        if(time() - $_SESSION['csrf_time'] > CSRF_TOKEN_LIFETIME) {
            return false;
        }
        
        return hash_equals($_SESSION['csrf_token'], $token);
    }
    
    /* ================= INPUT VALIDATION ================= */
    public static function sanitize($input) {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
    
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    public static function validatePassword($password) {
        if(strlen($password) < 6) {
            return false;
        }
        
        if(!preg_match('/[a-z]/', $password)) {
            return false;
        }
        
        if(!preg_match('/[A-Z]/', $password)) {
            return false;
        }
        
        if(!preg_match('/[0-9]/', $password)) {
            return false;
        }
        
        return true;
    }
    
    public static function validateName($name) {
        if(strlen($name) < 2 || strlen($name) > 100) {
            return false;
        }
        return preg_match('/^[a-zA-Z0-9\s\-_ء-ي]+$/', $name);
    }
    
    /* ================= XSS PROTECTION ================= */
    public static function escape($text) {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }
    
    public static function escapeAttr($text) {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }
    
    /* ================= PASSWORD HASHING ================= */
    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }
    
    public static function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
    
    /* ================= RATE LIMITING ================= */
    public static function checkRateLimit($key, $limit = 5, $window = 900) {
        $file = DATA_DIR . '/rate_limit.json';
        $limits = [];
        
        if(file_exists($file)) {
            $limits = json_decode(file_get_contents($file), true) ?: [];
        }
        
        $now = time();
        
        if(!isset($limits[$key])) {
            $limits[$key] = [];
        }
        
        // Remove old attempts
        $limits[$key] = array_filter($limits[$key], function($time) use($now, $window) {
            return $now - $time < $window;
        });
        
        if(count($limits[$key]) >= $limit) {
            file_put_contents($file, json_encode($limits, JSON_PRETTY_PRINT));
            return false;
        }
        
        $limits[$key][] = $now;
        file_put_contents($file, json_encode($limits, JSON_PRETTY_PRINT));
        
        return true;
    }
    
    /* ================= FILE UPLOAD VALIDATION ================= */
    public static function validateFileUpload($file, $maxSize = 5242880, $allowedTypes = ['application/json']) {
        if($file['size'] > $maxSize) {
            return ['error' => 'File size exceeds limit'];
        }
        
        if(!in_array($file['type'], $allowedTypes)) {
            return ['error' => 'Invalid file type'];
        }
        
        $content = file_get_contents($file['tmp_name']);
        if(json_decode($content) === null) {
            return ['error' => 'Invalid JSON file'];
        }
        
        return ['success' => true];
    }
    
    /* ================= GENERATE ID ================= */
    public static function generateID() {
        return uniqid('user_', true);
    }
    
    public static function generateToken() {
        return bin2hex(random_bytes(32));
    }
    
}

?>