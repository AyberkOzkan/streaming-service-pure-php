<?php
    class UserModel {
        private $db;
        public function __construct() {
            require_once __DIR__ . '/../config/db.php';
            $this->db = Database::connect();
        }

        public function getById(int $id): ?array {
            $stmt = $this->db->prepare("SELECT id, email, name, password FROM users WHERE id = :id LIMIT 1");
            $stmt->execute([':id' => $id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ?: null;
        }

        public function getByEmail(string $email): ?array {
            $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
            $stmt->execute([':email' => $email]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ?: null;
        }

        public function updatePassword(int $id, string $hashed): void {
            $stmt = $this->db->prepare("UPDATE users SET password = :p WHERE id = :id");
            $stmt->execute([':p' => $hashed, ':id' => $id]);
        }

        public function updateProfile(int $id, string $name, string $email): void {
            $stmt = $this->db->prepare("UPDATE users SET name = :name, email = :email WHERE id = :id");
            $stmt->execute([':name' => $name, ':email' => $email, ':id' => $id]);
        }

        public function emailExists(string $email, int $exceptUserId): bool {
            $stmt = $this->db->prepare("SELECT 1 FROM users WHERE email = :email AND id <> :id LIMIT 1");
            $stmt->execute([':email' => $email, ':id' => $exceptUserId]);
            return (bool) $stmt->fetchColumn();
        }
        
        public function countAll(): int {
            $stmt = $this->db->query("SELECT COUNT(*) FROM users");
            return (int)$stmt->fetchColumn();
        }

        public function listPaginated(int $page=1, int $perPage=20, ?string $q=null): array {
            $offset = ($page - 1) * $perPage;
            $params = [];
            $where = '';

            if ($q) {
                $where = "WHERE (name ILIKE :q OR email ILIKE :q)"; // PG: ILIKE (case-insensitive)
                $params[':q'] = "%{$q}%";
            }

            $sql = "SELECT id, email, name, is_banned, banned_at, banned_reason, created_at, last_login_at
                    FROM users
                    $where
                    ORDER BY id DESC
                    LIMIT :limit OFFSET :offset";
            $stmt = $this->db->prepare($sql);
            foreach ($params as $k=>$v) $stmt->bindValue($k, $v, PDO::PARAM_STR);
            $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function countFiltered(?string $q=null): int {
            $params = [];
            $where = '';
            if ($q) {
                $where = "WHERE (name ILIKE :q OR email ILIKE :q)";
                $params[':q'] = "%{$q}%";
            }
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM users $where");
            $stmt->execute($params);
            return (int)$stmt->fetchColumn();
        }

        public function setBan(int $userId, bool $ban, ?string $reason=null): void {
            if ($ban) {
                // PG: BOOLEAN + NOW()
                $stmt = $this->db->prepare(
                "UPDATE users SET is_banned=TRUE, banned_at=NOW(), banned_reason=:r WHERE id=:id"
                );
                $stmt->execute([':r'=>$reason, ':id'=>$userId]);
            } else {
                $stmt = $this->db->prepare(
                "UPDATE users SET is_banned=FALSE, banned_at=NULL, banned_reason=NULL WHERE id=:id"
                );
                $stmt->execute([':id'=>$userId]);
            }
        }


    }
