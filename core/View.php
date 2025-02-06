<?php

namespace Core;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class View
{
    private static $twig = null;

    public static function init()
    {
        if (self::$twig === null) {
            $loader = new FilesystemLoader(__DIR__ . '/../src/Views');
            self::$twig = new Environment($loader, ['cache' => false]);
        }
    }

    public static function render(string $template, array $data = [])
    {
        self::init();
        echo self::$twig->render($template, $data);
    }
}
