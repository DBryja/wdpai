<?php
//ZROBIĆ AUTOLOADER DO KLAS "use"
require_once __DIR__ . '/autoload.php';
require_once __DIR__ . '/Routing.php';

$path = trim($_SERVER['REQUEST_URI'], '/');
$path = parse_url($path, PHP_URL_PATH);

Routing::get("index", "DefaultController");
Routing::get("dashboard", "DefaultController");
Routing::post("login", "SecurityController");
Routing::run($path);
