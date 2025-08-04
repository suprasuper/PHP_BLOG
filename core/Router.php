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

        // Normalise l'URL en retirant /PHP_BLOG/public
        $basePath = '/PHP_BLOG/public';
        $path = str_replace($basePath, '', $path);

        // Recherche d'une route correspondante
        foreach ($this->routes[$method] as $route => $callback) {
            $pattern = "#^" . $route . "$#";

            if (preg_match($pattern, $path, $matches)) {
                array_shift($matches); // Enlève le match complet

                // Si c'est une fonction anonyme
                if (is_callable($callback)) {
                    call_user_func_array($callback, $matches);
                    return;
                }

                // Si c'est un contrôleur@méthode
                if (is_string($callback)) {
                    [$controller, $action] = explode('@', $callback);
                    $controller = "Controllers\\" . $controller;

                    if (class_exists($controller) && method_exists($controller, $action)) {
                        $instance = new $controller();
                        call_user_func_array([$instance, $action], $matches);
                        return; 
                    } else {
                        http_response_code(500);
                        echo "Erreur : Contrôleur ou méthode introuvable.";
                        return;
                    }
                }
            }
        }

        // Aucune route trouvée
        http_response_code(404);
        echo "Erreur 404 - Page non trouvée";
    }
}
