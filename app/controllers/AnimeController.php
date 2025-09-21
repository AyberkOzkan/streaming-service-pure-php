<?php 
    require_once __DIR__ . '/../models/FollowModel.php';
    require_once __DIR__ . '/../models/CommentModel.php';
    require_once __DIR__ . '/../models/AnimeLocalModel.php';


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

        public static function forYouFromFollowed(int $userId, int $limit = 8): array {
            $baseUrl = getenv('JIKAN_API_URL') ?: 'https://api.jikan.moe/v4';

            $followModel = new FollowModel();
            $follows = $followModel->listByUser($userId, 200, 0);
            if (empty($follows)) {
                return self::trending($limit); // fallback
            }

            // Takip edilenlerdeki genre’ları al
            $genreCount  = [];
            $followedIds = [];
            foreach ($follows as $row) {
                $malId = (int)$row['anime_id'];
                $followedIds[$malId] = true;

                $json = cachedGet("{$baseUrl}/anime/{$malId}");
                if (!$json) continue;
                $d = json_decode($json, true);
                $genres = $d['data']['genres'] ?? [];
                foreach ($genres as $g) {
                    $gid = (int)($g['mal_id'] ?? 0);
                    if ($gid > 0) {
                        $genreCount[$gid] = ($genreCount[$gid] ?? 0) + 1;
                    }
                }
            }

            if (empty($genreCount)) {
                return self::trending($limit);
            }

            // En çok tekrar edenleri al
            arsort($genreCount);
            $topGenreIds = array_slice(array_keys($genreCount), 0, 3);

            // Bu türlerden popülerleri çek
            $idsParam = implode(',', $topGenreIds);
            $fetchLimit = max($limit * 3, 12);
            $recUrl = "{$baseUrl}/anime?genres={$idsParam}&order_by=popularity&sort=desc&limit={$fetchLimit}";
            $recJson = cachedGet($recUrl);
            if (!$recJson) {
                return self::trending($limit);
            }

            $rec = json_decode($recJson, true);
            $candidates = $rec['data'] ?? [];

            // Zaten takip ettiklerini çıkar, $limit kadar döndür
            $result = [];
            foreach ($candidates as $a) {
                $aid = (int)($a['mal_id'] ?? 0);
                if (!$aid || isset($followedIds[$aid])) continue;

                $result[] = [
                    'mal_id'   => $aid,
                    'title'    => $a['title'] ?? 'Unknown',
                    'images'   => $a['images'] ?? [],
                    'type'     => $a['type'] ?? 'Unknown',
                    'members'  => $a['members'] ?? 0,
                    'episodes' => $a['episodes'] ?? '?',
                    'score'    => $a['score'] ?? 'N/A',
                    'status'   => $a['status'] ?? 'Unknown',
                ];
                if (count($result) >= $limit) break;
            }

            return $result ?: self::trending($limit);
        }

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

            $alm = new AnimeLocalModel();
            $localEpisodes = $alm->episodesByMalId($malId);
            $hasLocalEpisodes = !empty($localEpisodes);
            $manageEpisodesUrl = (function_exists('isSuperAdmin') && isSuperAdmin())
                ? "/admin/anime/episodes/by-mal?mal_id={$malId}"
                : null;

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

        public static function watch(int $malId, int $ep = 1): void {
            $baseUrl = getenv('JIKAN_API_URL') ?: 'https://api.jikan.moe/v4';
            $json = cachedGet("{$baseUrl}/anime/{$malId}");
            if (!$json) { http_response_code(404); echo "Anime not found."; return; }
            $data = json_decode($json, true);
            $animeDetails = $data['data'] ?? null;
            if (!$animeDetails) { http_response_code(404); echo "Anime not found."; return; }

            // View’a verilecek linkler
            $detailsUrl   = "/anime/{$malId}";
            $watchBaseUrl = "{$detailsUrl}/watch";

            // Varsayılanlar
            $currentEpisode = max(1, (int)$ep);
            $episodeCount   = (int)($animeDetails['episodes'] ?? 12);
            if ($episodeCount <= 0) $episodeCount = 12;

            // LOCAL ÖNCE: MAL id'ye bağlı yerel ep var mı?
            $alm = new AnimeLocalModel();
            $localEpisodes = $alm->episodesByMalId($malId);

            $localPlayerUrl   = null;
            $playerPoster     = null;
            $currentPromoUrl  = null;
            $currentPromoTitle= $animeDetails['title'] ?? 'Video';
            $episodes         = []; // local ep listesi varsa doldurulacak

            if (!empty($localEpisodes)) {
                // ep seç
                $current = null;
                foreach ($localEpisodes as $row) {
                    if ((int)$row['ep_no'] === (int)$ep) { $current = $row; break; }
                }
                if (!$current) { $current = $localEpisodes[0]; }
                $localPlayerUrl    = $current['stream_url'] ?? null;
                $currentPromoTitle = $current['title'] ?? $currentPromoTitle;
                $currentEpisode    = (int)$current['ep_no'];
                $episodeCount      = count($localEpisodes);
                $episodes          = $localEpisodes; // view “local list varsa onu basar”
            } else {
                // FALLBACK: Jikan promos
                $videosJson = cachedGet("{$baseUrl}/anime/{$malId}/videos");
                $videos = $videosJson ? (json_decode($videosJson, true)['data'] ?? []) : [];
                $promos = $videos['promo'] ?? [];
                if (!empty($promos)) {
                    $first = $promos[0];
                    $currentPromoUrl   = $first['trailer']['embed_url'] ?? ($first['trailer']['url'] ?? null);
                    $currentPromoTitle = $first['title'] ?? $currentPromoTitle;
                }
            }

            // Yorumlar (mevcut yapın MAL id’ye bağlı)
            $commentModel = new CommentModel();
            $comments = $commentModel->listByAnime($malId);

            // View’a sadece hazır değişkenleri ver
            require __DIR__ . '/../views/anime/watch.php';
        }

        public static function mapJikanForRecent(array $items): array {
            foreach ($items as &$a) {
                // aired.from -> timestamp; yoksa 0
                $from = $a['aired']['from'] ?? null;
                $a['added_ts'] = $from ? strtotime($from) : 0;
                $a['local_id'] = null;
                $a['source']   = 'jikan';
            }
            return $items;
        }






    }