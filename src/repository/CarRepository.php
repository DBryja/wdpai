<?php

namespace repository;

use models\Car;

class CarRepository extends Repository
{
    public function create($data)
    {
        $modelId = $data['modelId'];
        $year = $data['year'];
        $price = $data['price'];
        $isNew = $data['isNew'];
        $priority = $data['priority'];
        $status = $data['status'];
        $isActive = $data['isActive'];

        $queryCar = "INSERT INTO cars (id, model_id, year, price, is_new, priority, status, is_active)
                 VALUES (gen_random_uuid(), :model_id, :year, :price, :is_new, :priority, :status, :is_active)
                 RETURNING id";

        try {
            $stmt = $this->db->connect()->prepare($queryCar);
            $stmt->execute([
                ':model_id' => $modelId,
                ':year' => $year,
                ':price' => $price,
                ':is_new' => $isNew,
                ':priority' => $priority,
                ':status' => $status,
                ':is_active' => $isActive,
            ]);

            return $stmt->fetchColumn();
        } catch (\PDOException $e) {
            return null;
        }
    }


    public function find($id)
    {
        // Implement the logic to find a Car by its ID
    }

    public function findAllWithModel(): array
    {
        $query = "
        SELECT
            cars.id,
            cars.year,
            cars.price,
            cars.status,
            cars.is_new,
            cars.is_active,
            models.name as model_name
        FROM
            cars
        JOIN
            models ON cars.model_id = models.id
    ";

        try {
            $stmt = $this->db->connect()->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }
    public function findByAttributes($attributes): array
    {
        $query = "SELECT * FROM cars WHERE 1=1";
        $params = [];

        foreach ($attributes as $key => $value) {
            $query .= " AND {$key} = :{$key}";
            $params[":{$key}"] = $value;
        }

        try {
            $stmt = $this->db->connect()->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }

    public function findAllWithDetails(): array
    {
        $query = "
        SELECT 
            cars.*, 
            car_details.*, 
            brands.name as brand_name, 
            models.name as model_name 
        FROM 
            cars 
        JOIN 
            car_details ON cars.id = car_details.car_id 
        JOIN 
            models ON cars.model_id = models.id 
        JOIN 
            brands ON models.brand_id = brands.id
    ";

        try {
            $stmt = $this->db->connect()->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }

    public function findAll(): array
    {
        $stmt = $this->db->connect()->prepare("SELECT * FROM cars");
        try {
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }

    public function update($data)
    {
        $query = "
        UPDATE cars
        SET model_id = :model_id,
            year = :year,
            price = :price,
            is_new = :is_new,
            priority = :priority,
            status = :status,
            is_active = :is_active
        WHERE id = :id
    ";

        try {
            $stmt = $this->db->connect()->prepare($query);
            $stmt->execute([
                ':model_id' => $data['modelId'],
                ':year' => $data['year'],
                ':price' => $data['price'],
                ':is_new' => $data['isNew'],
                ':priority' => $data['priority'],
                ':status' => $data['status'],
                ':is_active' => $data['isActive'],
                ':id' => $data['id'],
            ]);
            return true;
        } catch (\PDOException $e) {
            return false;
        }
    }

    public function findByIdWithDetails($carId): array
    {
        $query = "
        SELECT
            cars.*,
            car_details.*,
            brands.name as brand_name,
            models.name as model_name
        FROM
            cars
        JOIN
            car_details ON cars.id = car_details.car_id
        JOIN
            models ON cars.model_id = models.id
        JOIN
            brands ON models.brand_id = brands.id
        WHERE
            cars.id = :car_id
    ";

        try {
            $stmt = $this->db->connect()->prepare($query);
            $stmt->execute([':car_id' => $carId]);
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }

    public function delete($id){
        $query = "UPDATE cars SET is_active = false WHERE id = :id";
        try {
            $stmt = $this->db->connect()->prepare($query);
            $stmt->execute([':id' => $id]);
            return true;
        } catch (\PDOException $e) {
            return false;
        }
    }

    public function deepDelete($id)
    {
        $query = "DELETE FROM cars WHERE id = :id";
        try {
            $stmt = $this->db->connect()->prepare($query);
            $stmt->execute([':id' => $id]);

            // Delete the car's images folder
            $uploadDir = "public/uploads/cars/{$id}/";
            if (is_dir($uploadDir)) {
                $this->deleteDirectory($uploadDir);
            }

            return true;
        } catch (\PDOException $e) {
            return false;
        }
    }

    private function deleteDirectory($dir)
    {
        if (!file_exists($dir)) {
            return true;
        }
        if (!is_dir($dir)) {
            return unlink($dir);
        }
        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }
            if (!$this->deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }
        }
        return rmdir($dir);
    }
}