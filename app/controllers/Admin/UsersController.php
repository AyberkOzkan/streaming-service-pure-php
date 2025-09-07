<?php

    require_once __DIR__ . '/../../core/helpers.php';
    require_once __DIR__ . '/../../models/UserModel.php';
    require_once __DIR__ . '/../../models/AdminModel.php';

    class Admin_UsersController
    {
        public function index(): void {
            // Access control: super admin only (helpers.php -> requireSuperAdmin uses AdminModel roles)
            requireSuperAdmin();

            $q = trim($_GET['q'] ?? '');
            $page = max(1, (int)($_GET['page'] ?? 1));
            $perPage = 20;

            $um = new UserModel();
            $users = $um->listPaginated($page, $perPage, $q ?: null);
            $total = $um->countFiltered($q ?: null);
            $pages = (int)ceil($total / $perPage);

            $_SESSION['csrf'] ??= bin2hex(random_bytes(16));
            $csrf = $_SESSION['csrf'];

            require_once __DIR__ . '/../../views/admin/layouts/header.php';
            require_once __DIR__ . '/../../views/admin/users/index.php';
            require_once __DIR__ . '/../../views/admin/layouts/footer.php';
        }

        public function ban(): void {
            requireSuperAdmin();

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405); echo 'Method Not Allowed'; return;
            }
            if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
                $_SESSION['flash_error'] = 'CSRF validation failed.';
                header('Location: /admin/users'); return;
            }

            $id = (int)($_POST['id'] ?? 0);
            $reason = trim($_POST['reason'] ?? '');
            if ($id <= 0) {
                $_SESSION['flash_error'] = 'Invalid user.';
                header('Location: /admin/users'); return;
            }

            $am = new AdminModel();
            if ($am->isSuperAdminByUserId($id)) {
                $_SESSION['flash_error'] = 'Super admin account cannot be banned.';
                header('Location: /admin/users'); return;
            }

            $um = new UserModel();
            $um->setBan($id, true, $reason ?: null);

            $_SESSION['flash_success'] = "User #$id banned.";
            header('Location: /admin/users');
        }


        public function unban(): void {
            requireSuperAdmin();

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405); echo 'Method Not Allowed'; return;
            }
            if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
                $_SESSION['flash_error'] = 'CSRF validation failed.';
                header('Location: /admin/users'); return;
            }

            $id = (int)($_POST['id'] ?? 0);
            if ($id <= 0) {
                $_SESSION['flash_error'] = 'Invalid user.';
                header('Location: /admin/users'); return;
            }

            $um = new UserModel();
            $um->setBan($id, false, null);

            $_SESSION['flash_success'] = "User #$id unbanned.";
            header('Location: /admin/users');
        }
    }
