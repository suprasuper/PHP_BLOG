<?php

namespace Models;

use Core\Database;

class Post {
    public static function all() {
        $pdo = Database::getPDO();
        $stmt = $pdo->query("SELECT * FROM article ORDER BY date DESC");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function getRecent(int $limit = 5): array {
        $pdo = Database::getPDO();

        $sql = "SELECT id, titre, contenu, date, auteur
                FROM article
                ORDER BY date DESC
                LIMIT :limit";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function getPost(int $id): array {
        $pdo = Database::getPDO();

        $sql = "SELECT id, titre, contenu, date, auteur
                FROM article
                WHERE id = :id";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public static function create(string $titre, string $contenu): bool {
        $pdo = Database::getPDO();
    
        $sql = "INSERT INTO article (titre, contenu, date, auteur)
                VALUES (:titre, :contenu, NOW(), :auteur)";
    
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':titre', $titre);
        $stmt->bindValue(':contenu', $contenu);
        $stmt->bindValue(':auteur', $auteur);
    
        return $stmt->execute();
    }
    
}
