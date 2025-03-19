<?php
class ComponentLoader {
    public static function load($component, $params = []) {
        if($component === "header"){
            $params = array_merge(['title' => ''], $params);
            $params['title'] = $params['title'] ? $params['title'] . ' | Car Dealership' : 'Car Dealership';
        }
        extract($params);
        include __DIR__ . "/../components/{$component}.php";
    }
}