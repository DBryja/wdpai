<?php

namespace repository;

use PDO;

class BrandRepository extends Repository
{

    public function find($id)
    {
        $query = "SELECT * FROM brands WHERE id = :id";
        $stmt = $this->db->connect()->prepare($query);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function create($name) {
        // Check if brand already exists by name and return its ID if it does
        $query = "SELECT id FROM brands WHERE LOWER(name) = LOWER(:name)";
        $stmt = $this->db->connect()->prepare($query);
        $stmt->execute([':name' => $name]);
        $existingId = $stmt->fetchColumn();

        if ($existingId) {
            return $existingId;
        }

        // Create new brand if it doesn't exist
        $query = "INSERT INTO brands (name, is_active) VALUES (:name, true) RETURNING id";
        try {
            $stmt = $this->db->connect()->prepare($query);
            $stmt->execute([':name' => $name]);
            return $stmt->fetchColumn(0);
        } catch (\PDOException $e) {
            return null;
        }
    }

    public function update($id, $name)
    {
        $query = "
        UPDATE brands
        SET name = :name
        WHERE id = :id
    ";

        try {
            $stmt = $this->db->connect()->prepare($query);
            $stmt->execute([
                ':name' => $name,
                ':id' => $id,
            ]);
            return true;
        } catch (\PDOException $e) {
            return false;
        }
    }

    public function getAll() {
        $query = "SELECT * FROM brands WHERE is_active = true ORDER BY name";
        $stmt = $this->db->connect()->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function exists($brand_id) {
        $query = "SELECT COUNT(*) FROM brands WHERE id = :id";
        $stmt = $this->db->connect()->prepare($query);
        $stmt->execute([':id' => $brand_id]);
        return $stmt->fetchColumn() > 0;
    }
    public function delete($id) {
        $query = "UPDATE brands SET is_active = false WHERE id = :id";
        $stmt = $this->db->connect()->prepare($query);
        $stmt->execute([':id' => $id]);
    }

    public function deepDelete($id) {
        $query = "DELETE FROM brands WHERE id = :id";
        $stmt = $this->db->connect()->prepare($query);
        $stmt->execute([':id' => $id]);
    }
}