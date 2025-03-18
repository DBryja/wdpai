<?php

spl_autoload_register(function ($class) {
    $file = __DIR__ . '/src/' . str_replace('\\', '/', $class) . '.php';

    if (file_exists($file)) {
        require_once $file;
    } else {
        $file = __DIR__ . '/' . str_replace('\\', '/', $class) . '.php'; // Dodaj ładowanie z głównego katalogu
        if (file_exists($file)) {
            require_once $file;
        }
    }
});
