<?php

namespace repository;

class CarDetailRepository extends Repository
{

    public function createCarDetails($data)
    {
        $carId = $data['carId'];
        $mileage = $data['mileage'];
        $fuelType = $data['fuel_type'];
        $engineSize = $data['engine_size'];
        $horsepower = $data['horsepower'];
        $transmission = $data['transmission'];
        $color = $data['color'];
        $description = $data['description'];

        $queryDetails = "INSERT INTO car_details (car_id, mileage, fuel_type, engine_size, horsepower, transmission, color, description)
                         VALUES (:car_id, :mileage, :fuel_type, :engine_size, :horsepower, :transmission, :color, :description)
                         RETURNING id";

        try {
            $stmt = $this->db->connect()->prepare($queryDetails);
            $stmt->execute([
                ':car_id' => $carId,
                ':mileage' => $mileage,
                ':fuel_type' => $fuelType,
                ':engine_size' => $engineSize,
                ':horsepower' => $horsepower,
                ':transmission' => $transmission,
                ':color' => $color,
                ':description' => $description
            ]);

            return $stmt->fetchColumn(0); // Ensure the column index is valid
        } catch (\PDOException $e) {
            return null;
        }
    }

    public function updateCarDetails($data)
    {
        $query = "
        UPDATE car_details
        SET mileage = :mileage,
            fuel_type = :fuel_type,
            engine_size = :engine_size,
            horsepower = :horsepower,
            transmission = :transmission,
            color = :color,
            description = :description
        WHERE car_id = :car_id
    ";

        try {
            $stmt = $this->db->connect()->prepare($query);
            $stmt->execute([
                ':mileage' => $data['mileage'],
                ':fuel_type' => $data['fuel_type'],
                ':engine_size' => $data['engine_size'],
                ':horsepower' => $data['horsepower'],
                ':transmission' => $data['transmission'],
                ':color' => $data['color'],
                ':description' => $data['description'],
                ':car_id' => $data['carId'],
            ]);
            return true;
        } catch (\PDOException $e) {
            return false;
        }
    }

    public function delete($id) {
        $query = "DELETE FROM car_details WHERE id = :id";
        try {
            $stmt = $this->db->connect()->prepare($query);
            return $stmt->execute([':id' => $id]);
        } catch (\PDOException $e) {
            return false;
        }
    }
}