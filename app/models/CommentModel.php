<?php
    class CommentModel {
        private $db;
        public function __construct() {
            require_once __DIR__ . '/../config/db.php';
            $this->db = Database::connect();
        }

        public function create(int $userId, int $animeId, string $body): void {
            $stmt = $this->db->prepare(
                "INSERT INTO comments (user_id, anime_id, body) VALUES (:user_id, :anime_id, :body)"
            );
            $stmt->execute([
                ':user_id'  => $userId,
                ':anime_id' => $animeId,
                ':body'     => $body,
            ]);
        }

        public function listByAnime(int $animeId, int $limit = 50, int $offset = 0): array {
            $stmt = $this->db->prepare(
                "SELECT c.*, u.name AS user_name
                FROM comments c
                JOIN users u ON u.id = c.user_id
                WHERE c.anime_id = :anime_id
                ORDER BY c.created_at DESC
                LIMIT :limit OFFSET :offset"
            );
            // integer paramlarını bindValue ile tipiyle verelim
            $stmt->bindValue(':anime_id', $animeId, PDO::PARAM_INT);
            $stmt->bindValue(':limit',    $limit,   PDO::PARAM_INT);
            $stmt->bindValue(':offset',   $offset,  PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        }

        public function delete(int $id, int $userId): void {
            // sadece sahibi silebilir belki admin kontrolü de eklenebilir
            $stmt = $this->db->prepare("DELETE FROM comments WHERE id = :id AND user_id = :user_id");
            $stmt->execute([':id' => $id, ':user_id' => $userId]);
        }
    }
