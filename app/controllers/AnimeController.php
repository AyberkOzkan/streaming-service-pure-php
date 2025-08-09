<?php 
    require_once __DIR__ . '/../models/FollowModel.php';
    require_once __DIR__ . '/../models/CommentModel.php';

    class AnimeController 
    {
        public static function trending(int $limit = 6): array {
            $baseUrl = getenv('JIKAN_API_URL') ?: 'https://api.jikan.moe/v4';
            $url = "{$baseUrl}/top/anime?limit={$limit}";
            $json = cachedGet($url);
            if (!$json) return [];

            $data = json_decode($json, true);
            return $data['data'] ?? [];
        }

        public static function adventure(int $limit = 6): array {
            $baseUrl = getenv('JIKAN_API_URL') ?: 'https://api.jikan.moe/v4';
            // Genre 2 = Adventure --> https://api.jikan.moe/v4/genres/anime
            $url = "{$baseUrl}/anime?genres=2&limit={$limit}";
            $json = cachedGet($url);
            if (!$json) return [];

            $data = json_decode($json, true);
            return $data['data'] ?? [];
        }

        public static function recentlyAdded(int $limit = 6): array {
            $baseUrl = getenv('JIKAN_API_URL') ?: 'https://api.jikan.moe/v4';
            $url = "{$baseUrl}/anime?order_by=mal_id&sort=desc&limit={$limit}";
            $json = cachedGet($url);
            if (!$json) return [];

            $data = json_decode($json, true);
            return $data['data'] ?? [];
        }

        public static function liveAiring(int $limit = 6): array {
            $baseUrl = getenv('JIKAN_API_URL') ?: 'https://api.jikan.moe/v4';
            $url = "{$baseUrl}/anime?status=airing&type=tv&order_by=popularity&sort=desc&limit={$limit}";
            $json = cachedGet($url);
            if (!$json) return [];

            $data = json_decode($json, true);
            return $data['data'] ?? [];
        }

        // public static function forYou(int $limit = 6): array {
        //     // This method can be customized based on user preferences or other criteria
        //     // For now, we'll just return trending anime
        //     return self::trending($limit);
        // }

        public static function heroSlider(int $limit = 10): array {
            $baseUrl = getenv('JIKAN_API_URL') ?: 'https://api.jikan.moe/v4';
            $url = "{$baseUrl}/top/anime?limit={$limit}&filter=bypopularity";
            $json = cachedGet($url);
            if (!$json) return [];

            $data = json_decode($json, true);
            return $data['data'] ?? [];
        }

        public static function categoryPage(string $category): void {
            $baseUrl = getenv('JIKAN_API_URL') ?: 'https://api.jikan.moe/v4';
            $url = "{$baseUrl}/anime?genres=" . urlencode($category) . "&limit=20";
            $json = cachedGet($url);
            if (!$json) {
                http_response_code(404);
                echo "Category not found.";
                return;
            }

            $data = json_decode($json, true);
            $animeList = $data['data'] ?? [];
            $categoryTitle = htmlspecialchars(ucfirst($category));

            require_once __DIR__ . '/../views/anime/categories.php';
        }

        public static function details(int $malId): void {
            $baseUrl = getenv('JIKAN_API_URL') ?: 'https://api.jikan.moe/v4';
            $url = "{$baseUrl}/anime/{$malId}";
            $json = cachedGet($url);
            if (!$json) {
                http_response_code(404);
                echo "Anime not found.";
                return;
            }

            $data = json_decode($json, true);
            $animeDetails = $data['data'] ?? null;

            if (!$animeDetails) {
                http_response_code(404);
                echo "Anime details not available.";
                return;
            }

            // Ana genre ID'yi al
            $genreId = $animeDetails['genres'][0]['mal_id'] ?? null;
            $recommendations = [];

            if ($genreId) {
                $recUrl = "{$baseUrl}/anime?genres={$genreId}&limit=25";
                $recJson = cachedGet($recUrl);

                if ($recJson) {
                    $recData = json_decode($recJson, true);
                    $filtered = array_filter($recData['data'] ?? [], fn($a) => $a['mal_id'] != $malId);
                    shuffle($filtered);
                    $recommendations = array_slice($filtered, 0, 4);
                }
            }

            $followModel = new FollowModel();
            $isFollowing = false;
            if (isset($_SESSION['user_id'])) {
                $isFollowing = $followModel->isFollowing($_SESSION['user_id'], $malId);
            }

            $commentModel = new CommentModel();
            $comments = $commentModel->listByAnime($malId);


            require_once __DIR__ . '/../views/anime/details.php';
        }

        public static function trendingPage(int $page = 1): void {
            $limit = 24;
            $baseUrl = getenv('JIKAN_API_URL') ?: 'https://api.jikan.moe/v4';
            $url = "{$baseUrl}/top/anime?limit={$limit}&page={$page}";

            $json = cachedGet($url);
            if (!$json) {
                http_response_code(500);
                echo "Failed to fetch trending anime.";
                return;
            }

            $data = json_decode($json, true);
            $animeList = $data['data'] ?? [];
            $pagination = $data['pagination'] ?? [];
            $currentPage = $pagination['current_page'] ?? 1;
            $lastPage = $pagination['last_visible_page'] ?? 1;

            $categoryTitle = 'Trending Now';
            $basePath = '/anime/trending';
            require_once __DIR__ . '/../views/anime/categories.php';

        }

        public static function recentlyAddedPage(int $page = 1): void {
            $limit = 24;
            $baseUrl = getenv('JIKAN_API_URL') ?: 'https://api.jikan.moe/v4';
            $url = "{$baseUrl}/anime?order_by=mal_id&sort=desc&limit={$limit}&page={$page}";

            $json = cachedGet($url);
            if (!$json) {
                http_response_code(500);
                echo "Failed to fetch recent anime.";
                return;
            }

            $data = json_decode($json, true);
            $animeList = $data['data'] ?? [];
            $pagination = $data['pagination'] ?? [];
            $currentPage = $pagination['current_page'] ?? 1;
            $lastPage = $pagination['last_visible_page'] ?? 1;

            $categoryTitle = 'Recently Added';
            $basePath = '/anime/recent';
            require_once __DIR__ . '/../views/anime/categories.php';

        }

        public static function liveAiringPage(int $page = 1): void {
            $limit = 24;
            $baseUrl = getenv('JIKAN_API_URL') ?: 'https://api.jikan.moe/v4';
            $url = "{$baseUrl}/anime?status=airing&type=tv&order_by=popularity&sort=desc&limit={$limit}&page={$page}";

            $json = cachedGet($url);
            if (!$json) {
                http_response_code(500);
                echo "Failed to fetch airing anime.";
                return;
            }

            $data = json_decode($json, true);
            $animeList = $data['data'] ?? [];
            $pagination = $data['pagination'] ?? [];
            $currentPage = $pagination['current_page'] ?? 1;
            $lastPage = $pagination['last_visible_page'] ?? 1;

            $categoryTitle = 'Currently Airing';
            $basePath = '/anime/airing';
            require_once __DIR__ . '/../views/anime/categories.php';

        }


    }