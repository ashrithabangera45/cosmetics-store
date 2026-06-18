<?php
/**
 * Authentication Middleware
 * Handles user authentication and authorization
 */

class Auth {
    private $db;

    public function __construct() {
        require_once __DIR__ . '/../config/Database.php';
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Hash password using bcrypt
     */
    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    /**
     * Verify password
     */
    public static function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }

    /**
     * Check if user is logged in
     */
    public static function isLoggedIn() {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }

    /**
     * Get current user
     */
    public static function getCurrentUser() {
        if (self::isLoggedIn()) {
            return $_SESSION['user'];
        }
        return null;
    }

    /**
     * Check if user is admin
     */
    public static function isAdmin() {
        if (self::isLoggedIn()) {
            return $_SESSION['user']['role'] === 'admin';
        }
        return false;
    }

    /**
     * Login user
     */
    public function login($email, $password) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && self::verifyPassword($password, $user['password'])) {
                if (!$user['is_active']) {
                    return ['success' => false, 'message' => 'Account is inactive'];
                }

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'role' => $user['role']
                ];
                $_SESSION['login_time'] = time();

                return ['success' => true, 'message' => 'Login successful'];
            }

            return ['success' => false, 'message' => 'Invalid credentials'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Login failed: ' . $e->getMessage()];
        }
    }

    /**
     * Logout user
     */
    public static function logout() {
        session_destroy();
        return ['success' => true, 'message' => 'Logged out successfully'];
    }

    /**
     * Register user
     */
    public function register($name, $email, $password, $phone = '') {
        try {
            // Check if user already exists
            $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->rowCount() > 0) {
                return ['success' => false, 'message' => 'Email already registered'];
            }

            // Hash password
            $hashedPassword = self::hashPassword($password);

            // Insert user
            $stmt = $this->db->prepare("
                INSERT INTO users (name, email, password, phone, role)
                VALUES (?, ?, ?, ?, 'user')
            ");
            
            $stmt->execute([$name, $email, $hashedPassword, $phone]);
            $userId = $this->db->lastInsertId();

            return ['success' => true, 'message' => 'Registration successful', 'user_id' => $userId];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Registration failed: ' . $e->getMessage()];
        }
    }

    /**
     * Update user profile
     */
    public function updateProfile($userId, $data) {
        try {
            $allowed_fields = ['name', 'phone', 'address', 'city', 'state', 'postal_code', 'country'];
            $update_data = [];

            foreach ($data as $key => $value) {
                if (in_array($key, $allowed_fields)) {
                    $update_data[$key] = $value;
                }
            }

            if (empty($update_data)) {
                return ['success' => false, 'message' => 'No valid fields to update'];
            }

            $set_clause = implode(', ', array_map(fn($key) => "$key = ?", array_keys($update_data)));
            $values = array_values($update_data);
            $values[] = $userId;

            $stmt = $this->db->prepare("UPDATE users SET $set_clause WHERE id = ?");
            $stmt->execute($values);

            return ['success' => true, 'message' => 'Profile updated successfully'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Update failed: ' . $e->getMessage()];
        }
    }

    /**
     * Require login - redirect if not logged in
     */
    public static function requireLogin() {
        if (!self::isLoggedIn()) {
            header('Location: /frontend/pages/login.html');
            exit;
        }
    }

    /**
     * Require admin - redirect if not admin
     */
    public static function requireAdmin() {
        if (!self::isAdmin()) {
            header('HTTP/1.0 403 Forbidden');
            exit;
        }
    }
}