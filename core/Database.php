<?php

namespace Core;

class Database {
    private static $pdo = null;

    public static function getPDO(): \PDO {
        if (self::$pdo === null) {
            $config = require dirname(__DIR__) . '/config/env.php';

            $dsn = "mysql:host={$config['db_host']};dbname={$config['db_name']};charset=utf8mb4";
            self::$pdo = new \PDO($dsn, $config['db_user'], $config['db_pass']);
            self::$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        }

        return self::$pdo;
    }
}
