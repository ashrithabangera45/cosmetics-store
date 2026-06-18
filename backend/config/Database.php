<?php
/**
 * Database Class - PDO Connection Handler
 */

class Database {
    private $connection;
    private static $instance;

    /**
     * Singleton pattern - get database instance
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Private constructor to prevent direct instantiation
     */
    private function __construct() {
        try {
            $dsn = 'mysql:host=' . DB_HOST . ':' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4';
            
            $this->connection = new PDO(
                $dsn,
                DB_USER,
                DB_PASSWORD,
                array(
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                )
            );
        } catch (PDOException $e) {
            die(json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]));
        }
    }

    /**
     * Get database connection
     */
    public function getConnection() {
        return $this->connection;
    }

    /**
     * Execute query with prepared statements
     */
    public function query($sql, $params = array()) {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            throw new Exception('Query failed: ' . $e->getMessage());
        }
    }

    /**
     * Fetch single row
     */
    public function fetch($sql, $params = array()) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Fetch all rows
     */
    public function fetchAll($sql, $params = array()) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Insert data
     */
    public function insert($table, $data) {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        
        $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        $stmt = $this->query($sql, array_values($data));
        
        return $this->connection->lastInsertId();
    }

    /**
     * Update data
     */
    public function update($table, $data, $where) {
        $set = implode(', ', array_map(function($key) {
            return "$key = ?";
        }, array_keys($data)));
        
        $whereClause = implode(' AND ', array_map(function($key) {
            return "$key = ?";
        }, array_keys($where)));
        
        $sql = "UPDATE $table SET $set WHERE $whereClause";
        $params = array_merge(array_values($data), array_values($where));
        
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }

    /**
     * Delete data
     */
    public function delete($table, $where) {
        $whereClause = implode(' AND ', array_map(function($key) {
            return "$key = ?";
        }, array_keys($where)));
        
        $sql = "DELETE FROM $table WHERE $whereClause";
        $stmt = $this->query($sql, array_values($where));
        
        return $stmt->rowCount();
    }

    /**
     * Close connection
     */
    public function closeConnection() {
        $this->connection = null;
    }

    /**
     * Prevent cloning
     */
    private function __clone() {}

    /**
     * Prevent unserialize
     */
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}