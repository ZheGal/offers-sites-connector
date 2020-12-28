<?php

namespace App\Classes;

class Router
{
    public $method;
    public $path;

    public function __construct()
    {
        $this->method = strtoupper($_SERVER['REQUEST_METHOD']);
        $path = $this->get_path();
        $this->path = trim($path, '\/ ');
    }

    public function get_path()
    {
        $rand = md5(rand(1,999));
        $exp = explode("?", $_SERVER['REQUEST_URI']);
        $path = trim($exp[0], '/');
        $folder = get_root_folder();
        if (empty($folder)) {
            return $path;
        }
        $folder = $rand.'/'.$folder;
        $path = $rand.'/'.$path;
        $result = str_replace($folder, '', $path);
        return trim($result, '/');
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