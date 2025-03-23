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
        $title = $data['title'];

        $queryCar = "INSERT INTO cars (id, model_id, year, price, is_new, priority, status, is_active, title)
                 VALUES (gen_random_uuid(), :model_id, :year, :price, :is_new, :priority, :status, :is_active, :title)
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
                ':title' => $title,
            ]);

            return $stmt->fetchColumn();
        } catch (\PDOException $e) {
            return null;
        }
    }

    public function find($id)
    {
        $query = "SELECT * FROM cars WHERE id = :id";
        $stmt = $this->db->connect()->prepare($query);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
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
    public function findByAttributes($attributes, $page = 1, $perPage = 9): array
    {
        $offset = ($page - 1) * $perPage;
        $query = "
    SELECT cars.*, brands.name as brand_name, models.name as model_name, car_details.mileage as mileage, car_details.horsepower as hp
    FROM cars
    JOIN models ON cars.model_id = models.id
    JOIN brands ON models.brand_id = brands.id
    JOIN car_details ON cars.id = car_details.car_id
    WHERE 1=1
    ";
        $params = [];

        if (!empty($attributes["isNew"])) {
            $query .= " AND cars.is_new = :is_new";
            $params[':is_new'] = true;
        }
        if (!empty($attributes['brand'])) {
            $query .= " AND brands.name ILIKE :brand";
            $params[':brand'] = '%' . $attributes['brand'] . '%';
        }
        if (!empty($attributes['model'])) {
            $query .= " AND models.name ILIKE :model";
            $params[':model'] = '%' . $attributes['model'] . '%';
        }
        if (!empty($attributes['price-min'])) {
            $query .= " AND cars.price >= :price_min";
            $params[':price_min'] = $attributes['price-min'];
        }
        if (!empty($attributes['price-max'])) {
            $query .= " AND cars.price <= :price_max";
            $params[':price_max'] = $attributes['price-max'];
        }
        if (!empty($attributes['year-min'])) {
            $query .= " AND cars.year >= :year_min";
            $params[':year_min'] = $attributes['year-min'];
        }
        if (!empty($attributes['year-max'])) {
            $query .= " AND cars.year <= :year_max";
            $params[':year_max'] = $attributes['year-max'];
        }
        if (!empty($attributes['sort'])) {
            switch ($attributes['sort']) {
                case 'price-asc':
                    $query .= " ORDER BY cars.price ASC";
                    break;
                case 'price-desc':
                    $query .= " ORDER BY cars.price DESC";
                    break;
                case 'year-asc':
                    $query .= " ORDER BY cars.year ASC";
                    break;
                case 'year-desc':
                    $query .= " ORDER BY cars.year DESC";
                    break;
                case 'mileage-asc':
                    $query .= " ORDER BY car_details.mileage ASC";
                    break;
                case 'mileage-desc':
                    $query .= " ORDER BY car_details.mileage DESC";
                    break;
                case 'power-asc':
                    $query .= " ORDER BY car_details.horsepower ASC";
                    break;
                case 'power-desc':
                    $query .= " ORDER BY car_details.horsepower DESC";
                    break;
            }
        } else {
            $query .= " ORDER BY cars.priority DESC";
        }

        $query .= " LIMIT :limit OFFSET :offset";
        $params[':limit'] = $perPage;
        $params[':offset'] = $offset;

        try {
            $stmt = $this->db->connect()->prepare($query);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value, is_int($value) ? \PDO::PARAM_INT : \PDO::PARAM_STR);
            }
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }

    public function findAllWithDetails($limit=null): array
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

    public function findAll($limit=null, $page=1): array
    {
        $query = "SELECT * FROM cars ORDER BY priority DESC";
        if ($limit !== null) {
            $query .= " LIMIT :limit OFFSET :offset";
        }

        $stmt = $this->db->connect()->prepare($query);

        try {
            if ($limit !== null) {
                $stmt->bindValue(':limit', (int) $limit, \PDO::PARAM_INT);
                $stmt->bindValue(':offset', (int) ($limit * ($page - 1)), \PDO::PARAM_INT);
            }
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
        is_active = :is_active,
        title = :title
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
                ':title' => $data['title'],
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
        car_details.color,
        car_details.description,
        car_details.engine_size,
        car_details.fuel_type,
        car_details.horsepower,
        car_details.mileage,
        car_details.transmission,
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

    public function countByModelId($id)
    {
        $query = "SELECT COUNT(*) FROM cars WHERE model_id = :id";
        $stmt = $this->db->connect()->prepare($query);
        $stmt->execute([':id' => $id]);
        return $stmt->fetchColumn();
    }

    public function countByBrandId($brand_id)
    {
        $query = "SELECT COUNT(*) FROM models WHERE brand_id = :brand_id";
        $stmt = $this->db->connect()->prepare($query);
        $stmt->execute([':brand_id' => $brand_id]);
        return $stmt->fetchColumn();
    }

    public function getCarImages($carId)
    {
        $uploadDir = __DIR__ . "/../../public/uploads/cars/{$carId}/";
        $images = [];

        if (is_dir($uploadDir)) {
            $files = scandir($uploadDir);
            foreach ($files as $file) {
                if (in_array(pathinfo($file, PATHINFO_EXTENSION), ['jpg', 'webp', 'png'])) {
                    $images[] = "/public/uploads/cars/{$carId}/{$file}";
                }
            }
        }
        return $images;
    }

    public function getCarThumbnail($carId)
    {
        $uploadDir = __DIR__ . "/../../public/uploads/cars/{$carId}/";
        $allowedExtensions = ['jpg', 'webp', 'png'];

        if (is_dir($uploadDir)) {
            $files = scandir($uploadDir);
            foreach ($files as $file) {
                if (in_array(pathinfo($file, PATHINFO_EXTENSION), $allowedExtensions)) {
                    return "/public/uploads/cars/{$carId}/{$file}";
                }
            }
        }

        return null;
    }


//    Funkcje do automatycznego wypełniania bazy danych
    public function populateCars($numCars)
    {
        $stockDir = __DIR__ . "/../../stock/";
        $carDirBase = __DIR__ . "/../../public/uploads/cars/";

        // Predefiniowane marki samochodów z ich modelami
        $carBrands = [
            'Toyota' => ['Corolla', 'Camry', 'RAV4', 'Yaris', 'Prius', 'Highlander', 'Land Cruiser'],
            'Volkswagen' => ['Golf', 'Passat', 'Tiguan', 'Polo', 'Arteon', 'ID.4', 'Touareg'],
            'Ford' => ['Focus', 'Fiesta', 'Mondeo', 'Mustang', 'Kuga', 'Puma', 'Explorer'],
            'BMW' => ['3 Series', '5 Series', 'X3', 'X5', '7 Series', 'i4', 'iX'],
            'Mercedes-Benz' => ['A-Class', 'C-Class', 'E-Class', 'S-Class', 'GLA', 'GLC', 'EQS'],
            'Audi' => ['A3', 'A4', 'A6', 'Q3', 'Q5', 'e-tron', 'TT'],
            'Honda' => ['Civic', 'Accord', 'CR-V', 'HR-V', 'Jazz', 'Pilot', 'e'],
            'Hyundai' => ['i30', 'Tucson', 'Kona', 'Santa Fe', 'i20', 'IONIQ', 'Elantra'],
            'Kia' => ['Ceed', 'Sportage', 'Sorento', 'Rio', 'Stonic', 'EV6', 'Niro'],
            'Skoda' => ['Octavia', 'Superb', 'Kodiaq', 'Karoq', 'Fabia', 'Scala', 'Enyaq']
        ];

        // Kolory samochodów (po angielsku)
        $colors = ['Black', 'White', 'Silver', 'Blue', 'Red', 'Gray', 'Green', 'Brown', 'Orange'];

        // Rodzaje paliwa (według opcji z formularza)
        $fuelTypes = ['Gasoline', 'Diesel', 'Electric', 'Hybrid', 'Other'];

        // Typy transmisji (według opcji z formularza)
        $transmissions = ['Manual', 'Automatic', 'CVT'];

        // Statusy (według opcji z formularza)
        $statuses = ['available', 'sold', 'reserved'];

        // Get all stock images
        $stockImages = array_filter(scandir($stockDir), function($file) use ($stockDir) {
            return in_array(pathinfo($file, PATHINFO_EXTENSION), ['jpg', 'webp', 'png']);
        });

        if (empty($stockImages)) {
            throw new \Exception("No stock images found in the stock directory.");
        }

        // Cache dla brandId, żeby nie tworzyć tych samych brandów wielokrotnie
        $brandCache = [];
        $modelCache = [];

        for ($i = 0; $i < $numCars; $i++) {
            // Wybierz losową markę
            $brand = array_keys($carBrands)[array_rand(array_keys($carBrands))];

            // Sprawdź czy marka już istnieje w cache
            if (!isset($brandCache[$brand])) {
                $brandCache[$brand] = $this->createRealBrand($brand);
            }
            $brandId = $brandCache[$brand];

            // Wybierz losowy model dla danej marki
            $modelName = $carBrands[$brand][array_rand($carBrands[$brand])];
            $cacheKey = $brandId . '_' . $modelName;

            if (!isset($modelCache[$cacheKey])) {
                $modelCache[$cacheKey] = $this->createRealModel($modelName, $brandId);
            }
            $modelId = $modelCache[$cacheKey];

            // Losowe dane dla samochodu
            $year = (int)rand(2010, 2023);
            $mileage = $year > 2020 ? (int)rand(0, 50000) : (int)rand(10000, 200000);
            $isNew = $year >= 2022 && $mileage < 10000 ? 1 : 0;

            // Losowy kolor
            $color = $colors[array_rand($colors)];

            // Losowy rodzaj paliwa
            $fuelType = $fuelTypes[array_rand($fuelTypes)];

            // Losna transmisja
            $transmission = $transmissions[array_rand($transmissions)];

            // Cena zależna od roku, marki i stanu
            $basePrice = rand(10000, 30000);
            $yearFactor = ($year - 2010) * 1000;
            $premiumBrands = ['BMW', 'Mercedes-Benz', 'Audi'];
            $brandFactor = in_array($brand, $premiumBrands) ? 15000 : 0;
            $newFactor = $isNew ? 10000 : 0;
            $price = $basePrice + $yearFactor + $brandFactor + $newFactor;

            // Rozmiar silnika i konie mechaniczne
            $engineSize = rand(10, 50) / 10; // 1.0 - 5.0 L (float)
            $horsepower = (int)($engineSize * rand(60, 100)); // Konwersja na integer

            // Generuj realistyczny tytuł
            $title = "$year $brand $modelName $color";

            // Losowy status z większym prawdopodobieństwem 'available'
            $randStatus = rand(1, 10);
            $status = $randStatus <= 7 ? 'available' : ($randStatus <= 9 ? 'reserved' : 'sold');

            // Losowy priorytet 1-5
            $priority = (int)rand(1, 5);

            // Generuj opis
            $description = "This $year $brand $modelName comes in a beautiful $color color. " .
                "It has a $engineSize L engine with $horsepower HP and $transmission transmission. " .
                "Fuel type: $fuelType. " .
                ($isNew ? "This is a brand new car in excellent condition." :
                    "This car has $mileage kilometers on it and is in good condition.");

            // Dane głównej tabeli cars
            $carData = [
                'modelId' => $modelId,
                'year' => $year,
                'price' => $price,
                'isNew' => $isNew,
                'priority' => $priority,
                'status' => $status,
                'isActive' => true,
                'title' => $title
            ];

            // Wstaw dane samochodu do bazy danych
            $carId = $this->create($carData);

            if ($carId) {
                // Dane tabeli car_details
                $carDetailsData = [
                    'carId' => $carId,
                    'mileage' => $mileage,
                    'fuel_type' => $fuelType,
                    'engine_size' => $engineSize,
                    'horsepower' => $horsepower,
                    'transmission' => $transmission,
                    'color' => $color,
                    'description' => $description
                ];

                // Wstaw dane szczegółowe do bazy danych
                $this->createCarDetails($carDetailsData);

                // Create car image directory
                $carDir = $carDirBase . $carId . '/';
                if (!is_dir($carDir)) {
                    mkdir($carDir, 0777, true);
                }

                // Copy a random stock image to the car's image directory
                $randomImage = $stockImages[array_rand($stockImages)];
                copy($stockDir . $randomImage, $carDir . $randomImage);
            }
        }
    }

    private function createRealBrand($brandName)
    {
        $brandRepository = new BrandRepository();
        return $brandRepository->create($brandName);
    }

    private function createRealModel($modelName, $brandId)
    {
        $modelRepository = new ModelRepository();
        return $modelRepository->create($modelName, $brandId);
    }

    private function createCarDetails($carDetailsData)
    {
        $query = "INSERT INTO car_details (car_id, mileage, fuel_type, engine_size, horsepower, transmission, color, description) 
          VALUES (:car_id, :mileage, :fuel_type, :engine_size, :horsepower, :transmission, :color, :description)";
        $stmt = $this->db->connect()->prepare($query);
        $stmt->execute([
            ':car_id' => $carDetailsData['carId'],
            ':mileage' => $carDetailsData['mileage'],
            ':fuel_type' => $carDetailsData['fuel_type'],
            ':engine_size' => $carDetailsData['engine_size'],
            ':horsepower' => $carDetailsData['horsepower'],
            ':transmission' => $carDetailsData['transmission'],
            ':color' => $carDetailsData['color'],
            ':description' => $carDetailsData['description']
        ]);
    }
}