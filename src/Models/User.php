<?php

namespace Models;

use Core\Database;

class User {
    public static function findByEmail(string $email): ?array {
        $pdo = Database::getPDO();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindValue(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $user ?: null;
    }
}