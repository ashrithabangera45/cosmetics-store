<?php
/**
 * Category Model
 */

class Category {
    private $db;
    private $table = 'categories';

    public function __construct() {
        require_once __DIR__ . '/../config/Database.php';
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Get all categories
     */
    public function getAllCategories() {
        try {
            $sql = "SELECT * FROM {$this->table} 
                    WHERE is_active = 1 
                    ORDER BY name ASC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Get category by ID
     */
    public function getCategoryById($id) {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE id = ? AND is_active = 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Create category (admin only)
     */
    public function createCategory($data) {
        try {
            $sql = "INSERT INTO {$this->table} (name, description, image_url, is_active) 
                    VALUES (?, ?, ?, ?)";
            
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([
                $data['name'],
                $data['description'] ?? null,
                $data['image_url'] ?? null,
                $data['is_active'] ?? 1
            ]);

            return $result ? $this->db->lastInsertId() : false;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Update category (admin only)
     */
    public function updateCategory($id, $data) {
        try {
            $allowed_fields = ['name', 'description', 'image_url', 'is_active'];
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
     * Delete category (admin only)
     */
    public function deleteCategory($id) {
        try {
            $sql = "UPDATE {$this->table} SET is_active = 0 WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$id]);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Count total categories
     */
    public function countCategories() {
        try {
            $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE is_active = 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch();
            return $result['count'] ?? 0;
        } catch (Exception $e) {
            return 0;
        }
    }
}