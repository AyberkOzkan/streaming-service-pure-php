<?php
    
    class HomeController {
        public function index() {
            require_once __DIR__ . '/AnimeController.php';
            require_once __DIR__ . '/GenreController.php';
            
            $trending = AnimeController::trending();
            $hero = AnimeController::heroSlider();
            $adventure = AnimeController::adventure();
            $recent = AnimeController::recentlyAdded();
            $live = AnimeController::liveAiring();
            // $forYou = AnimeController::forYou();
            $genres = GenreController::all();
            $groupedGenres = [];

            foreach ($genres as $genre) {
                $firstLetter = strtoupper($genre['name'][0]);
                if (!ctype_alpha($firstLetter)) {
                    $firstLetter = '#';
                }
                $groupedGenres[$firstLetter][] = $genre;
            }

            ksort($groupedGenres); // Harf sırasına göre sırala


            require_once __DIR__ . '/../views/layouts/header.php';
            require_once __DIR__ . '/../views/home/index.php';
            require_once __DIR__ . '/../views/layouts/footer.php';
        }
    }
