<?php
namespace controllers;


use repository\CarRepository;

class ApiController extends AppController{

    public function api_getAllCars(){
        if(!$this->isPost()) {
            return null;
        }
        if($this->getContentType() === "application/json"){
            $carRepository = new CarRepository();
            $content = trim(file_get_contents("php://input")); // Get the raw POST data
            $decoded = json_decode($content, true); // Decode the JSON data

            header("Conent-Type: application/json");
            http_response_code(200);
            echo json_encode($carRepository->findAll());
        }
    }

    public function api_getCarsByAttributes() {
        if (!$this->isPost()) {
            return null;
        }
        if ($this->getContentType() === "application/json") {
            $carRepository = new CarRepository();
            $content = trim(file_get_contents("php://input")); // Get the raw POST data
            $attributes = json_decode($content, true); // Decode the JSON data

            $cars = $carRepository->findByAttributes($attributes);

            header("Content-Type: application/json");
            http_response_code(200);
            echo json_encode($cars);
        }
    }

    public function api_getAllCars_withDetails(){
        if(!$this->isPost()) {
            return null;
        }
        if($this->getContentType() === "application/json"){
            $carRepository = new CarRepository();
            $content = trim(file_get_contents("php://input")); // Get the raw POST data
            $decoded = json_decode($content, true); // Decode the JSON data

            header("Content-Type: application/json");
            http_response_code(200);
            echo json_encode($carRepository->findAllWithDetails());
        }
    }

    public function api_getCarById_withDetails()
    {
        if (!$this->isPost()) {
            return null;
        }
        if ($this->getContentType() === "application/json") {
            $carRepository = new CarRepository();
            $content = trim(file_get_contents("php://input")); // Get the raw POST data
            $decoded = json_decode($content, true); // Decode the JSON data

            if (!isset($decoded['car_id'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Car ID is required']);
                return;
            }

            $carId = $decoded['car_id'];
            $car = $carRepository->findByIdWithDetails($carId);

            header("Content-Type: application/json");
            http_response_code(200);
            echo json_encode($car);
        }
    }

    public function api_getAllCars_withModel()
    {
        if (!$this->isPost()) {
            return null;
        }
        if ($this->getContentType() === "application/json") {
            $carRepository = new CarRepository();
            $content = trim(file_get_contents("php://input")); // Get the raw POST data
            $decoded = json_decode($content, true); // Decode the JSON data

            header("Content-Type: application/json");
            http_response_code(200);
            echo json_encode($carRepository->findAllWithModel());
        }
    }

    public function api_deleteCar(){
        if(!$this->isPost()) {
            return null;
        }
        if($this->getContentType() === "application/json"){
            $carRepository = new CarRepository();
            $content = trim(file_get_contents("php://input")); // Get the raw POST data
            $decoded = json_decode($content, true); // Decode the JSON data

            if (!isset($decoded['car_id'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Car ID is required']);
                return;
            }

            $carId = $decoded['car_id'];
            $car = $carRepository->deepDelete($carId);

            header("Content-Type: application/json");
            http_response_code(200);
            echo json_encode($car);
        }
    }
}