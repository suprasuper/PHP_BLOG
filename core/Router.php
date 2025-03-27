<?php

namespace Core;

class Router
{
    private $routes = [];

    public function get($path, $callback)
    {
        $this->routes['GET'][$path] = $callback;
    }

    public function post($path, $callback)
    {
        $this->routes['POST'][$path] = $callback;
    }

    public function dispatch()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = $_SERVER['REQUEST_URI'] ?? '/';

        // Vérifier si la route existe
        if (isset($this->routes[$method][$path])) {
            call_user_func($this->routes[$method][$path]); 
        } else {
            http_response_code(404);
            echo "404 - Page non trouvée";
        }
    }
}
 