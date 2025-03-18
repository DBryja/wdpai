<?php

use controllers\DefaultController;
use controllers\SecurityController;

class Routing {
    public static $routes;

    public static function get($url, $controller){
        self::$routes[$url] = "controllers\\".$controller;
    }

    public static function post($url, $controller) {
        self::$routes[$url] = "controllers\\".$controller;
    }

    public static function run($url) {
        $action = explode("/", $url)[0];

        if(!array_key_exists($action, self::$routes)){
            die("Wrong url!");
        }

        $controller = self::$routes[$action];
        $object = new $controller;

        $object->$action();
    }
}