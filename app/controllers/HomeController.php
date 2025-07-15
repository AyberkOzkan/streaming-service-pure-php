<?php
    
    class HomeController {
        public function index() {
            require_once __DIR__ . '/AnimeController.php';
            $trending = AnimeController::trending();
            $hero = AnimeController::heroSlider();
            $adventure = AnimeController::adventure();
            $recent = AnimeController::recentlyAdded();
            $live = AnimeController::liveAiring();
            // $forYou = AnimeController::forYou();
            require_once __DIR__ . '/../views/layouts/header.php';
            require_once __DIR__ . '/../views/home/index.php';
            require_once __DIR__ . '/../views/layouts/footer.php';
        }
    }
