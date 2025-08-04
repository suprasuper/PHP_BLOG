<?php

namespace Controllers;
use Models\Comment;


use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Models\Post;

class BlogController
{
    //Verif admin
    private function requireAdmin()
    {
        if (empty($_SESSION['user']) || empty($_SESSION['user']['is_admin'])) {
            header('Location: /login');
            exit;
        }
    }

    //Creer article ( admin )
    public function create()
    {
        $this->requireAdmin();

        $loader = new FilesystemLoader(__DIR__ . '/../../src/Views');
        $twig = new Environment($loader);
        $assets = require dirname(__DIR__, 2) . '/config/assets.php';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titre = $_POST['titre'] ?? '';
            $chapo = $_POST['chapo'] ?? '';
            $contenu = $_POST['contenu'] ?? '';

            if (!empty($titre) && !empty($chapo) && !empty($contenu)) {
                $auteur = $_SESSION['user']['username'] ?? 'anonyme';
                Post::create($titre, $chapo, $contenu, $auteur);
                header('Location: /blog');
                exit;
            }

            $error = "Tous les champs sont obligatoires.";
        }
        echo $twig->render('posts/new.html.twig', [
            'titre' => 'Créer un article',
            'css_files' => $assets['css'],
            'js_files' => $assets['js'],
            'error' => $error ?? null
        ]);

    }

    //Afficher tous les articles
    public function index()
    {
        // Initialisation de Twig
        $config = require dirname(__DIR__, 2) . '/config/env.php';
        $loader = new FilesystemLoader(__DIR__ . '/../../src/Views'); // Définit le dossier des templates
        $twig = new Environment($loader);
        $assets = require dirname(__DIR__, 2) . '/config/assets.php';
        $articles = Post::all();


        // Rendu du template "home/acceuil.html.twig"
        echo $twig->render('posts/blog.html.twig', [
            'articles' => $articles,
            'error' => $error ?? null,
            'css_files' => $assets['css'],
            'js_files' => $assets['js'],
            'base_path' => $config['base_path'], // <= ajoute cette ligne
        ]);
    }

    //Afficher un seul article
    public function show($id)
    {
        $loader = new FilesystemLoader(__DIR__ . '/../../src/Views');
        $twig = new Environment($loader);
        $assets = require dirname(__DIR__, 2) . '/config/assets.php';
        $config = require dirname(__DIR__, 2) . '/config/env.php';
        $commentaires = Comment::allForPost($id);

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
            'article' => $article,
            'user' => $_SESSION["user"] ?? null,
            'base_path' => $config['base_path'],
            'commentaires' => $commentaires,
            'css_files' => $assets['css'],
            'js_files' => $assets['js']
        ]);
    }

    //Modifier un article
    public function updateArticle($id)
    {
        $this->requireAdmin();

        $loader = new FilesystemLoader(__DIR__ . '/../../src/Views');
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

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titre = $_POST['titre'] ?? '';
            $chapo = $_POST['chapo'] ?? '';
            $contenu = $_POST['contenu'] ?? '';


            if (!empty($titre) && !empty($chapo) && !empty($contenu)) {
                Post::update((int) $id, $titre, $chapo, $contenu);
                header("Location: {$config['base_path']}/article/{$id}");
                exit;
            }

            $error = "Tous les champs sont obligatoires.";
        }

        echo $twig->render('posts/edit.html.twig', [
            'article' => $article,
            'error' => $error ?? null,
            'css_files' => $assets['css'],
            'base_path' => $config['base_path'],
            'js_files' => $assets['js'],
        ]);
    }


    //Supprimer un article
    public function delete($id)
    {
        $this->requireAdmin();
        if (is_numeric($id)) {
            Post::delete((int) $id);
        }
        header('Location: /blog');
        exit;
    }

    //Ajouter un commentaire
    public function addComment($id)
    {
        $config = require dirname(__DIR__, 2) . '/config/env.php';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auteur = $_POST['auteur'] ?? '';
            $contenu = $_POST['contenu'] ?? '';

            if (!empty($auteur) && !empty($contenu)) {
                Comment::create((int) $id, $auteur, $contenu);
            }
        }

        header("Location: {$config['base_path']}/article/{$id}");


        exit;
    }

    //Supprimer un commentaire
    public function deleteComment($articleId, $commentId)
    {

        $this->requireAdmin();

        Comment::delete((int) $commentId);

        $config = require dirname(__DIR__, 2) . '/config/env.php';
        header("Location: {$config['base_path']}/article/{$articleId}");
        exit;
    }
}
