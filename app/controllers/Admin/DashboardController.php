<?php
    require_once __DIR__ . '/../../core/helpers.php';
    require_once __DIR__ . '/../../models/UserModel.php';
    require_once __DIR__ . '/../../models/CommentModel.php';
    require_once __DIR__ . '/../../models/FollowModel.php';
    require_once __DIR__ . '/../../models/AdminModel.php';

    class Admin_DashboardController
    {
        public function index(): void {
            requireAdmin();

            $u = new UserModel();
            $c = new CommentModel();
            $f = new FollowModel();
            $a = new AdminModel();

            $stats = [
                'users'    => method_exists($u,'countAll') ? $u->countAll() : 0,
                'comments' => method_exists($c,'countAll') ? $c->countAll() : 0,
                'follows'  => method_exists($f,'countAll') ? $f->countAll() : 0,
                'admins'   => method_exists($a,'countAll') ? $a->countAll() : 0,
            ];

            require_once __DIR__ . '/../../views/admin/layouts/header.php';
            require_once __DIR__ . '/../../views/admin/index.php';
            require_once __DIR__ . '/../../views/admin/layouts/footer.php';
        }
    }
