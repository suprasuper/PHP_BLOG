<?php

namespace Controllers;

use Core\Controller;
use Models\User;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class AuthController extends Controller
{
    public function login()
    {
        $config = require dirname(__DIR__, 2) . '/config/env.php'; 
        $assets = require dirname(__DIR__, 2) . '/config/assets.php';
        $error = null;
        $email = "...";
        $password = '...';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            $user = User::findByEmail($email);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'email' => $user['email'],
                    'is_admin' => $user['is_admin'],
                    
                ];
                header('Location: /dashboard');
                exit;
            } else {
                $error = "Email ou mot de passe incorrect.";
            }
        }
        $this->render('connexion/login.html.twig', [
            'base_path' => $config['base_path'],
            'email' => $email,
            'password' => $password,
            'error' => $error,
            'css_files' => $assets['css'], // Envoie les CSS à Twig
            'js_files' => $assets['js'] // Envoie les JS à Twig
        ]);
    }

    public function logout()
    {
        unset($_SESSION['user']);
        session_destroy();
        header('Location: /login');
        exit;
    }

    public function register()
{
    $config = require dirname(__DIR__, 2) . '/config/env.php'; 
    $error = null;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $confirmPassword = $_POST['confirm_password'];

        if ($password !== $confirmPassword) {
            $error = "Les mots de passe ne correspondent pas.";
        } else {

            $result = \Models\User::create($email,$password,$name);
                if($result)
                {
                    header('Location: /PHP_BLOG/public/login');
                    exit;
                }   
         }
    }
    $this->render('connexion/register.html.twig', [
        'error' => $error,
        'base_path' => $config['base_path'],
        'css_files' => $assets['css'], // Envoie les CSS à Twig
        'js_files' => $assets['js'] // Envoie les JS à Twig
    ]);
    }
 
}


