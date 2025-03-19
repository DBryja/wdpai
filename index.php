<?php
require_once __DIR__ . '/autoload.php';
require_once __DIR__ . '/Routing.php';

use utils\LoginSecurity;

$path = trim($_SERVER['REQUEST_URI'], '/');
$path = parse_url($path, PHP_URL_PATH);

if ($path === '') {
    $path = 'index';
}

LoginSecurity::checkAdminAccess();

Routing::get("index", "DefaultController");
Routing::get("dashboard", "DefaultController");
Routing::get("adminLogin", "DefaultController");
Routing::get("admin", "DefaultController");
Routing::post("adminLogin", "SecurityController");
Routing::post("addProject", "ProjectController");
Routing::post("logout", "SecurityController");
Routing::run($path);