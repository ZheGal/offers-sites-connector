<?php

namespace App\Classes;

class Router
{
    public $method;
    public $path;

    public function __construct()
    {
        $this->method = strtoupper($_SERVER['REQUEST_METHOD']);
        $this->path = trim($_SERVER['REQUEST_URI'], '\/ ');
    }

    public function get_routes()
    {
        $routes = [];
        $path = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'routes.php']);
        if (file_exists($path)) {
            $routes = require($path);
        }
        return $routes;
    }

    public function covergence()
    {
        $routes = $this->get_routes();

        return (isset($routes[$this->path]) && $routes[$this->path][0] == $this->method);
    }

    public function connect()
    {
        $routes = $this->get_routes();
        $route = $routes[$this->path];

        $ar = explode("@", $route[1]);
        $class = "\App\Classes\\{$ar[0]}";
        $action = lcfirst($ar[1]);
        
        if (method_exists($class, $action)) {
            $class::$action();
            die;
        }
    }
}