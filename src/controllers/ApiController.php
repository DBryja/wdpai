<?php
namespace controllers;


use repository\CarRepository;
use repository\UserRepository;

class ApiController extends AppController{

    private function verifySession(){
        $headers = getallheaders();
        if (!isset($headers['Admin-Email']) || !isset($headers['Session-Token'])) {
            http_response_code(403);
            echo json_encode(['error' => 'Forbidden - Session token is required']);
            exit;
        }

        $userRepository = new UserRepository();
        $user = $userRepository->getUser($headers['Admin-Email']);
        if($user === null){
            error_log("User not found: " . $headers['Admin-Email']);
            http_response_code(403);
            echo json_encode(['error' => 'Forbidden - User not found']);
            exit;
        }

        $sessionToken = $userRepository->getSessionToken($user->getId());
        if ($sessionToken !== $headers['Session-Token']) {
            error_log("Invalid session token for user: " . $headers['Admin-Email']);
            http_response_code(403);
            echo json_encode(['error' => 'Forbidden - Session token is invalid']);
            exit;
        }
    }
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
        $this->verifySession();
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

    public function api_getAllAdmins(){
        $this->verifySession();
        $userRepository = new UserRepository();
        $admins = $userRepository->getAllAdmins();

        header("Content-Type: application/json");
        http_response_code(200);
        echo json_encode($admins);
    }

    public function api_getAllUsers(){
        $this->verifySession();
        $userRepository = new UserRepository();
        $users = $userRepository->findAll();

        header("Content-Type: application/json");
        http_response_code(200);
        echo json_encode($users);
    }

    public function api_deleteUser(){
        if(!$this->isPost()) {
            return null;
        }
        $this->verifySession();
        if($this->getContentType() === "application/json"){
            $userRepository = new UserRepository();
            $content = trim(file_get_contents("php://input")); // Get the raw POST data
            $decoded = json_decode($content, true); // Decode the JSON data

            if (!isset($decoded['user_id'])) {
                http_response_code(400);
                echo json_encode(['error' => 'User ID is required']);
                return;
            }

            $userId = $decoded['user_id'];

            header("Content-Type: application/json");
            http_response_code(200);
            echo json_encode($userId);
        }

    }
}