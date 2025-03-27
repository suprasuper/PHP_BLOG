<?php

namespace Controllers;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class HomeController {
    public function index() {
        // Initialisation de Twig
        $loader = new FilesystemLoader(__DIR__ . '/../../Views'); // Définit le dossier des templates
        $twig = new Environment($loader);
        $assets = require dirname(__DIR__, 2) . '/config/assets.php';

        // Rendu du template "home/acceuil.html.twig"
        echo $twig->render('home/acceuil.html.twig', [
            'titre' => 'Bienvenue sur mon blog',
            'welcome_message' => 'Bienvenu !',
            'css_files' => $assets['css'], // Envoie les CSS à Twig
            'js_files' => $assets['js'] // Envoie les JS à Twig
        ]);
    }
}
