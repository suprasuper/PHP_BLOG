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

    public static function create(string $email, string $password, string $nom) : bool {
        $pdo = Database::getPDO();
        
        $sql = "INSERT INTO users (email, password, nom, is_admin)
                VALUES (:email, :password, :nom, 0)";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':password', password_hash($password,PASSWORD_DEFAULT));
        $stmt->bindValue(':nom', $nom);

        return $stmt->execute();
    }
}