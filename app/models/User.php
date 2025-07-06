<?php 
    require_once __DIR__ . '/../config/db.php';
    class User {
        private $db;

        public function __construct() {
            $this->db = Database::connect();
        }

        public function createUser($name, $email, $password) {
            $stmt = $this->db->prepare("INSERT INTO users (name, email, password) VALUES (:name, :email, :password)");
            $stmt->execute([
                ':name' => $name,
                ':email' => $email,
                ':password' => $password
            ]);
        }
    }
