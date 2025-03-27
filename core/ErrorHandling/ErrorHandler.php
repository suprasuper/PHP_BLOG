<?php

namespace Core\ErrorHandling;

class ErrorHandler {
    public static function register() {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        set_exception_handler([self::class, 'handleException']);
    }

    public static function handleException($exception) {
        echo "Une erreur est survenue : " . $exception->getMessage();
    }
}

