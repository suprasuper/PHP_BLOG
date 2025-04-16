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
<<<<<<< Updated upstream
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
=======
    
        // Vérifie chaque route définie pour ce type de requête
        foreach ($this->routes[$method] as $route => $callback) {
            // Convertit la route en regex
            $pattern = "#^" . $route . "$#";
    
            if (preg_match($pattern, $path, $matches)) {
                array_shift($matches); // enlève le match complet
    
                if (is_callable($callback)) {
                    call_user_func_array($callback, $matches);
                } elseif (is_string($callback)) {
                    [$controller, $method] = explode('@', $callback);
                    $controller = "Controllers\\" . $controller;
    
                    if (class_exists($controller) && method_exists($controller, $method)) {
                        $instance = new $controller();
                        call_user_func_array([$instance, $method], $matches);
                    } else {
                        http_response_code(500);
                        echo "Erreur : Contrôleur ou méthode introuvable.";
                    }
>>>>>>> Stashed changes
                }
                return;
            }
        }
<<<<<<< Updated upstream
=======
    
        // Si aucune route ne correspond
>>>>>>> Stashed changes
        http_response_code(404);
        echo "Erreur 404 - Page non trouvée";
    }
    
    
    
}
