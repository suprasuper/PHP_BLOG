<?php

namespace Controllers;

use Models\Comment;
use Models\Post;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class BlogController
{
    // Vérifie si l'utilisateur est admin
    private function requireAdmin()
    {
        $user = $_SESSION['user'] ?? null;
        $isAdmin = $user['is_admin'] ?? false;

        if (!$user || !$isAdmin) {
            header('Location: /login');
            exit;
        }
    }

    // Créer un article (admin)
    public function create()
    {
        $this->requireAdmin();

        $loader = new FilesystemLoader(__DIR__ . '/../../src/Views');
        $twig = new Environment($loader);
        $assets = require dirname(__DIR__, 2) . '/config/assets.php';

        $method = filter_input(INPUT_SERVER, 'REQUEST_METHOD', FILTER_SANITIZE_STRING);

        if ($method === 'POST') {
            $titre = filter_input(INPUT_POST, 'titre', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '';
            $chapo = filter_input(INPUT_POST, 'chapo', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '';
            $contenu = filter_input(INPUT_POST, 'contenu', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '';

            if (!empty($titre) && !empty($chapo) && !empty($contenu)) {
                $auteur = htmlspecialchars($_SESSION['user']['username'] ?? 'anonyme');
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

    // Afficher tous les articles
    public function index()
    {
        $config = require dirname(__DIR__, 2) . '/config/env.php';
        $loader = new FilesystemLoader(__DIR__ . '/../../src/Views');
        $twig = new Environment($loader);
        $assets = require dirname(__DIR__, 2) . '/config/assets.php';
        $articles = Post::all();

        echo $twig->render('posts/blog.html.twig', [
            'articles' => $articles,
            'error' => $error ?? null,
            'css_files' => $assets['css'],
            'js_files' => $assets['js'],
            'base_path' => $config['base_path'],
        ]);
    }

    // Afficher un seul article
    public function show($id)
    {
        $id = (int) filter_var($id, FILTER_SANITIZE_NUMBER_INT);
        $loader = new FilesystemLoader(__DIR__ . '/../../src/Views');
        $twig = new Environment($loader);
        $assets = require dirname(__DIR__, 2) . '/config/assets.php';
        $config = require dirname(__DIR__, 2) . '/config/env.php';
        $commentaires = Comment::allForPost($id);
        $article = Post::getPost($id);

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
            'user' => $_SESSION['user'] ?? null,
            'base_path' => $config['base_path'],
            'commentaires' => $commentaires,
            'css_files' => $assets['css'],
            'js_files' => $assets['js']
        ]);
    }

    // Modifier un article
    public function updateArticle($id)
    {
        $this->requireAdmin();

        $id = (int) filter_var($id, FILTER_SANITIZE_NUMBER_INT);
        $loader = new FilesystemLoader(__DIR__ . '/../../src/Views');
        $twig = new Environment($loader);
        $assets = require dirname(__DIR__, 2) . '/config/assets.php';
        $config = require dirname(__DIR__, 2) . '/config/env.php';

        $article = Post::getPost($id);

        if (!$article) {
            http_response_code(404);
            echo $twig->render('errors/404.html.twig', [
                'message' => 'Article introuvable.',
                'css_files' => $assets['css'],
                'js_files' => $assets['js'],
            ]);
            return;
        }

        $method = filter_input(INPUT_SERVER, 'REQUEST_METHOD', FILTER_SANITIZE_STRING);

        if ($method === 'POST') {
            $titre = filter_input(INPUT_POST, 'titre', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '';
            $chapo = filter_input(INPUT_POST, 'chapo', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '';
            $contenu = filter_input(INPUT_POST, 'contenu', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '';

            if (!empty($titre) && !empty($chapo) && !empty($contenu)) {
                Post::update($id, $titre, $chapo, $contenu);
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

    // Supprimer un article
    public function delete($id)
    {
        $this->requireAdmin();
        $id = (int) filter_var($id, FILTER_SANITIZE_NUMBER_INT);

        if ($id > 0) {
            Post::delete($id);
        }

        header('Location: /blog');
        exit;
    }

    // Ajouter un commentaire
    public function addComment($id)
    {
        $id = (int) filter_var($id, FILTER_SANITIZE_NUMBER_INT);
        $config = require dirname(__DIR__, 2) . '/config/env.php';

        $method = filter_input(INPUT_SERVER, 'REQUEST_METHOD', FILTER_SANITIZE_STRING);

        if ($method === 'POST') {
            $auteur = filter_input(INPUT_POST, 'auteur', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '';
            $contenu = filter_input(INPUT_POST, 'contenu', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '';

            if (!empty($auteur) && !empty($contenu)) {
                Comment::create($id, $auteur, $contenu);
            }
        }

        header("Location: {$config['base_path']}/article/{$id}");
        exit;
    }

    public function editComment($articleId, $commentId)
    {
        $this->requireAdmin();

        $articleId = (int) filter_var($articleId, FILTER_SANITIZE_NUMBER_INT);
        $commentId = (int) filter_var($commentId, FILTER_SANITIZE_NUMBER_INT);

        $loader = new FilesystemLoader(__DIR__ . '/../../src/Views');
        $twig = new Environment($loader);
        $assets = require dirname(__DIR__, 2) . '/config/assets.php';
        $config = require dirname(__DIR__, 2) . '/config/env.php';

        $comment = Comment::getById($commentId);

        if (!$comment) {
            http_response_code(404);
            echo $twig->render('errors/404.html.twig', [
                'message' => 'Commentaire introuvable.',
                'css_files' => $assets['css'],
                'js_files' => $assets['js'],
            ]);
            return;
        }

        $method = filter_input(INPUT_SERVER, 'REQUEST_METHOD', FILTER_SANITIZE_STRING);

        if ($method === 'POST') {
            $contenu = filter_input(INPUT_POST, 'contenu', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '';

            if (!empty($contenu)) {
                Comment::update($commentId, $contenu);
                header("Location: {$config['base_path']}/article/{$articleId}");
                exit;
            } else {
                $error = "Le contenu ne peut pas être vide.";
            }
        }

        echo $twig->render('posts/comment-edit.html.twig', [
            'comment' => $comment,
            'articleId' => $articleId,
            'base_path' => $config['base_path'],
            'error' => $error ?? null,
            'css_files' => $assets['css'],
            'js_files' => $assets['js'],
        ]);
    }

    // Supprimer un commentaire
    public function deleteComment($articleId, $commentId)
    {
        $this->requireAdmin();
        $commentId = (int) filter_var($commentId, FILTER_SANITIZE_NUMBER_INT);
        $articleId = (int) filter_var($articleId, FILTER_SANITIZE_NUMBER_INT);

        Comment::delete($commentId);

        $config = require dirname(__DIR__, 2) . '/config/env.php';
        header("Location: {$config['base_path']}/article/{$articleId}");
        exit;
    }
}
