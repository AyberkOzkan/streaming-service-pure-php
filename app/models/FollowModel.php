<?php 

    class FollowModel
    {
        private $db;

        public function __construct() {
            require_once __DIR__ . '/../config/db.php';
            $this->db = Database::connect();
        }

        public function addFollow($userId, $animeId, $animeTitle) {
            if (empty($userId) || empty($animeId) || empty($animeTitle)) {
                throw new Exception("User ID, anime ID, and title are required.");
            }

            $stmt = $this->db->prepare("INSERT INTO follows (user_id, anime_id, anime_title) VALUES (:user_id, :anime_id, :anime_title)");
            $stmt->execute([
                ':user_id' => $userId,
                ':anime_id' => $animeId,
                ':anime_title' => $animeTitle
            ]);
        }

        public function removeFollow($userId, $animeId) {
            if (empty($userId) || empty($animeId)) {
                throw new Exception("User ID and anime ID are required.");
            }

            $stmt = $this->db->prepare("DELETE FROM follows WHERE user_id = :user_id AND anime_id = :anime_id");
            $stmt->execute([
                ':user_id' => $userId,
                ':anime_id' => $animeId
            ]);
        }

        public function isFollowing($userId, $animeId): bool {
            $stmt = $this->db->prepare("SELECT 1 FROM follows WHERE user_id = :user_id AND anime_id = :anime_id LIMIT 1");
            $stmt->execute([
                ':user_id' => $userId,
                ':anime_id' => $animeId
            ]);
            return (bool) $stmt->fetchColumn();
        }

        public function listByUser(int $userId, int $limit = 24, int $offset = 0): array {
            $stmt = $this->db->prepare(
                "SELECT anime_id, anime_title, created_at
                FROM follows
                WHERE user_id = :uid
                ORDER BY created_at DESC
                LIMIT :lim OFFSET :off"
            );
            $stmt->bindValue(':uid', $userId, PDO::PARAM_INT);
            $stmt->bindValue(':lim', $limit,  PDO::PARAM_INT);
            $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        }

        public function countByUser(int $userId): int {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM follows WHERE user_id = :uid");
            $stmt->execute([':uid' => $userId]);
            return (int)$stmt->fetchColumn();
        }

        public function listFollowedAnimeIds(int $userId, int $limit, int $offset): array {
            $stmt = $this->db->prepare("SELECT anime_id FROM follows WHERE user_id = :u ORDER BY id DESC LIMIT :l OFFSET :o");
            $stmt->bindValue(':u', $userId, PDO::PARAM_INT);
            $stmt->bindValue(':l', $limit,  PDO::PARAM_INT);
            $stmt->bindValue(':o', $offset, PDO::PARAM_INT);
            $stmt->execute();
            return array_map('intval', array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'anime_id'));
        }
        
        public function countFollowed(int $userId): int {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM follows WHERE user_id = :u");
            $stmt->execute([':u' => $userId]);
            return (int)$stmt->fetchColumn();
        }

        public function countAll(): int {
            $stmt = $this->db->query("SELECT COUNT(*) FROM follows");
            return (int)$stmt->fetchColumn();
        }


    }

?>