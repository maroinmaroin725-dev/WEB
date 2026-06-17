<?php
/* ================================
   AUTHENTICATION LAYER
   ================================ */

class Auth {
    
    /* ================= CHECK LOGIN ================= */
    public static function isLoggedIn() {
        return isset($_SESSION['user']) && !empty($_SESSION['user']);
    }
    
    /* ================= GET CURRENT USER ================= */
    public static function user() {
        return $_SESSION['user'] ?? null;
    }
    
    /* ================= LOGIN ================= */
    public static function login($email, $password, $remember = false) {
        // Rate limiting
        if(!Security::checkRateLimit('login_' . $email, MAX_LOGIN_ATTEMPTS, LOCKOUT_TIME)) {
            return ['error' => 'Too many login attempts. Please try again later.'];
        }
        
        // Validate input
        if(!Security::validateEmail($email)) {
            return ['error' => 'Invalid email format'];
        }
        
        if(empty($password)) {
            return ['error' => 'Password is required'];
        }
        
        // Find user
        $user = Database::find('users', 'email', $email);
        
        if(!$user) {
            return ['error' => 'Invalid email or password'];
        }
        
        if($user['status'] === 'banned') {
            return ['error' => 'Your account has been banned'];
        }
        
        // Verify password
        if(!Security::verifyPassword($password, $user['pass'])) {
            return ['error' => 'Invalid email or password'];
        }
        
        // Update last login
        Database::update('users', 'id', $user['id'], [
            'last_login' => date('Y-m-d H:i:s'),
            'last_ip' => $_SERVER['REMOTE_ADDR']
        ]);
        
        // Set session
        $_SESSION['user'] = [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role'],
            'points' => $user['points'],
            'avatar' => $user['avatar'] ?? null,
            'status' => $user['status']
        ];
        
        // Remember me
        if($remember) {
            $token = Security::generateToken();
            setcookie('remember_token', $token, time() + REMEMBER_TIMEOUT, '/');
            Database::update('users', 'id', $user['id'], ['remember_token' => $token]);
        }
        
        // Log activity
        self::log('login', $user['id']);
        
        return ['success' => true];
    }
    
    /* ================= REGISTER ================= */
    public static function register($name, $email, $password, $passwordConfirm) {
        // Rate limiting
        if(!Security::checkRateLimit('register_' . $email, 3, 3600)) {
            return ['error' => 'Too many registration attempts'];
        }
        
        // Validate inputs
        if(!Security::validateName($name)) {
            return ['error' => 'Invalid name format'];
        }
        
        if(!Security::validateEmail($email)) {
            return ['error' => 'Invalid email format'];
        }
        
        if(!Security::validatePassword($password)) {
            return ['error' => 'Password must be at least 6 characters with uppercase, lowercase, and numbers'];
        }
        
        if($password !== $passwordConfirm) {
            return ['error' => 'Passwords do not match'];
        }
        
        // Check if email exists
        if(Database::find('users', 'email', $email)) {
            return ['error' => 'Email already registered'];
        }
        
        // Create user
        $user = [
            'id' => Security::generateID(),
            'name' => Security::sanitize($name),
            'email' => strtolower(trim($email)),
            'pass' => Security::hashPassword($password),
            'avatar' => 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&background=random',
            'role' => 'user',
            'points' => INITIAL_POINTS,
            'status' => 'active',
            'created_at' => date('Y-m-d H:i:s'),
            'last_login' => null,
            'last_ip' => $_SERVER['REMOTE_ADDR']
        ];
        
        Database::insert('users', $user);
        
        // Log activity
        self::log('register', $user['id']);
        
        return ['success' => true, 'message' => 'Account created successfully'];
    }
    
    /* ================= LOGOUT ================= */
    public static function logout() {
        if(self::isLoggedIn()) {
            self::log('logout', $_SESSION['user']['id']);
        }
        
        session_destroy();
        setcookie('remember_token', '', time() - 3600, '/');
        
        return true;
    }
    
    /* ================= LOG ACTIVITY ================= */
    public static function log($action, $userId) {
        $log = [
            'id' => Security::generateID(),
            'user_id' => $userId,
            'action' => $action,
            'ip' => $_SERVER['REMOTE_ADDR'],
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        Database::insert('logs', $log);
    }
    
    /* ================= REQUIRE AUTH ================= */
    public static function requireAuth() {
        if(!self::isLoggedIn()) {
            header('Location: index.php?page=login');
            exit;
        }
    }
    
    /* ================= REQUIRE ADMIN ================= */
    public static function requireAdmin() {
        self::requireAuth();
        
        if($_SESSION['user']['role'] !== 'admin') {
            header('HTTP/1.0 403 Forbidden');
            exit('Access Denied');
        }
    }
    
    /* ================= REQUIRE GUEST ================= */
    public static function requireGuest() {
        if(self::isLoggedIn()) {
            header('Location: index.php');
            exit;
        }
    }
}

?>