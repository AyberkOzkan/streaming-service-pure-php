<?php 

    class GenreController
    {
        public static function all(): array {
            $baseUrl = getenv('JIKAN_API_URL') ?: 'https://api.jikan.moe/v4';
            $url = "{$baseUrl}/genres/anime";
            $json = cachedGet($url);
            if (!$json) return [];

            $data = json_decode($json, true);
            return $data['data'] ?? [];
        }

        public static function show(string $genreName): void {
            $genreMap = self::genreNameToIdMap(); // name → id
            $genreName = str_replace('_', ' ', urldecode($genreName));
            $genreId = $genreMap[strtolower($genreName)] ?? null;

            if (!$genreId) {
                http_response_code(404);
                echo "Genre not found.";
                return;
            }

            $baseUrl = getenv('JIKAN_API_URL') ?: 'https://api.jikan.moe/v4';
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $url = "{$baseUrl}/anime?genres={$genreId}&limit=24&page={$page}";
            $json = cachedGet($url);
            if (!$json) {
                http_response_code(500);
                echo "Failed to fetch anime list.";
                return;
            }

            $data = json_decode($json, true);
            $animeList = $data['data'] ?? [];
            $pagination = $data['pagination'] ?? [];
            $totalItems = $pagination['items']['total'] ?? 0;
            $perPage = $pagination['items']['per_page'] ?? 24;
            $currentPage = $pagination['current_page'] ?? 1;
            $lastPage = $pagination['last_visible_page'] ?? 1;
            $categoryTitle = ucfirst($genreName);
            $genreName = strtolower($genreName);
            $basePath = "/anime/genre/" . urlencode($genreName);
            require_once __DIR__ . '/../views/anime/categories.php';
        }

        private static function genreNameToIdMap(): array {
            // İstersen bunu cache edebilirsin
            $genres = self::all();
            $map = [];

            foreach ($genres as $genre) {
                $map[strtolower($genre['name'])] = $genre['mal_id'];
            }

            return $map;
        }


    }
