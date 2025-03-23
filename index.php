<?php
require_once __DIR__ . '/autoload.php';
require_once __DIR__ . '/Routing.php';

use utils\LoginSecurity;

$path = str_replace('/', '_', trim($_SERVER['REQUEST_URI'], '/'));
$path = parse_url($path, PHP_URL_PATH);

// Dość prymitywna, ale w miarę skuteczna metoda autoryzacji -
// wszystie ścieżki zaczynające się od /admin/ wymagają ustawionego $_SESSION['user_id']
// z wyjątkiem /adminLogin, który służy do logowania
// Konkretne ścieżki /api/ wymagają ustawionego nagłówka Admin-Email i Session-Token (implementacja w ApiController)
LoginSecurity::checkAdminAccess();

// Ścieżki /admin/ i /api/ rozróżniam:
// - Requesty wywoływane przez Fetch w JS lecą na /api/, a te z formularzy na /admin/
// Prawdopodobnie bym to teraz zrobił inaczej, bo jest taki misz-masz, ale działa :P

// Zaimplementowałem nested ścieżki zmieniając "/" na "_"
// w kontrolerach też nalezy mieć to na uwadze
// Ogólne widoki
Routing::get("index", "DefaultController");
Routing::get("dashboard", "DefaultController");
Routing::get("adminLogin", "DefaultController");
Routing::get("car", "DefaultController");

// Widoki admina
Routing::get("admin", "AdminController");
Routing::get("admin_users", "AdminController");
Routing::get("admin_cars", "AdminController");

// Formularze
Routing::post("adminLogin", "SecurityController");
Routing::post("logout", "SecurityController");
Routing::post("admin_addCar", "AdminController");
Routing::post("admin_updateCar", "AdminController");
Routing::post("admin_deleteCar", "AdminController");
Routing::post("admin_addUser", "AdminController");
Routing::post("admin_deleteUser", "AdminController");
Routing::post("admin_populateCars", "AdminController");


// API
Routing::POST("api_getAllCars", "ApiController");
Routing::POST("api_getAllCars_withModel", "ApiController");
Routing::POST("api_getCarsByAttributes", "ApiController");
Routing::POST("api_getAllCars_withDetails", "ApiController");
Routing::POST("api_getCarById_withDetails", "ApiController");
Routing::POST("api_deleteCar", "ApiController");
Routing::POST("api_getAllUsers", "ApiController");
Routing::POST("api_getBrandsLike", "ApiController");
Routing::POST("api_getModelsLike", "ApiController");


Routing::run($path);