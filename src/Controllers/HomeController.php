<?php

namespace Controllers;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class HomeController {
    public function index() {
        // Initialisation de Twig
        $loader = new FilesystemLoader(__DIR__ . '/../../src/Views'); // Dossier des templates
        $twig = new Environment($loader);
        $assets = require dirname(__DIR__, 2) . '/config/assets.php';
        $config = require dirname(__DIR__, 2) . '/config/env.php'; 

        $messageEnvoye = false;
        $erreur = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = htmlspecialchars($_POST['nom'] ?? '');
            $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
            $message = htmlspecialchars($_POST['message'] ?? '');

            $to = "thibaudiut@gmail.fr";
            $subject = "Nouveau message depuis le site";
            $body = "Nom: $nom\nEmail: $email\n\nMessage:\n$message";
            $headers = "From: $email\r\nReply-To: $email\r\n";

            if (mail($to, $subject, $body, $headers)) {
                $messageEnvoye = true;
            } else {
                $erreur = "Erreur lors de l'envoi de l'e-mail.";
            }
        }

        echo $twig->render('home/acceuil.html.twig', [
            'titre' => 'Bienvenue sur mon blog',
            'welcome_message' => 'Bienvenu !',
            'user' => $_SESSION["user"] ?? null,
            'base_path' => $config['base_path'],
            'css_files' => $assets['css'],
            'js_files' => $assets['js'],
            'messageEnvoye' => $messageEnvoye,
            'erreur' => $erreur
        ]);
    }
}
