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
        $method = filter_input(INPUT_SERVER, 'REQUEST_METHOD', FILTER_SANITIZE_STRING);
        $allowedMethods = ['GET', 'POST', 'PUT', 'DELETE', 'PATCH'];
        if (!in_array($method, $allowedMethods)) {
            http_response_code(405); // Method Not Allowed
            echo "Méthode HTTP non autorisée.";
            return;
        }

        $rawUri = filter_input(INPUT_SERVER, 'REQUEST_URI', FILTER_SANITIZE_URL);
        $path = parse_url($rawUri, PHP_URL_PATH);
        $path = filter_var($path, FILTER_SANITIZE_URL);

        $basePath = '/PHP_BLOG/public';
        $path = str_replace($basePath, '', $path);

        // Recherche d'une route correspondante
        if (!isset($this->routes[$method])) {
            http_response_code(404);
            echo "Erreur 404 - Méthode non supportée.";
            return;
        }

        foreach ($this->routes[$method] as $route => $callback) {
            $pattern = "#^" . $route . "$#";

            if (preg_match($pattern, $path, $matches)) {
                array_shift($matches); // Retire le match complet

                // Fonction anonyme
                if (is_callable($callback)) {
                    call_user_func_array($callback, $matches);
                    return;
                }

                // Contrôleur@Méthode
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
        echo "Erreur 404 - Page non trouvée.";
    }

}
