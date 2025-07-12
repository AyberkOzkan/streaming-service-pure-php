<?php 

    class AuthController {
        public function showRegisterForm() {
            if (isset($_SESSION['user_id'])) {
                // Eğer kullanıcı zaten giriş yapmışsa, anasayfaya yönlendir
                header('Location: /');
                exit;
            }

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
            if (isset($_SESSION['user_id'])) {
                // Eğer kullanıcı zaten giriş yapmışsa, anasayfaya yönlendir
                header('Location: /');
                exit;
            }
            require_once '../app/views/auth/login.php';
        }

        public function login() {
            // Login işlemleri
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $email = $_POST['email'] ?? '';
                $password = $_POST['password'] ?? '';

                if (empty($email) || empty($password)) {
                    // Hatalı giriş durumunda hata mesajı gösterilebilir
                    echo "Email and password are required.";
                    return;
                }

                require_once '../app/models/User.php';
                $userModel = new User();
                $user = $userModel->getUserByEmail($email);

                if ($user && password_verify($password, $user['password'])) {
                    // Başarılı giriş işlemleri
                    session_start();
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['name'];
                    $_SESSION['user_email'] = $user['email'];
                    header('Location: /');
                    exit;
                } else {
                    // Hatalı giriş durumunda hata mesajı gösterilebilir
                    $_SESSION['login_error'] = "Email and password are required.";
                    header('Location: /login');
                    exit;
                }
            } else {
                // Hatalı istek durumunda hata mesajı gösterilebilir
                echo "Invalid request method.";
            }
        }

        public function logout() {
            // Session sil vs.
            session_start();
            session_unset();
            session_destroy();
            header('Location: /login');
            exit;
        }
    }
