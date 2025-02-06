<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Core\Router;
use Controllers\HomeController;
use Core\ErrorHandling\ErrorHandler;

// Activer le gestionnaire d'erreurs
ErrorHandler::register();

// Initialiser le router
$router = new Router();

// Définir la route pour la page d'accueil
$router->get('/', function () {
    $controller = new HomeController();
    $controller->index(); // Affiche la page d'accueil
});

// Lancer le dispatching des routes
$router->dispatch();
