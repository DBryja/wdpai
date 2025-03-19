<?php

namespace models;

class CarDetail {
    private $id;
    private $carId;
    private $mileage;
    private $fuelType;
    private $engineSize;
    private $horsepower;
    private $transmission;
    private $color;
    private $condition;

    public function __construct($id, $carId, $mileage, $fuelType, $engineSize, $horsepower, $transmission, $color, $condition) {
        $this->id = $id;
        $this->carId = $carId;
        $this->mileage = $mileage;
        $this->fuelType = $fuelType;
        $this->engineSize = $engineSize;
        $this->horsepower = $horsepower;
        $this->transmission = $transmission;
        $this->color = $color;
        $this->condition = $condition;
    }

    public function getId() {
        return $this->id;
    }

    public function getCarId() {
        return $this->carId;
    }

    public function getMileage() {
        return $this->mileage;
    }

    public function getFuelType() {
        return $this->fuelType;
    }

    public function getEngineSize() {
        return $this->engineSize;
    }

    public function getHorsepower() {
        return $this->horsepower;
    }

    public function getTransmission() {
        return $this->transmission;
    }

    public function getColor() {
        return $this->color;
    }

    public function getCondition() {
        return $this->condition;
    }
}