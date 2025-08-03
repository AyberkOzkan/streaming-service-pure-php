<?php 
    require_once __DIR__ . '/../models/FollowModel.php';

    class FollowController
    {
        public static function addFollow(): void
        {
            if (!isset($_SESSION['user_id']) || !isset($_POST['anime_id']) || !isset($_POST['anime_title'])) {
                header('Location: /login');
                exit;
            }

            $userId = $_SESSION['user_id'];
            $animeId = $_POST['anime_id'] ?? null;
            $animeTitle = $_POST['anime_title'] ?? null;

            if (!$animeId || !$animeTitle) {
                header('Location: /');
                exit;
            }

            $followModel = new FollowModel();
            $followModel->addFollow($userId, $animeId, $animeTitle);

            header('Location: /anime/' . urlencode($animeId));
            exit;
        }

        public static function removeFollow(): void
        {
            if (!isset($_SESSION['user_id']) || !isset($_POST['anime_id'])) {
                header('Location: /login');
                exit;
            }

            $userId = $_SESSION['user_id'];
            $animeId = $_POST['anime_id'] ?? null;

            if (!$animeId) {
                header('Location: /');
                exit;
            }

            $followModel = new FollowModel();
            $followModel->removeFollow($userId, $animeId);

            header('Location: /anime/' . urlencode($animeId));
            exit;
        }
    }