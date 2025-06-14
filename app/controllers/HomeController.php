<?php
    class HomeController {
        public function index() {
            require_once __DIR__ . '/../views/layouts/header.php';
            require_once __DIR__ . '/../views/home/index.php';
            require_once __DIR__ . '/../views/layouts/footer.php';
        }
    }
