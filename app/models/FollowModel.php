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
    }

?>