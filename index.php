<?php
require_once __DIR__ . '/autoload.php';
require_once __DIR__ . '/Routing.php';

use utils\LoginSecurity;

$path = str_replace('/', '_', trim($_SERVER['REQUEST_URI'], '/'));
$path = parse_url($path, PHP_URL_PATH);

if ($path === '') {
    $path = 'index';
}

LoginSecurity::checkAdminAccess();

// to get nested paths like admin/users change the "/" to "_"
// and add the method in the controller
Routing::get("index", "DefaultController");
Routing::get("dashboard", "DefaultController");
Routing::get("adminLogin", "DefaultController");

Routing::get("admin", "AdminController");
Routing::get("admin_users", "AdminController");
Routing::get("admin_cars", "AdminController");
Routing::get("admin_editCar", "AdminController");

Routing::post("adminLogin", "SecurityController");
Routing::post("logout", "SecurityController");
Routing::post("admin_addCar", "AdminController");
Routing::post("admin_updateCar", "AdminController");

//API
Routing::POST("api_getAllCars", "ApiController");
Routing::POST("api_getAllCars_withModel", "ApiController");
Routing::POST("api_getCarsByAttributes", "ApiController");
Routing::POST("api_getAllCars_withDetails", "ApiController");
Routing::POST("api_getCarById_withDetails", "ApiController");
Routing::POST("api_deleteCar", "ApiController");

Routing::run($path);