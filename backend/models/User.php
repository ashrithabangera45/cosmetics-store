<?php
/**
 * User Model
 */

class User {
    private $db;
    private $table = 'users';

    public function __construct() {
        require_once __DIR__ . '/../config/Database.php';
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Get all users (admin only)
     */
    public function getAllUsers($limit = 10, $offset = 0) {
        try {
            $sql = "SELECT id, name, email, phone, role, is_active, created_at 
                    FROM {$this->table} 
                    ORDER BY created_at DESC 
                    LIMIT ? OFFSET ?";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$limit, $offset]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Get user by ID
     */
    public function getUserById($id) {
        try {
            $sql = "SELECT id, name, email, phone, address, city, state, postal_code, country, role, is_active, created_at 
                    FROM {$this->table} 
                    WHERE id = ?";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Get user by email
     */
    public function getUserByEmail($email) {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE email = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$email]);
            return $stmt->fetch();
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Create new user
     */
    public function createUser($data) {
        try {
            $sql = "INSERT INTO {$this->table} (name, email, password, phone, role, is_active) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([
                $data['name'],
                $data['email'],
                password_hash($data['password'], PASSWORD_BCRYPT),
                $data['phone'] ?? '',
                $data['role'] ?? 'user',
                true
            ]);

            return $result ? $this->db->lastInsertId() : false;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Update user
     */
    public function updateUser($id, $data) {
        try {
            $allowed_fields = ['name', 'email', 'phone', 'address', 'city', 'state', 'postal_code', 'country', 'is_active'];
            $updates = [];
            $values = [];

            foreach ($data as $key => $value) {
                if (in_array($key, $allowed_fields)) {
                    $updates[] = "$key = ?";
                    $values[] = $value;
                }
            }

            if (empty($updates)) return false;

            $values[] = $id;
            $sql = "UPDATE {$this->table} SET " . implode(', ', $updates) . " WHERE id = ?";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($values);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Delete user
     */
    public function deleteUser($id) {
        try {
            $sql = "DELETE FROM {$this->table} WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$id]);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Count total users
     */
    public function countUsers() {
        try {
            $sql = "SELECT COUNT(*) as count FROM {$this->table}";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch();
            return $result['count'] ?? 0;
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * Search users
     */
    public function searchUsers($keyword, $limit = 10, $offset = 0) {
        try {
            $sql = "SELECT id, name, email, phone, role, is_active, created_at 
                    FROM {$this->table} 
                    WHERE name LIKE ? OR email LIKE ? 
                    ORDER BY created_at DESC 
                    LIMIT ? OFFSET ?";
            
            $keyword = "%$keyword%";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$keyword, $keyword, $limit, $offset]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }
}