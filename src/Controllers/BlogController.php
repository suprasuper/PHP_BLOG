<?php

namespace Controllers;


use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Models\Post;

class BlogController 

{
    
    public function index() {

       

        // Initialisation de Twig
        $loader = new FilesystemLoader(__DIR__ . '/../../Views'); // Définit le dossier des templates
        $twig = new Environment($loader);
        $assets = require dirname(__DIR__, 2) . '/config/assets.php';
        $articles = Post::all();


        // Rendu du template "home/acceuil.html.twig"
        echo $twig->render('posts/blog.html.twig', [
            'titre' => 'Articles',
            'articles' => $articles,
            'css_files' => $assets['css'], // Envoie les CSS à Twig
            'js_files' => $assets['js'] // Envoie les JS à Twig
        ]);
    }

    public function show($id) {
        $loader = new FilesystemLoader(__DIR__ . '/../../Views');
        $twig = new Environment($loader);
        $assets = require dirname(__DIR__, 2) . '/config/assets.php';
        $config = require dirname(__DIR__, 2) . '/config/env.php'; 

        $article = Post::getPost((int) $id);
    
        if (!$article) {
            http_response_code(404);
            echo $twig->render('errors/404.html.twig', [
                'message' => 'Article introuvable.',
                'css_files' => $assets['css'],
                'js_files' => $assets['js'],
            ]);
            return;
        }
    
        echo $twig->render('posts/show.html.twig', [
            'page_title' => $article['titre'],
            'article' => $article,
            'base_path' => $config['base_path'],
            'css_files' => $assets['css'],
            'js_files' => $assets['js']
        ]);
    }
    
}
