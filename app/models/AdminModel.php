<?php
    class AdminModel {
        public PDO $db;
        public function __construct() {
            require_once __DIR__ . '/../config/db.php';
            $this->db = Database::connect();
        }

        public function isAdmin(int $userId): bool {
            $st = $this->db->prepare("SELECT 1 FROM admins WHERE user_id = :uid AND status = 'active' LIMIT 1");
            $st->execute([':uid' => $userId]);
            return (bool)$st->fetchColumn();
        }

        public function countAll(): int {
            $stmt = $this->db->query("SELECT COUNT(*) FROM admins");
            return (int)$stmt->fetchColumn();
        }

        public function listAll(): array {
            $sql = "SELECT a.id, a.user_id, a.role, a.status, a.created_at, u.name, u.email
                    FROM admins a JOIN users u ON u.id = a.user_id
                    ORDER BY a.created_at DESC";
            return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC) ?: [];
        }

        public function existsByUserId(int $userId): bool {
            $st = $this->db->prepare("SELECT 1 FROM admins WHERE user_id = :uid");
            $st->execute([':uid' => $userId]);
            return (bool)$st->fetchColumn();
        }

        public function create(int $userId, string $role = 'editor', string $status = 'active'): bool {
            $st = $this->db->prepare("INSERT INTO admins (user_id, role, status) VALUES (:uid, :role, :status)");
            return $st->execute([':uid'=>$userId, ':role'=>$role, ':status'=>$status]);
        }

        public function delete(int $id): bool {
            $st = $this->db->prepare("DELETE FROM admins WHERE id = :id");
            return $st->execute([':id'=>$id]);
        }
        
        public function getRoleByUserId(int $userId): ?string {
            $stmt = $this->db->prepare("SELECT role FROM admins WHERE user_id = :uid LIMIT 1");
            $stmt->execute([':uid' => $userId]);
            $role = $stmt->fetchColumn();
            return $role !== false ? (string)$role : null;
        }

        public function getById(int $id): ?array {
            $stmt = $this->db->prepare("SELECT id, user_id, role, status FROM admins WHERE id = :id LIMIT 1");
            $stmt->execute([':id' => $id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ?: null;
        }

        private function normalizeRole(?string $role): string {
            $role = strtolower(trim((string)$role));
            return str_replace(['-', ' '], '_', $role);
        }

        public function isSuperAdminByUserId(int $userId): bool {
            $role = $this->getRoleByUserId($userId);
            $role = $this->normalizeRole($role);
            return in_array($role, ['superadmin', 'super_admin'], true);
        }

        public function isSuperAdminByAdminId(int $adminId): bool {
            $row = $this->getById($adminId);
            if (!$row) return false;
            $role = $this->normalizeRole($row['role'] ?? '');
            return in_array($role, ['superadmin', 'super_admin'], true);
        }

        /** helpful for hiding Ban/Delete UI for super admins */
        public function getSuperAdminUserIds(): array {
            $sql = "SELECT user_id FROM admins WHERE LOWER(role) IN ('superadmin','super_admin')";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_COLUMN, 0) ?: [];
        }

        /** OPTIONAL: block deleting the last super admin */
        public function countSuperAdmins(): int {
            $stmt = $this->db->query("SELECT COUNT(*) FROM admins WHERE LOWER(role) IN ('superadmin','super_admin')");
            return (int)$stmt->fetchColumn();
        }



    }
