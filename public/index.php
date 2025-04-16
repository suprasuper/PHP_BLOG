<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Core\Router;
use Controllers\HomeController;
use Controllers\BlogController;
use Core\ErrorHandling\ErrorHandler;

// Activer le gestionnaire d'erreurs
ErrorHandler::register();

//Initialise router
$router = new Router();

// Ajouter la route pour la page d'accueil
$router->get('/', 'HomeController@index');
$router->get('/blog', 'BlogController@index');
$router->get('/article/(\d+)', 'BlogController@show');

// Lancer le routeur
$router->dispatch();





