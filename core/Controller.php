<?php

namespace Core; //pour pouvoir utiliser la classe Controller dans d'autre dossier (namespace)

use Twig\Environment; // Importe classe environnemlent de twig
use Twig\Loader\FilesystemLoader; // Importe classe FilesystemLoader de twig

class Controller
{
    protected $twig;

    public function __construct() 
    {
        $loader = new FilesystemLoader(__DIR__ . '/../src/Views');
        $this->twig = new Environment($loader, ['cache' => false]);
    }

    protected function render(string $view, array $params = [])
    {
        echo $this->twig->render($view, $params);
    }
}
