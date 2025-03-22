<?php

namespace repository;

use PDO;

class ModelRepository extends Repository
{
    public function exists($model_id) {
        $query = "SELECT COUNT(*) FROM models WHERE id = :id";
        $stmt = $this->db->connect()->prepare($query);
        $stmt->execute([':id' => $model_id]);
        return $stmt->fetchColumn() > 0;
    }

    public function find($id)
    {
        $query = "SELECT * FROM models WHERE id = :id";
        $stmt = $this->db->connect()->prepare($query);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function findLike($text)
    {
        $query = "SELECT name FROM models WHERE name ILIKE :text";
        $stmt = $this->db->connect()->prepare($query);
        $stmt->execute([':text' => '%' . $text . '%']);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function create($name, $brand_id) {
        // Check if model already exists by name and brand
        $query = "SELECT id FROM models WHERE LOWER(name) = LOWER(:name) AND brand_id = :brand_id";
        $stmt = $this->db->connect()->prepare($query);
        $stmt->execute([
            ':name' => $name,
            ':brand_id' => $brand_id
        ]);
        $existingId = $stmt->fetchColumn();

        if ($existingId) {
            return $existingId;
        }

        // Create new model if it doesn't exist
        $query = "INSERT INTO models (name, brand_id, is_active) VALUES (:name, :brand_id, true) RETURNING id";
        try{
            $stmt = $this->db->connect()->prepare($query);
            $stmt->execute([
                ':name' => $name,
                ':brand_id' => $brand_id
            ]);
            return $stmt->fetchColumn(0);
        } catch (\PDOException $e) {
            return null;
        }
    }

    public function getAll() {
        $query = "SELECT m.*, b.name as brand_name 
              FROM models m 
              JOIN brands b ON m.brand_id = b.id 
              WHERE m.is_active = true 
              ORDER BY b.name, m.name";
        $stmt = $this->db->connect()->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getModelsByBrand($brand_id) {
        $query = "SELECT * FROM models WHERE brand_id = :brand_id AND is_active = true ORDER BY name";
        $stmt = $this->db->connect()->prepare($query);
        $stmt->execute([':brand_id' => $brand_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countByBrandId($brand_id)
    {
        $query = "SELECT COUNT(*) FROM models WHERE brand_id = :brand_id";
        $stmt = $this->db->connect()->prepare($query);
        $stmt->execute([':brand_id' => $brand_id]);
        return $stmt->fetchColumn();
    }

    public function update($id, $name, $brandId)
    {
        $query = "
        UPDATE models
        SET name = :name,
            brand_id = :brand_id
        WHERE id = :id
    ";

        try {
            $stmt = $this->db->connect()->prepare($query);
            $stmt->execute([
                ':name' => $name,
                ':brand_id' => $brandId,
                ':id' => $id,
            ]);
            return true;
        } catch (\PDOException $e) {
            return false;
        }
    }

    public function delete($id){
        $query = "UPDATE models SET is_active = false WHERE id = :id";
        $stmt = $this->db->connect()->prepare($query);
        $stmt->execute([':id' => $id]);
    }

    public function deepDelete($id){
        $query = "DELETE FROM models WHERE id = :id";
        $stmt = $this->db->connect()->prepare($query);
        $stmt->execute([':id' => $id]);
    }
}