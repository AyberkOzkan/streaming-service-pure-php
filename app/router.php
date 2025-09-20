<?php
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    error_log("Requested URI: " . $uri);

    // Follow işlemleri için yönlendirme
    if ($uri === '/follow/add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        require_once __DIR__ . '/controllers/FollowController.php';
        FollowController::addFollow();
        return;
    }

    if ($uri === '/follow/remove' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        require_once __DIR__ . '/controllers/FollowController.php';
        FollowController::removeFollow();
        return;
    }

    if ($uri === '/comments/add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        require_once __DIR__ . '/controllers/CommentController.php';
        CommentController::add();
        return;
    }

    // /anime/{id}/watch
    if (preg_match('#^/anime/(\d+)/watch$#', $uri, $m)) {
        require_once __DIR__ . '/controllers/AnimeController.php';
        $ep = isset($_GET['ep']) ? (int)$_GET['ep'] : 1;
        AnimeController::watch((int)$m[1], $ep);
        return;
    }

    if ($uri === '/admin' || $uri === '/admin/') {
        require_once __DIR__ . '/controllers/Admin/DashboardController.php';
        $c = new Admin_DashboardController();
        $c->index();
        return;
    }


    switch ($uri) {
        case '/':
            require_once __DIR__ . '/controllers/HomeController.php';
            $controller = new HomeController();
            $controller->index();
            break;

        case '/register':
            require_once __DIR__ . '/controllers/AuthController.php';
            $controller = new AuthController();
            $controller->showRegisterForm();
            break;
        case '/register/submit':
            require_once __DIR__ . '/controllers/AuthController.php';
            $controller = new AuthController();
            $controller->register();
            break;
        case '/login':
            require_once __DIR__ . '/controllers/AuthController.php';
            $controller = new AuthController();
            $controller->showLoginForm();
            break;
        case '/login/submit':
            require_once __DIR__ . '/controllers/AuthController.php';
            $controller = new AuthController();
            $controller->login();
            break;
        case '/logout':
            require_once __DIR__ . '/controllers/AuthController.php';
            $controller = new AuthController();
            $controller->logout();
            break;
        case (preg_match('#^/anime/genre/([\w\-\+%]+)$#', $uri, $matches) ? true : false):
            require_once __DIR__ . '/controllers/GenreController.php';
            $controller = new GenreController();
            $controller->show($matches[1]);
            break;
        case (preg_match('#^/anime/(\d+)$#', $uri, $matches) ? true : false):
            require_once __DIR__ . '/controllers/AnimeController.php';
            AnimeController::details((int)$matches[1]);
            break;
        case (preg_match('#^/anime/trending(?:\?page=(\d+))?$#', $_SERVER['REQUEST_URI'], $matches) ? true : false):
            require_once __DIR__ . '/controllers/AnimeController.php';
            $page = isset($matches[1]) ? (int)$matches[1] : 1;
            AnimeController::trendingPage($page);
            break;
        case (preg_match('#^/anime/recent(?:\?page=(\d+))?$#', $_SERVER['REQUEST_URI'], $matches) ? true : false):
            require_once __DIR__ . '/controllers/AnimeController.php';
            $page = isset($matches[1]) ? (int)$matches[1] : 1;
            AnimeController::recentlyAddedPage($page);
            break;
        case (preg_match('#^/anime/airing(?:\?page=(\d+))?$#', $_SERVER['REQUEST_URI'], $matches) ? true : false):
            require_once __DIR__ . '/controllers/AnimeController.php';
            $page = isset($matches[1]) ? (int)$matches[1] : 1;
            AnimeController::liveAiringPage($page);
            break;
        case '/profile':
            require_once __DIR__ . '/controllers/ProfileController.php';
            $controller = new ProfileController();
            $controller->index();
            break;
        case '/profile/password':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                require_once __DIR__ . '/controllers/ProfileController.php';
                $controller = new ProfileController();
                $controller->changePassword();
            } else {
                http_response_code(405); echo "Method Not Allowed";
            }
            break;
        case '/profile/update':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                require_once __DIR__ . '/controllers/ProfileController.php';
                $controller = new ProfileController();
                $controller->updateProfile();
            } else {
                http_response_code(405); echo "Method Not Allowed";
            }
            break;
        case '/admin':
        case '/admin/':
            require_once __DIR__ . '/controllers/Admin/DashboardController.php';
            $controller = new Admin_DashboardController();
            $controller->index();
            break;
        case '/admin/admins':
        case '/admin/admins/':
            require_once __DIR__ . '/controllers/Admin/AdminController.php';
            $controller = new Admin_AdminsController();
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // form: yeni admin ekle
                $controller->create();
            } else {
                // liste sayfası
                $controller->index();
            }
            break;
        case '/admin/admins/create':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                require_once __DIR__ . '/controllers/Admin/AdminController.php';
                $controller = new Admin_AdminsController();
                $controller->create();
            } else {
                http_response_code(405); echo 'Method Not Allowed';
            }
            break;
        case '/admin/admins/delete':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo 'Method Not Allowed'; break; }
            require_once __DIR__ . '/controllers/Admin/AdminController.php';
            $controller = new Admin_AdminsController();
            $controller->delete();
            break;
        case '/admin/users':
            require_once __DIR__.'/controllers/Admin/UsersController.php';
            $controller = new Admin_UsersController();
            $controller->index();
            break;
        case '/admin/users/ban':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo 'Method Not Allowed'; break; }
            require_once __DIR__.'/controllers/Admin/UsersController.php';
            $controller = new Admin_UsersController();
            $controller->ban();
            break;
        case '/admin/users/unban':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo 'Method Not Allowed'; break; }
            require_once __DIR__.'/controllers/Admin/UsersController.php';
            $controller = new Admin_UsersController();
            $controller->unban();
            break;
        case '/admin/comments':
            require_once __DIR__ . '/controllers/Admin/CommentsController.php';
            $controller = new Admin_CommentsController();
            $controller->index();
            break;
        case '/admin/comments/delete':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo 'Method Not Allowed'; break; }
            require_once __DIR__ . '/controllers/Admin/CommentsController.php';
            $controller = new Admin_CommentsController();
            $controller->delete();
            break;
        case '/admin/anime':
            require_once __DIR__ . '/controllers/Admin/AnimeController.php';
            $controller = new Admin_AnimeController();
            $controller->index();
            break;
        case '/admin/anime/new':
            require_once __DIR__ . '/controllers/Admin/AnimeController.php';
            $controller = new Admin_AnimeController();
            $controller->new();
            break;
        case '/admin/anime/create':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo 'Method Not Allowed'; break; }
            require_once __DIR__ . '/controllers/Admin/AnimeController.php';
            $controller = new Admin_AnimeController();
            $controller->create();
            break;
        case '/admin/anime/edit':
            require_once __DIR__ . '/controllers/Admin/AnimeController.php';
            $controller = new Admin_AnimeController();
            $controller->edit();
            break;
        case '/admin/anime/update':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo 'Method Not Allowed'; break; }
            require_once __DIR__ . '/controllers/Admin/AnimeController.php';
            $controller = new Admin_AnimeController();
            $controller->update();
            break;
        case '/admin/anime/delete':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo 'Method Not Allowed'; break; }
            require_once __DIR__ . '/controllers/Admin/AnimeController.php';
            $controller = new Admin_AnimeController();
            $controller->delete();
            break;
        case '/admin/anime/episodes':
            require_once __DIR__ . '/controllers/Admin/AnimeController.php';
            $controller = new Admin_AnimeController();
            $controller->episodes();
            break;
        case '/admin/anime/episodes/create':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo 'Method Not Allowed'; break; }
            require_once __DIR__ . '/controllers/Admin/AnimeController.php';
            $controller = new Admin_AnimeController();
            $controller->addEpisode();
            break;

        case '/admin/anime/episodes/delete':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo 'Method Not Allowed'; break; }
            require_once __DIR__ . '/controllers/Admin/AnimeController.php';
            $controller = new Admin_AnimeController();
            $controller->deleteEpisode();
            break;
        case '/admin/anime/episodes/by-mal':
            require_once __DIR__ . '/controllers/Admin/AnimeController.php';
            $controller = new Admin_AnimeController();
            $controller->manageByMal();
            break;
        default:
            http_response_code(404);
            echo "404 Not Found";
            break;
    }

