<?php 
    require_once __DIR__ . '/../models/FollowModel.php';

    class FollowController
    {
        public static function addFollow(): void {
            if (!isset($_SESSION['user_id'])) { header('Location:/login'); return; }

            $userId     = (int)$_SESSION['user_id'];
            $animeId    = (int)($_POST['anime_id'] ?? 0);
            $animeTitle = trim($_POST['anime_title'] ?? '');

            if (!$animeId || $animeTitle === '') { header('Location:/'); return; }

            $m = new FollowModel();
            $m->addFollow($userId, $animeId, $animeTitle, 'mal');

            header('Location: /anime/' . urlencode((string)$animeId));
        }

        public static function removeFollow(): void {
            if (!isset($_SESSION['user_id'])) { header('Location:/login'); return; }
            $userId  = (int)$_SESSION['user_id'];
            $animeId = (int)($_POST['anime_id'] ?? 0);

            $m = new FollowModel();
            $m->removeFollow($userId, $animeId, 'mal');

            header('Location: /anime/' . urlencode((string)$animeId));
        }

        // Local tabanlı
        public static function addLocal(): void {
            if (!isset($_SESSION['user_id'])) { header('Location:/login'); return; }

            $userId     = (int)$_SESSION['user_id'];
            $animeId    = (int)($_POST['anime_id'] ?? 0);    // animes.id
            $animeTitle = trim($_POST['anime_title'] ?? '');

            if (!$animeId || $animeTitle === '') { header('Location:/'); return; }

            $m = new FollowModel();
            $m->addFollow($userId, $animeId, $animeTitle, 'local');

            header("Location: /local/anime/{$animeId}");
        }

        public static function removeLocal(): void {
            if (!isset($_SESSION['user_id'])) { header('Location:/login'); return; }

            $userId  = (int)$_SESSION['user_id'];
            $animeId = (int)($_POST['anime_id'] ?? 0);

            $m = new FollowModel();
            $m->removeFollow($userId, $animeId, 'local');

            header("Location: /local/anime/{$animeId}");
        }
    }