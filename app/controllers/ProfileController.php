<?php

require_once __DIR__ . '/../models/FollowModel.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/CommentModel.php';

class ProfileController
{
    public function index(): void {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login'); exit;
        }

        $userId = (int)$_SESSION['user_id'];
        $userModel = new UserModel();
        $user = $userModel->getById($userId);
        $limit = 24;
        $page  = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $offset = ($page - 1) * $limit;

        $followModel = new FollowModel();
        $total = $followModel->countByUser($userId);
        $lastPage = max(1, (int)ceil($total / $limit));

        // Kullanıcının takip ettiği anime’ler (sadece id/title alıyoruz)
        $follows = $followModel->listByUser($userId, $limit, $offset);
        $baseUrl = getenv('JIKAN_API_URL') ?: 'https://api.jikan.moe/v4';
        $animeList = [];

        foreach ($follows as $row) {
            $malId = (int)$row['anime_id'];
            $json  = cachedGet("{$baseUrl}/anime/{$malId}");
            $data  = $json ? json_decode($json, true) : null;
            $a     = $data['data'] ?? null;

            if ($a) {
                $animeList[] = [
                    'mal_id'  => $a['mal_id'],
                    'title'   => $a['title'],
                    'images'  => $a['images'], // jpg/webp altları var
                    'episodes'=> $a['episodes'] ?? '?',
                    'score'   => $a['score'] ?? 'N/A',
                    'members' => $a['members'] ?? 0,
                    'type'    => $a['type'] ?? 'Unknown',
                    'status'  => $a['status'] ?? 'Unknown',
                ];
            } else {
                // API dönmezse en azından başlık/id göster
                $animeList[] = [
                    'mal_id'  => $malId,
                    'title'   => $row['anime_title'],
                    'images'  => ['jpg' => ['large_image_url' => '/img/placeholder.jpg']],
                    'episodes'=> '?',
                    'score'   => 'N/A',
                    'members' => 0,
                    'type'    => 'Unknown',
                    'status'  => 'Unknown',
                ];
            }
        }

        $categoryTitle = 'Profile';
        $basePath = '/profile';
        $currentPage = $page;
        $commentModel = new CommentModel();
        $recentComments = $commentModel->listByUser($userId, 5, 0);
        $titles = [];
        $uniqAnimeIds = array_values(array_unique(array_map(fn($c)=> (int)$c['anime_id'], $recentComments)));

        foreach ($uniqAnimeIds as $aid) {
            $json = cachedGet("{$baseUrl}/anime/{$aid}");
            if ($json) {
                $d = json_decode($json, true);
                $titles[$aid] = $d['data']['title'] ?? "Anime #{$aid}";
            } else {
                $titles[$aid] = "Anime #{$aid}";
            }
        }

        require_once __DIR__ . '/../views/profile/profile.php';
    }

    public function changePassword(): void {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login'); exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405); echo 'Method Not Allowed'; exit;
        }

        $userId = (int)$_SESSION['user_id'];
        $current = trim($_POST['current_password'] ?? '');
        $new     = trim($_POST['new_password'] ?? '');
        $confirm = trim($_POST['confirm_password'] ?? '');

        if ($new === '' || $new !== $confirm) {
            $_SESSION['flash_error'] = 'Yeni şifre doğrulaması başarısız.';
            header('Location: /profile'); exit;
        }

        $userModel = new UserModel();
        $user = $userModel->getById($userId);
        if (!$user) {
            $_SESSION['flash_error'] = 'Kullanıcı bulunamadı.';
            header('Location: /profile'); exit;
        }

        if (!password_verify($current, $user['password'])) {
            $_SESSION['flash_error'] = 'Mevcut şifre hatalı.';
            header('Location: /profile'); exit;
        }

        $hashed = password_hash($new, PASSWORD_DEFAULT);
        $userModel->updatePassword($userId, $hashed);

        $_SESSION['flash_success'] = 'Şifre başarıyla güncellendi.';
        header('Location: /profile'); exit;
    }
    
    public function updateProfile(): void {
        if (!isset($_SESSION['user_id'])) { header('Location: /login'); exit; }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo 'Method Not Allowed'; exit; }

        $userId = (int)$_SESSION['user_id'];
        $name   = trim($_POST['name']  ?? '');
        $email  = trim($_POST['email'] ?? '');

        if ($name === '' || $email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['flash_error'] = 'Lütfen geçerli ad ve e-posta giriniz.';
            header('Location: /profile'); exit;
        }

        $userModel = new UserModel();
        if ($userModel->emailExists($email, $userId)) {
            $_SESSION['flash_error'] = 'Bu e-posta başka bir hesap tarafından kullanılıyor.';
            header('Location: /profile'); exit;
        }

        $userModel->updateProfile($userId, $name, $email);

        // header’da/oturumda görünen ad/e-posta güncel kalsın
        $_SESSION['user_name']  = $name;
        $_SESSION['user_email'] = $email;

        $_SESSION['flash_success'] = 'Profil bilgilerin güncellendi.';
        header('Location: /profile'); exit;
    }
    
}
