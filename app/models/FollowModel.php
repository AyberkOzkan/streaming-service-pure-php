<?php 

    class FollowModel
    {
        private $db;

        public function __construct() {
            require_once __DIR__ . '/../config/db.php';
            $this->db = Database::connect();
        }

        public function addFollow(int $userId, int $animeId, string $animeTitle, string $source='mal'): bool {
                $st = $this->db->prepare("
                    INSERT INTO follows (user_id, anime_id, anime_title, source)
                    VALUES (:u,:a,:t,:s)
                    ON CONFLICT DO NOTHING
                ");
                return $st->execute([':u'=>$userId, ':a'=>$animeId, ':t'=>$animeTitle, ':s'=>$source]);
            }

            public function removeFollow(int $userId, int $animeId, string $source='mal'): bool {
                $st = $this->db->prepare("DELETE FROM follows WHERE user_id=:u AND anime_id=:a AND source=:s");
                return $st->execute([':u'=>$userId, ':a'=>$animeId, ':s'=>$source]);
            }


        public function isFollowing(int $userId, int $id, string $source='mal'): bool {
            $st = $this->db->prepare("SELECT 1 FROM follows WHERE user_id=:u AND anime_id=:a AND source=:s LIMIT 1");
            $st->execute([':u'=>$userId, ':a'=>$id, ':s'=>$source]);
            return (bool)$st->fetchColumn();
        }

        public function listByUser(int $userId, int $limit = 24, int $offset = 0): array {
            $stmt = $this->db->prepare(
                "SELECT anime_id, anime_title, source, created_at
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