<?php 

    class AuthController {
        public function showRegisterForm() {
            require_once '../app/views/auth/register.php';
        }

        public function register() {
            // Validasyon, kullanıcı kaydı ve yönlendirme işlemleri
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $email = $_POST['email'] ?? '';
                $name = $_POST['name'] ?? '';
                $password = $_POST['password'] ?? '';

                if (empty($email) || empty($name) || empty($password)) {
                    // Hatalı giriş durumunda hata mesajı gösterilebilir
                    echo "All fields are required.";
                    return;
                }

                if ($name && $email && $password) {
                    require_once '../app/models/User.php';
                    $userModel = new User();
                    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
                    $userModel->createUser($name, $email, $hashedPassword);
                    // Kayıt başarılı ise oturum açma işlemi yapılabilir
                    header('Location: /login');
                    exit;
                } else {
                    // Hatalı giriş durumunda hata mesajı gösterilebilir
                    echo "Invalid input.";
                }
            } else {
                // Hatalı istek durumunda hata mesajı gösterilebilir
                echo "Invalid request method.";
            }
        }

        public function showLoginForm() {
            require_once '../app/views/auth/login.php';
        }

        public function login() {
            // Login işlemleri
        }

        public function logout() {
            // Session sil vs.
        }
    }
