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
}
