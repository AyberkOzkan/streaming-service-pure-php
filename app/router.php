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
        default:
            http_response_code(404);
            echo "404 Not Found";
            break;
    }
