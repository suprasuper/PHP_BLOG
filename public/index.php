<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Core\Router;
use Controllers\HomeController;
use Core\ErrorHandling\ErrorHandler;

// Activer le gestionnaire d'erreurs
ErrorHandler::register();

//Initialise router
$router = new Router();

// Ajouter la route pour la page d'accueil
$router->get('/', 'HomeController@index');

// Lancer le routeur
$router->dispatch();





