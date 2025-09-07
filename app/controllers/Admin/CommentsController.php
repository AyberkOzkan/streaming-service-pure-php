<?php

    require_once __DIR__ . '/../../core/helpers.php';
    require_once __DIR__ . '/../../models/CommentModel.php';

    class Admin_CommentsController
    {
        public function index(): void {
            requireAdmin(); // any active admin can access

            $q = trim($_GET['q'] ?? '');
            $page = max(1, (int)($_GET['page'] ?? 1));
            $perPage = 20;

            $cm = new CommentModel();
            $comments = $cm->listPaginatedAll($page, $perPage, $q ?: null);
            $total = $cm->countFilteredAll($q ?: null);
            $pages = (int)ceil($total / $perPage);

            $_SESSION['csrf'] ??= bin2hex(random_bytes(16));
            $csrf = $_SESSION['csrf'];

            require_once __DIR__ . '/../../views/admin/layouts/header.php';
            require_once __DIR__ . '/../../views/admin/comments/index.php';
            require_once __DIR__ . '/../../views/admin/layouts/footer.php';
        }

        public function delete(): void {
            requireAdmin();

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405); echo 'Method Not Allowed'; return;
            }
            if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
                $_SESSION['flash_error'] = 'CSRF validation failed.';
                header('Location: /admin/comments'); return;
            }

            $id = (int)($_POST['id'] ?? 0);
            if ($id <= 0) {
                $_SESSION['flash_error'] = 'Invalid comment.';
                header('Location: /admin/comments'); return;
            }

            $cm = new CommentModel();
            $ok = $cm->adminDelete($id);

            if ($ok) {
                $_SESSION['flash_success'] = 'Comment deleted.';
            } else {
                $_SESSION['flash_error'] = 'Failed to delete comment.';
            }
            header('Location: /admin/comments');
        }
    }
