<?php
    class CommentController {
        public static function add(): void {
            session_start(); // eğer globalde yoksa
            if (!isset($_SESSION['user_id'])) {
                header('Location: /login'); exit;
            }

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405); echo 'Method Not Allowed'; exit;
            }

            $animeId = isset($_POST['anime_id']) ? (int)$_POST['anime_id'] : 0;
            $body    = trim($_POST['body'] ?? '');

            if ($animeId <= 0 || $body === '') {
                header("Location: /anime/{$animeId}"); exit;
            }

            require_once __DIR__ . '/../models/CommentModel.php';
            $model = new CommentModel();
            $model->create($_SESSION['user_id'], $animeId, $body);

            header("Location: /anime/{$animeId}#comments");
            exit;
        }
    }
