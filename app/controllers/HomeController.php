<?php
    
    class HomeController {
        public function index() {
            require_once __DIR__ . '/AnimeController.php';
            require_once __DIR__ . '/GenreController.php';

            $forYou         = [];
            $groupedGenres  = [];
            $showForYou     = false;

            $trending = AnimeController::trending();
            $hero = AnimeController::heroSlider();
            $adventure = AnimeController::adventure();
            $recent = AnimeController::recentlyAdded();
            $live = AnimeController::liveAiring();
            $genres = GenreController::all();

            if (isset($_SESSION['user_id'])) {
                $forYou = AnimeController::forYouFromFollowed((int)$_SESSION['user_id'], 8);
                $showForYou = !empty($forYou);
            }


            foreach ($genres as $genre) {
                $firstLetter = strtoupper($genre['name'][0]);
                if (!ctype_alpha($firstLetter)) {
                    $firstLetter = '#';
                }
                $groupedGenres[$firstLetter][] = $genre;
            }

            ksort($groupedGenres);
            if (isset($groupedGenres['#'])) {
                $hashBlock = $groupedGenres['#'];
                unset($groupedGenres['#']);
                $groupedGenres['#'] = $hashBlock;
            }

            $availableLetters = array_keys($groupedGenres);
            $firstLetter = null;
            foreach ($availableLetters as $L) { if ($L !== '#') { $firstLetter = $L; break; } }

            require_once __DIR__ . '/../views/layouts/header.php';
            require_once __DIR__ . '/../views/home/index.php';
            require_once __DIR__ . '/../views/layouts/footer.php';
        }
    }
