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

        public function listByUser(int $userId, int $limit = 5, int $offset = 0): array {
            $stmt = $this->db->prepare(
                "SELECT id, anime_id, user_id, body, created_at
                FROM comments
                WHERE user_id = :user_id
                ORDER BY created_at DESC
                LIMIT :limit OFFSET :offset"
            );
            $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindValue(':limit',   $limit,   PDO::PARAM_INT);
            $stmt->bindValue(':offset',  $offset,  PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        }

        public function countAll(): int {
            $stmt = $this->db->query("SELECT COUNT(*) FROM comments");
            return (int)$stmt->fetchColumn();
        }

        public function listPaginatedAll(int $page=1, int $perPage=20, ?string $q=null): array {
            $offset = ($page - 1) * $perPage;
            $params = [];
            $where = 'WHERE 1=1';

            if ($q) {
                // Search in user name, email, body
                $where .= " AND (u.name ILIKE :q OR u.email ILIKE :q OR c.body ILIKE :q)";
                $params[':q'] = "%{$q}%";
            }

            $sql = "
                SELECT
                    c.id, c.user_id, c.anime_id, c.body, c.created_at,
                    u.name AS user_name, u.email AS user_email
                FROM comments c
                JOIN users u ON u.id = c.user_id
                $where
                ORDER BY c.id DESC
                LIMIT :limit OFFSET :offset
            ";
            $stmt = $this->db->prepare($sql);
            foreach ($params as $k=>$v) $stmt->bindValue($k, $v, PDO::PARAM_STR);
            $stmt->bindValue(':limit',  $perPage, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset,  PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        }

        public function countFilteredAll(?string $q=null): int {
            $params = [];
            $where = 'WHERE 1=1';
            if ($q) {
                $where .= " AND (u.name ILIKE :q OR u.email ILIKE :q OR c.body ILIKE :q)";
                $params[':q'] = "%{$q}%";
            }
            $sql = "
                SELECT COUNT(*)
                FROM comments c
                JOIN users u ON u.id = c.user_id
                $where
            ";
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return (int)$stmt->fetchColumn();
        }

        /** Admin-level delete (no owner check) */
        public function adminDelete(int $id): bool {
            $stmt = $this->db->prepare("DELETE FROM comments WHERE id = :id");
            return $stmt->execute([':id' => $id]);
        }


    }
