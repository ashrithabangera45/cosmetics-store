<?php
/**
 * Product Model
 */

class Product {
    private $db;
    private $table = 'products';

    public function __construct() {
        require_once __DIR__ . '/../config/Database.php';
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Get all active products
     */
    public function getAllProducts($limit = 10, $offset = 0) {
        try {
            $sql = "SELECT p.*, c.name as category_name 
                    FROM {$this->table} p
                    JOIN categories c ON p.category_id = c.id
                    WHERE p.is_active = 1
                    ORDER BY p.created_at DESC 
                    LIMIT ? OFFSET ?";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$limit, $offset]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Get products by category
     */
    public function getProductsByCategory($categoryId, $limit = 10, $offset = 0) {
        try {
            $sql = "SELECT p.*, c.name as category_name 
                    FROM {$this->table} p
                    JOIN categories c ON p.category_id = c.id
                    WHERE p.category_id = ? AND p.is_active = 1
                    ORDER BY p.created_at DESC 
                    LIMIT ? OFFSET ?";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$categoryId, $limit, $offset]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Get product by ID
     */
    public function getProductById($id) {
        try {
            $sql = "SELECT p.*, c.name as category_name 
                    FROM {$this->table} p
                    JOIN categories c ON p.category_id = c.id
                    WHERE p.id = ? AND p.is_active = 1";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Search products
     */
    public function searchProducts($keyword, $limit = 10, $offset = 0) {
        try {
            $sql = "SELECT p.*, c.name as category_name 
                    FROM {$this->table} p
                    JOIN categories c ON p.category_id = c.id
                    WHERE (p.name LIKE ? OR p.description LIKE ?) AND p.is_active = 1
                    ORDER BY p.created_at DESC 
                    LIMIT ? OFFSET ?";
            
            $keyword = "%$keyword%";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$keyword, $keyword, $limit, $offset]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Create product (admin only)
     */
    public function createProduct($data) {
        try {
            $sql = "INSERT INTO {$this->table} (category_id, name, description, price, quantity, image_url, sku, is_active) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([
                $data['category_id'],
                $data['name'],
                $data['description'] ?? null,
                $data['price'],
                $data['quantity'] ?? 0,
                $data['image_url'] ?? null,
                $data['sku'] ?? null,
                $data['is_active'] ?? 1
            ]);

            return $result ? $this->db->lastInsertId() : false;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Update product (admin only)
     */
    public function updateProduct($id, $data) {
        try {
            $allowed_fields = ['category_id', 'name', 'description', 'price', 'quantity', 'image_url', 'sku', 'is_active'];
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
     * Delete product (admin only)
     */
    public function deleteProduct($id) {
        try {
            $sql = "UPDATE {$this->table} SET is_active = 0 WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$id]);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Count total products
     */
    public function countProducts() {
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

    /**
     * Get featured products
     */
    public function getFeaturedProducts($limit = 6) {
        try {
            $sql = "SELECT p.*, c.name as category_name 
                    FROM {$this->table} p
                    JOIN categories c ON p.category_id = c.id
                    WHERE p.is_active = 1
                    ORDER BY p.created_at DESC 
                    LIMIT ?";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$limit]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }
}