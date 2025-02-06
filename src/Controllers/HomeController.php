<?php

namespace Controllers;

use Core\View;

class HomeController
{
    public function index()
    {
        // Afficher la vue de la page d'accueil
        View::render('home/home.twig', [
            'page_title' => 'Accueil - Mon Blog',
            'welcome_message' => 'Bienvenue sur mon blog !',
        ]);
    }
}
