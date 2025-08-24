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
    }
