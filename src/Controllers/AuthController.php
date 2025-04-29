<?php

namespace Controllers;

use Core\Controller;
use Models\User;

class AuthController extends Controller
{
    public function login()
    {
        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            $user = User::findByEmail($email);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'email' => $user['email']
                ];
                header('Location: /dashboard');
                exit;
            } else {
                $error = "Email ou mot de passe incorrect.";
            }
        }

        $this->render('auth/login.html.twig', [
            'error' => $error
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
    $error = null;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $confirmPassword = $_POST['confirm_password'];

        if ($password !== $confirmPassword) {
            $error = "Les mots de passe ne correspondent pas.";
        } else {
            $pdo = Database::getPDO();

            // Vérifier si l'email existe déjà
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
            $stmt->execute(['email' => $email]);

            if ($stmt->fetch()) {
                $error = "Un compte avec cet e-mail existe déjà.";
            } else {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (:name, :email, :password)");
                $stmt->execute([
                    'name' => $name,
                    'email' => $email,
                    'password' => $hashedPassword
                ]);

                header('Location: /login');
                exit;
            }
        }
    }

    $this->render('auth/register.html.twig', ['error' => $error]);
}

}
