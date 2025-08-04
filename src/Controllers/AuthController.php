<?php

namespace Controllers;

use Core\Controller;
use Models\User;

class AuthController extends Controller
{
    public function login()
    {
        $config = require dirname(__DIR__, 2) . '/config/env.php'; 
        $assets = require dirname(__DIR__, 2) . '/config/assets.php';
        $error = null;

        $email = '';
        $password = '';

        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        if ($method === 'POST') {
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL) ?? '';
            $password = $_POST['password'] ?? '';

            $user = User::findByEmail($email);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user'] = [
                    'id' => (int) $user['id'],
                    'email' => $user['email'],
                    'is_admin' => (bool) $user['is_admin'],
                ];
                header('Location: http://localhost/PHP_BLOG/public/');
                exit;
            } else {
                $error = "Email ou mot de passe incorrect.";
            }
        }

        $this->render('connexion/login.html.twig', [
            'base_path' => $config['base_path'],
            'email' => $email,
            'password' => '', 
            'error' => $error,
            'css_files' => $assets['css'],
            'js_files' => $assets['js']
        ]);
    }

    public function logout()
    {
        $config = require dirname(__DIR__, 2) . '/config/env.php';
        unset($_SESSION['user']);
        session_destroy();
        header('Location: ' . $config['base_path']);
        exit;
    }

    public function register()
    {
        $config = require dirname(__DIR__, 2) . '/config/env.php'; 
        $error = null;
        $assets = require dirname(__DIR__, 2) . '/config/assets.php';

        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        if ($method === 'POST') {
            $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '';
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL) ?? '';
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            if ($password !== $confirmPassword) {
                $error = "Les mots de passe ne correspondent pas.";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = "Adresse email invalide.";
            } elseif (empty($name) || empty($password)) {
                $error = "Tous les champs sont obligatoires.";
            } else {
                $result = User::create($email, $password, $name);
                if ($result) {
                    header('Location: /PHP_BLOG/public/login');
                    exit;
                } else {
                    $error = "Erreur lors de la création du compte.";
                }
            }
        }

        $this->render('connexion/register.html.twig', [
            'error' => $error,
            'base_path' => $config['base_path'],
            'css_files' => $assets['css'],
            'js_files' => $assets['js']
        ]);
    }
}
