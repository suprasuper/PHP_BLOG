<?php

namespace Controllers;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class HomeController {
    public function index() {
        $loader = new FilesystemLoader(__DIR__ . '/../../src/Views');
        $twig = new Environment($loader);
        $assets = require dirname(__DIR__, 2) . '/config/assets.php';
        $config = require dirname(__DIR__, 2) . '/config/env.php';

        $messageEnvoye = false;
        $erreur = null;

        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        if ($method === 'POST') {
            $nom = filter_input(INPUT_POST, 'nom', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $erreur = "Adresse email invalide.";
            } else {
                $mail = new PHPMailer(true);

                try {
                    // Configuration SMTP Gmail
                    $mail->isSMTP();
                    $mail->Host       = 'smtp.gmail.com';
                    $mail->SMTPAuth   = true;
                    $mail->Username   = 'thibaudiut@gmail.com';           
                    $mail->Password   = 'xvqa fhju tllf dsqk'; 
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port       = 587;

                    $mail->setFrom('thibaudiut@gmail.com', 'PHP_BLOG');
                    $mail->addAddress('thibaudiut@gmail.com');
                    $mail->addReplyTo($email, $nom);

                    $mail->isHTML(false);
                    $mail->Subject = 'Nouveau message depuis le site';
                    $mail->Body    = "Nom: $nom\nEmail: $email\n\nMessage:\n$message";

                    $mail->send();
                    $messageEnvoye = true;
                } catch (Exception $e) {
                    $erreur = "Erreur lors de l'envoi de l'e-mail : {$mail->ErrorInfo}";
                }
            }
        }

        echo $twig->render('home/acceuil.html.twig', [
            'titre' => 'Bienvenue sur mon blog',
            'page_title' => 'Accueil',
            'nom' => 'Thibaud',
            'prenom' => 'Dalbera',
            'welcome_message' => 'Bienvenu !',
            'user' => $_SESSION['user'] ?? null,
            'base_path' => htmlspecialchars($config['base_path']),
            'css_files' => $assets['css'],
            'js_files' => $assets['js'],
            'messageEnvoye' => $messageEnvoye,
            'erreur' => $erreur
        ]);
    }
}
