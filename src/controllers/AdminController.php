<?php
namespace controllers;

use Database;
use models\Car;
use repository\CarRepository;
use repository\CarDetailRepository;
use repository\BrandRepository;
use repository\ModelRepository;
use repository\UserRepository;

class AdminController extends AppController {
    const MAX_FILE_SIZE = 1024 * 1024 * 10;
    const SUPPORTED_TYPES = ['image/png', 'image/jpg', 'image/jpeg', 'image/webp'];
    const UPLOAD_DIRECTORY = "/../public/uploads/";

    protected $messages = [];

    public function admin() {
        $this->render("admin");
    }

    public function admin_users() {
        $this->render("admin-users");
    }

    public function admin_cars() {
        $this->render("admin-cars");
    }

    public function admin_populateCars(){
        if (!$this->isPost()) {
            header("Location: /admin/cars");
        }

        $carRepository = new CarRepository();
        try {
            $carRepository->populateCars($_POST["count"]);
        } catch (\Exception $e) {
            $this->messages[] = $e->getMessage();
        }
        header("Location: /admin/cars");
    }
    public function admin_addCar() {
        if (!$this->isPost()) {
            header("Location: /admin/cars");
        }

        $brandsRepository = new BrandRepository();
        $modelsRepository = new ModelRepository();
        $carRepository = new CarRepository();
        $carDetailsRepository = new CarDetailRepository();

        if (!$this->validate_addCar($_POST)) {
            return $this->render("admin-cars", [
                'messages' => $this->messages,
            ]);
        }

        // Handle Brand selection/creation
        $brandName = $_POST['brand'];
        $brandId = $brandsRepository->create($brandName);
        if (!$brandId) {
            $this->messages[] = "Failed to create brand.";
            return $this->render("admin-cars", ['messages' => $this->messages]);
        }

        // Handle Model selection/creation
        $modelName = $_POST['model'];
        $modelId = $modelsRepository->create($modelName, $brandId);
        if (!$modelId) {
            $brandsRepository->deepDelete($brandId);
            $this->messages[] = "Failed to create model.";
            return $this->render("admin-cars", ['messages' => $this->messages]);
        }

        $isNew = isset($_POST['isNew']) ? 1 : 0;
        $isActive = isset($_POST['isActive']) ? 1 : 0;

        // Adding to `cars` table
        $carId = $carRepository->create([
            "title" => $_POST['title'],
            'modelId' => $modelId,
            'year' => $_POST['year'],
            'price' => $_POST['price'],
            'isNew' => $isNew,
            'priority' => $_POST['priority'],
            'status' => $_POST['status'],
            'isActive' => $isActive,
        ]);
        if (!$carId) {
            $modelsRepository->deepDelete($modelId);
            $brandsRepository->deepDelete($brandId);
            $this->messages[] = "Failed to create Car.";
            return $this->render("admin-cars", ['messages' => $this->messages]);
        }

        $uploadDir = "public/uploads/cars/{$carId}/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Adding to `car_details`
        $detailsId = $carDetailsRepository->createCarDetails([
            'carId' => $carId,
            'mileage' => $_POST['mileage'],
            'fuel_type' => $_POST['fuel_type'],
            'engine_size' => $_POST['engine_size'],
            'horsepower' => $_POST['horsepower'],
            'transmission' => $_POST['transmission'],
            'color' => $_POST['color'],
            'description' => $_POST['description'],
        ]);
        if (!$detailsId) {
            $carRepository->deepDelete($carId);
            $modelsRepository->deepDelete($modelId);
            $brandsRepository->deepDelete($brandId);
            rmdir($uploadDir);
            $this->messages[] = "Failed to create Car Details.";
            return $this->render("admin-cars", ['messages' => $this->messages]);
        }

        // Upload images
        $uploadedImages = $this->saveCarImages($_FILES['images'], $uploadDir);
        if (!$uploadedImages) {
            $carDetailsRepository->delete($detailsId);
            $carRepository->deepDelete($carId);
            $modelsRepository->deepDelete($modelId);
            $brandsRepository->deepDelete($brandId);
            rmdir($uploadDir);
            $this->messages[] = "Failed to save images.";
            return $this->render("admin-cars", ['messages' => $this->messages]);
        }

        header("Location: /admin/cars");
        return $this->render("admin-cars", ["messages" => ["Car added successfully!"]]);
    }

