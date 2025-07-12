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
                // Kullanıcı zaten varsa hata dönebilir
                throw new Exception("User already exists.");
            }
            $stmt = $this->db->prepare("INSERT INTO users (name, email, password) VALUES (:name, :email, :password)");
            $stmt->execute([
                ':name' => $name,
                ':email' => $email,
                ':password' => $password
            ]);
        }

        public function getUserByEmail($email) {
            $email = strtolower(trim($email));
            $email = filter_var($email, FILTER_SANITIZE_EMAIL);

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception("Invalid email format.");
            }
            
            $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email");
            $stmt->execute([':email' => $email]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        public function getUserById($id) {
            $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id");
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }


    }
