<?php 
    require_once __DIR__ . '/../config/db.php';
    class User {
        private $db;

        public function __construct() {
            $this->db = Database::connect();
        }

        public function createUser($name, $email, $password) {
            if (empty($name) || empty($email) || empty($password)) {
                throw new Exception("Name, email, and password are required.");
            }

            if ($this->getUserByEmail($email)) {
                throw new Exception("User already exists.");
            }

            $stmt = $this->db->prepare("
                INSERT INTO users (name, email, password) 
                VALUES (:name, :email, :password)
            ");

            $stmt->execute([
                ':name' => $name,
                ':email' => strtolower(trim($email)),
                ':password' => $password
            ]);
        }


        public function getUserByEmail($email) {
            $email = strtolower(trim($email));
            $email = filter_var($email, FILTER_SANITIZE_EMAIL);

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception("Invalid email format.");
            }

            $stmt = $this->db->prepare("
                SELECT id, name, email, password, is_banned, banned_reason
                FROM users
                WHERE LOWER(email) = :email
                LIMIT 1
            ");
            $stmt->execute([':email' => $email]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }


        public function getUserById($id) {
            $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id");
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        public function touchLastLoginAt(int $userId): void {
            $stmt = $this->db->prepare("UPDATE users SET last_login_at = NOW() WHERE id = :id");
            $stmt->execute([':id' => $userId]);
        }


    }
