<?php

namespace Models;

use Core\Database;

class Comment {
    public static function create(int $postId, string $auteur, string $contenu): bool {
        $pdo = Database::getPDO();

        $sql = "INSERT INTO commentaire (article_id, auteur, contenu, date) VALUES (:post_id, :auteur, :contenu, NOW())";
        $stmt = $pdo->prepare($sql);

        return $stmt->execute([
            ':post_id' => $postId,
            ':auteur' => $auteur,
            ':contenu' => $contenu
        ]);
    }

    public static function allForPost(int $postId): array {
        $pdo = Database::getPDO();

        $sql = "SELECT * FROM commentaire WHERE article_id = :post_id ORDER BY date DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':post_id' => $postId]);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function delete(int $id): bool {
        $pdo = Database::getPDO();
    
        $sql = "DELETE FROM commentaire WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
    
        return $stmt->execute();
    }

    //récupérer un commentaire par son ID
    public static function getById(int $id): ?array {
        $pdo = Database::getPDO();

        $sql = "SELECT * FROM commentaire WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $comment = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $comment ?: null; // Retourne null si non trouvé
    }

    //mettre à jour le contenu d'un commentaire
    public static function update(int $id, string $contenu): bool {
        $pdo = Database::getPDO();

        $sql = "UPDATE commentaire SET contenu = :contenu WHERE id = :id";
        $stmt = $pdo->prepare($sql);

        return $stmt->execute([
            ':contenu' => $contenu,
            ':id' => $id
        ]);
    }
}
