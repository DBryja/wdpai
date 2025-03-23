<?php
namespace controllers;


use repository\BrandRepository;
use repository\CarRepository;
use repository\ModelRepository;
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

    public function api_getCarsByAttributes()
    {
        if (!$this->isPost()) {
            return null;
        }
        if ($this->getContentType() === "application/json") {
            $content = trim(file_get_contents("php://input"));
            $decoded = json_decode($content, true);

            $carRepository = new CarRepository();
            $cars = $carRepository->findByAttributes($decoded);

            foreach ($cars as &$car) {
                $car['images'] = $carRepository->getCarImages($car['id']);
            }

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

    public function api_getBrandsLike()
    {
        if (!$this->isPost()) {
            return null;
        }
        if ($this->getContentType() === "application/json") {
            $content = trim(file_get_contents("php://input")); // Get the raw POST data
            $decoded = json_decode($content, true); // Decode the JSON data

            if (!isset($decoded['query'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Query parameter is required']);
                return;
            }

            $query = $decoded['query'];
            $brandsRepository = new BrandRepository();
            $brands = $brandsRepository->findLike($query);
            $brandNames = array_map(function($brand) {
                return $brand['name']."(".$brand['count'].")";
            }, $brands);

            header("Content-Type: application/json");
            http_response_code(200);
            echo json_encode($brandNames);
        }
    }
    public function api_getModelsLike()
    {
        if (!$this->isPost()) {
            return null;
        }
        if ($this->getContentType() === "application/json") {
            $content = trim(file_get_contents("php://input")); // Get the raw POST data
            $decoded = json_decode($content, true); // Decode the JSON data

            if (!isset($decoded['query'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Query parameter is required']);
                return;
            }

            $query = $decoded['query'];
            $brand = $decoded['brand'] ?? null;
            $modelsRepository = new ModelRepository();

            if ($brand) {
                $models = $modelsRepository->findLikeWithBrand($query, $brand);
            } else {
                $models = $modelsRepository->findLike($query);
            }

            $filteredModels = array_filter($models, function($model) {
                return $model['count'] > 0;
            });

            $modelNames = array_values(array_map(function($model) {
                return $model['name']."(".$model['count'].")";
            }, $filteredModels));

            header("Content-Type: application/json");
            http_response_code(200);
            echo json_encode($modelNames);
        }
    }
}