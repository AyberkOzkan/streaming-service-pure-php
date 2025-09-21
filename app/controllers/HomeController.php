<?php
    
    class HomeController {
        public function index() {
            require_once __DIR__ . '/AnimeController.php';
            require_once __DIR__ . '/GenreController.php';
            require_once __DIR__ . '/../models/AnimeLocalModel.php';


            $forYou         = [];
            $groupedGenres  = [];
            $showForYou     = false;

            $trending = AnimeController::trending();
            $hero = AnimeController::heroSlider();
            $adventure = AnimeController::adventure();
            $limitRecent = 12;
            // Jikan "recent"
            $recentJikan = AnimeController::recentlyAdded($limitRecent);
            foreach ($recentJikan as &$a) {
                // tek tip görsel alanı
                if (!isset($a['images']['jpg']['image_url'])) {
                    $a['images']['jpg']['image_url'] =
                        $a['images']['jpg']['large_image_url']
                        ?? ($a['images']['webp']['large_image_url'] ?? null);
                }
                // Jikan tarafında "eklenme" için aired.from'u kullan (yoksa 0)
                $from = $a['aired']['from'] ?? null;
                $a['added_ts'] = $from ? strtotime($from) : 0;
                $a['local_id'] = null;
                $a['source']   = 'jikan';
            }
            unset($a);
            $alm = new AnimeLocalModel();
            $recentLocal = $alm->listLatestMapped($limitRecent);
            $recentCombined = array_merge($recentLocal, $recentJikan);
            usort($recentCombined, fn($x,$y) => ($y['added_ts'] ?? 0) <=> ($x['added_ts'] ?? 0));
            $recent = array_slice($recentCombined, 0, $limitRecent);
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
