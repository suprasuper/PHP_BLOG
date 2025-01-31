<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Controllers\PostController;

// Charger la config des assets
$assets = require __DIR__ . '/../config/assets.php';

// Définir Twig
$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../src/Views');
$twig = new \Twig\Environment($loader, ['cache' => false]);

// Passer les CSS et JS à toutes les vues Twig
echo $twig->render('home.twig', [
    'css_files' => $assets['css'],
    
]);
