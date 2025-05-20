<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Core\Router;
use Controllers\HomeController;
use Controllers\BlogController;
use Core\ErrorHandling\ErrorHandler;

//Demarrer la session
session_start();

// Activer le gestionnaire d'erreurs
ErrorHandler::register();

//Initialise router
$router = new Router();

// Ajouter la route pour la page d'accueil
$router->get('/', 'HomeController@index');
$router->get('/blog', 'BlogController@index');
$router->get('/article/(\d+)', 'BlogController@show');
$router->get('/article/(\d+)/delete', 'BlogController@deleteArticle');
$router->get('/article/(\d+)/update', 'BlogController@updateArticle');
$router->get('/login', 'AuthController@login');
$router->post('/login', 'AuthController@login');
$router->get('/logout', 'AuthController@logout');
$router->get('/register', 'AuthController@register');
$router->post('/register', 'AuthController@register');
$router->post('/article/(\d+)/comment', 'BlogController@addComment');
$router->post('/article/(\d+)/comment/(\d+)/delete', 'BlogController@deleteComment');
$router->post('/article/(\d+)/comment/(\d+)/update', 'BlogController@updateComment');




// Lancer le routeur
$router->dispatch();





