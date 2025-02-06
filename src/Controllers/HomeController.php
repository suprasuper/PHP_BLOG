<?php

namespace App\Controllers;

use Core\Controller;

class HomeController extends Controller
{
    public function index()
    {
        $this->render('home/index', [
            'title' => 'Bienvenue sur mon blog',
            'message' => 'Découvrez mes articles et projets !'
        ]);
    }
}
