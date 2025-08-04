<?php

namespace Models;

use Core\Database;

class Post
{
    public static function all()
    {
        $pdo = Database::getPDO();
        $stmt = $pdo->query("SELECT * FROM article ORDER BY date DESC");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function getRecent(int $limit = 5): array
    {
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

    public static function getPost(int $id): array
    {
        $pdo = Database::getPDO();

        $sql = "SELECT id, titre, chapo, contenu, date, auteur
        FROM article
        WHERE id = :id";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public static function create(string $titre, string $chapo, string $contenu, string $auteur): bool
    {
        $pdo = Database::getPDO();

        $sql = "INSERT INTO article (titre, chapo, contenu, date, auteur)
                VALUES (:titre, :chapo, :contenu, NOW(), :auteur)";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':titre', $titre);
        $stmt->bindValue(':chapo', $chapo);
        $stmt->bindValue(':contenu', $contenu);

        return $stmt->execute();
    }


    public static function update(int $id, string $titre, string $chapo, string $contenu): bool
    {
        $pdo = Database::getPDO();

        $sql = "UPDATE article SET titre = :titre, chapo = :chapo, contenu = :contenu WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':titre', $titre);
        $stmt->bindValue(':chapo', $chapo);
        $stmt->bindValue(':contenu', $contenu);
        $stmt->bindValue(':id', $id, \PDO::PARAM_INT);

        return $stmt->execute();
    }



    public static function delete(int $id): bool
    {
        $pdo = Database::getPDO();

        $sql = "DELETE FROM article WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $id, \PDO::PARAM_INT);

        return $stmt->execute();
    }


}
