<?php

    require_once __DIR__ . '/../../core/helpers.php';
    require_once __DIR__ . '/../../models/AdminModel.php';
    require_once __DIR__ . '/../../models/UserModel.php';


    class Admin_AdminsController
    {
        public function index(): void {
            requireAdmin();

            $am = new AdminModel();
            $admins = $am->listAll();

            require_once __DIR__ . '/../../views/admin/layouts/header.php';
            require_once __DIR__ . '/../../views/admin/admins/index.php';
            require_once __DIR__ . '/../../views/admin/layouts/footer.php';
        }

        public function create(): void {
            requireAdmin();

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405); echo 'Method Not Allowed'; return;
            }

            $email = trim($_POST['email'] ?? '');
            $role  = trim($_POST['role'] ?? 'editor');
            $status= trim($_POST['status'] ?? 'active');

            if ($email === '') {
                $_SESSION['flash_error'] = 'E-posta zorunlu.';
                header('Location: /admin/admins'); return;
            }

            $um = new UserModel();
            $user = $um->getByEmail($email);
            if (!$user) {
                $_SESSION['flash_error'] = 'Bu e-postaya ait kullanıcı bulunamadı.';
                header('Location: /admin/admins'); return;
            }

            $am = new AdminModel();
            if ($am->existsByUserId((int)$user['id'])) {
                $_SESSION['flash_error'] = 'Kullanıcı zaten admin.';
                header('Location: /admin/admins'); return;
            }

            if ($am->create((int)$user['id'], $role ?: 'editor', $status ?: 'active')) {
                $_SESSION['flash_success'] = 'Admin başarıyla eklendi.';
            } else {
                $_SESSION['flash_error'] = 'Admin eklenemedi.';
            }
            header('Location: /admin/admins');
        }

        public function delete(): void {
            requireAdmin();

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405); echo 'Method Not Allowed'; return;
            }
            $id = (int)($_POST['id'] ?? 0); // this is admins.id
            if ($id <= 0) { header('Location: /admin/admins'); return; }

            $am = new AdminModel();

            // Block deleting super admin
            // if ($am->isSuperAdminByAdminId($id)) {
            //     $_SESSION['flash_error'] = 'Super admin account cannot be deleted.';
            //     header('Location: /admin/admins'); return;
            // }

            // OPTIONAL: prevent deleting the last remaining super admin
            $row = $am->getById($id);
            if ($row && $am->countSuperAdmins() <= 1 && $am->isSuperAdminByAdminId($id)) {
                $_SESSION['flash_error'] = 'At least one super admin must remain.';
                header('Location: /admin/admins'); return;
            }

            $am->delete($id);
            $_SESSION['flash_success'] = 'Admin deleted.';
            header('Location: /admin/admins');
        }


    }