    public function admin_updateCar()
    {
        if (!$this->isPost()) {
            header("Location: /admin/cars");
        }

        $brandsRepository = new BrandRepository();
        $modelsRepository = new ModelRepository();
        $carRepository = new CarRepository();
        $carDetailsRepository = new CarDetailRepository();

        if (!$this->validate_addCar($_POST, true)) {
            return $this->render("admin-cars", [
                'messages' => $this->messages,
            ]);
        }

        // Fetch the current car details
        $currentCar = $carRepository->find($_POST['car_id']);
        $currentModel = $modelsRepository->find($currentCar['model_id']);
        $currentBrand = $brandsRepository->find($currentModel['brand_id']);

        // Handle Brand selection/creation
        $brandName = $_POST['brand'];
        $brandId = $brandsRepository->create($brandName);
        if (!$brandId) {
            $this->messages[] = "Failed to create brand.";
            return $this->render("admin-cars", ['messages' => $this->messages]);
        }

        // Handle Model selection/creation
        $modelName = $_POST['model'];
        $modelId = $modelsRepository->create($modelName, $brandId);
        if (!$modelId) {
            $brandsRepository->deepDelete($brandId);
            $this->messages[] = "Failed to create model.";
            return $this->render("admin-cars", ['messages' => $this->messages]);
        }

        $isNew = isset($_POST['isNew']) ? 1 : 0;
        $isActive = isset($_POST['isActive']) ? 1 : 0;

        // Updating `cars` table
        if (!$carRepository->update([
            'id' => $_POST['car_id'],
            "title" => $_POST['title'],
            'modelId' => $modelId,
            'year' => $_POST['year'],
            'price' => $_POST['price'],
            'isNew' => $isNew,
            'priority' => $_POST['priority'],
            'status' => $_POST['status'],
            'isActive' => $isActive,
        ])) {
            $this->messages[] = "Failed to update Car.";
            return $this->render("admin-cars", ['messages' => $this->messages]);
        }

        // Updating `car_details`
        if (!$carDetailsRepository->updateCarDetails([
            'carId' => $_POST['car_id'],
            'mileage' => $_POST['mileage'],
            'fuel_type' => $_POST['fuel_type'],
            'engine_size' => $_POST['engine_size'],
            'horsepower' => $_POST['horsepower'],
            'transmission' => $_POST['transmission'],
            'color' => $_POST['color'],
            'description' => $_POST['description'],
        ])) {
            $this->messages[] = "Failed to update Car Details.";
            return $this->render("admin-cars", ['messages' => $this->messages]);
        }

        // Check if the brand or model changed and delete the old ones if no other cars are using them
        if ($currentModel['name'] !== $modelName) {
            if ($carRepository->countByModelId($currentModel['id']) === 0) {
                $modelsRepository->deepDelete($currentModel['id']);
            }
        }

        if ($currentBrand['name'] !== $brandName) {
            if ($modelsRepository->countByBrandId($currentBrand['id']) === 0) {
                $brandsRepository->deepDelete($currentBrand['id']);
            }
        }

        header("Location: /admin/cars");
        return $this->render("admin-cars", ["messages" => ["Car updated successfully!"]]);
    }

    private function validate_addCar($data, $isUpdate = false) {
        $this->messages = []; // Reset messages

        if (!$isUpdate) {
            if (!$this->validate_car_images($_FILES['images'])) {
                return false;
            }
        }

        $required_fields = ['year', 'price', 'priority', 'status', 'brand', 'model', 'description'];

        foreach ($required_fields as $field) {
            if (!isset($data[$field]) || trim($data[$field]) === '') {
                $this->messages[] = "Field '{$field}' is required.";
            }
        }

        if (!empty($this->messages)) {
            return false;
        }

        // Validate numeric fields
        if (!filter_var($data['year'], FILTER_VALIDATE_INT, ["options" => ["min_range" => 1900, "max_range" => 2025]])) {
            $this->messages[] = "Invalid year.";
        }

        if (!filter_var($data['price'], FILTER_VALIDATE_FLOAT) || $data['price'] <= 0) {
            $this->messages[] = "Price must be a positive number.";
        }

        if (!filter_var($data['priority'], FILTER_VALIDATE_INT) || $data['priority'] < 0) {
            $this->messages[] = "Priority must be a non-negative integer.";
        }

        // Validate status
        $valid_statuses = ['available', 'sold', 'reserved'];
        if (!in_array($data['status'], $valid_statuses, true)) {
            $this->messages[] = "Invalid status.";
        }

        // Return validation result
        return empty($this->messages);
    }

    private function saveCarImages($files, $uploadDir) {
        $imagePaths = [];

        foreach ($files['tmp_name'] as $index => $tmpName) {
            $filename = basename($files['name'][$index]);
            $targetPath = $uploadDir . $filename;

            if (move_uploaded_file($tmpName, $targetPath)) {
                $imagePaths[] = $targetPath;
            }
        }

        return count($imagePaths) > 0 ? $imagePaths : false;
    }

    private function validate_car_images($images) {
        // First check if any files were uploaded
        if (empty($images) || empty($images['name'][0])) {
            $this->messages[] = "No images uploaded";
            return false;
        }

        // Now check each file
        for ($i = 0; $i < count($images['name']); $i++) {
            if ($images['size'][$i] > self::MAX_FILE_SIZE) {
                $this->messages[] = "File '{$images['name'][$i]}' is too large";
                return false;
            }
            if (!isset($images['type'][$i]) || !in_array($images['type'][$i], self::SUPPORTED_TYPES)) {
                $this->messages[] = "File type for '{$images['name'][$i]}' is not supported";
                return false;
            }
        }
        return true;
    }
}