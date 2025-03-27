<?php

namespace Core\ErrorHandling;

class Logger
{
    public static function logError($exception)
    {
        $logFile = __DIR__ . '/../../logs/error.log'; // Chemin du fichier de logs

        $message = "[" . date('Y-m-d H:i:s') . "] ";
        $message .= "Exception: " . $exception->getMessage();
        $message .= " in " . $exception->getFile() . " at line " . $exception->getLine() . "\n";
        $message .= "Stack trace:\n" . $exception->getTraceAsString() . "\n\n";

        file_put_contents($logFile, $message, FILE_APPEND);
    }
}
