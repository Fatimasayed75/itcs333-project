<?php

namespace Utils;

use PDO;
use PDOException;

/**
 * The Crud class provides basic CRUD operations for a database.
 * instead of using the PDO class directly you can use this class to perform CRUD operations.
 */

class Crud {
    private $db;

    public function __construct(PDO $pdo) {
        $this->db = $pdo;
    }

    // Create inserts a new record into the specified table.
    public function create(string $tableName, array $columns, array $values): bool {
        $placeholders = rtrim(str_repeat('?,', count($values)), ',');
        $columnList = implode(',', $columns);
        
        $query = "INSERT INTO $tableName ($columnList) VALUES ($placeholders)";
        $stmt = $this->db->prepare($query);

        return $stmt->execute($values);
    }

    // Read retrieves records from the specified table based on the given condition.
    public function read(string $tableName, array $columns = [], string $condition = '', ...$args): array {
        $columnList = empty($columns) ? '*' : implode(', ', $columns);
        $query = "SELECT $columnList FROM $tableName";

        if ($condition) {
            $query .= " WHERE $condition";
        }

        $stmt = $this->db->prepare($query);
        $stmt->execute($args);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Update modifies existing records in the specified table based on the given condition.
    public function update(string $tableName, array $updates, string $condition, ...$args): bool {
        $setClauses = [];
        $updateValues = [];

        foreach ($updates as $col => $val) {
            $setClauses[] = "$col = ?";
            $updateValues[] = $val;
        }

        $setClause = implode(',', $setClauses);
        $query = "UPDATE $tableName SET $setClause WHERE $condition";
        
        $stmt = $this->db->prepare($query);
        return $stmt->execute(array_merge($updateValues, $args));
    }

    // Delete removes records from the specified table based on the given condition.
    public function delete(string $tableName, string $condition, ...$args): bool {
        $query = "DELETE FROM $tableName WHERE $condition";
        $stmt = $this->db->prepare($query);
        return $stmt->execute($args);
    }
}