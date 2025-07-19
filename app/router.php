<?php
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    error_log("Requested URI: " . $uri);
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


        // // --- View All / Anime Genre Pages ---
        // case preg_match('#^/anime/trending$#', $uri) ? true : false:
        //     require_once __DIR__ . '/controllers/AnimeController.php';
        //     AnimeController::trendingPage();
        //     break;

        // case preg_match('#^/anime/genre/adventure$#', $uri) ? true : false:
        //     require_once __DIR__ . '/controllers/AnimeController.php';
        //     AnimeController::adventurePage();
        //     break;

        // case preg_match('#^/anime/recent$#', $uri) ? true : false:
        //     require_once __DIR__ . '/controllers/AnimeController.php';
        //     AnimeController::recentlyAddedPage();
        //     break;

        // case preg_match('#^/anime/genre/liveaction$#', $uri) ? true : false:
        //     require_once __DIR__ . '/controllers/AnimeController.php';
        //     AnimeController::liveActionPage();
        //     break;

        default:
            http_response_code(404);
            echo "404 Not Found";
            break;
    }
