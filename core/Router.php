<?php

namespace Core;

class Router {
    private $routes = [];
    public function get($path, $callback) {
        $this->routes['GET'][$path] = $callback;
    }
    public function post($path, $callback) {
        $this->routes['POST'][$path] = $callback;
    }
    public function dispatch() {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        // Supprimer "/PHP_BLOG/public" pour normaliser l'URL
        $basePath = '/PHP_BLOG/public';
        $path = str_replace($basePath, '', $path);
        // Vérifier si la route existe
        if (isset($this->routes[$method][$path])) {
            $callback = $this->routes[$method][$path];
            if (is_callable($callback)) {
                call_user_func($callback);
            } elseif (is_string($callback)) {
                [$controller, $method] = explode('@', $callback);
                $controller = "Controllers\\" . $controller;
                if (class_exists($controller) && method_exists($controller, $method)) {
                    $instance = new $controller();
                    $instance->$method();
                } else {
                    http_response_code(500);
                    echo "Erreur : Contrôleur ou méthode introuvable.";
                }
            }
            return;
        }
        http_response_code(404);
        echo "Erreur 404 - Page non trouvée";
    }
    
    
}
