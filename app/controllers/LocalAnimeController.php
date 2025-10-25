<?php
    require_once __DIR__ . '/../models/AnimeLocalModel.php';
    require_once __DIR__ . '/../models/CommentModel.php';
    require_once __DIR__ . '/../core/helpers.php';

    class LocalAnimeController {

        /** Dakikayı "X hr Y min" metnine çevir */
        private static function minutesToDuration(?int $m): ?string {
            if (empty($m) || $m <= 0) return null;
            $h = intdiv($m, 60);
            $r = $m % 60;
            if ($h > 0 && $r > 0) return "{$h} hr {$r} min";
            if ($h > 0)            return "{$h} hr";
            return "{$r} min";
        }

        public static function details(int $id): void {
            $m = new AnimeLocalModel();
            $anime = $m->getById($id);
            if (!$anime) { http_response_code(404); echo "Not found"; return; }

            // studios/genres virgülle geliyorsa diziye çevir
            $studios = [];
            if (!empty($anime['studios'])) {
                foreach (explode(',', $anime['studios']) as $s) {
                    $s = trim($s);
                    if ($s !== '') $studios[] = ['name' => $s]; // Jikan benzeri yapı
                }
            }
            $genres = [];
            if (!empty($anime['genres'])) {
                foreach (explode(',', $anime['genres']) as $g) {
                    $g = trim($g);
                    if ($g !== '') $genres[] = $g; // view zaten string/array ikisini de destekliyor
                }
            }

            $animeDetails = [
                'id'             => $id,
                'title'          => $anime['title'],
                'synopsis'       => $anime['synopsis'] ?? '',
                'poster_url'     => $anime['poster_url'] ?? null,
                'release_date'   => $anime['release_date'] ?? null,
                'total_episodes' => $anime['total_episodes'] ?? null,
                'type'           => $anime['type']   ?? null,
                'status'         => $anime['status'] ?? null,
                'duration'       => self::minutesToDuration(isset($anime['duration_minutes']) ? (int)$anime['duration_minutes'] : null),
                'studios'        => $studios,                       // view: studios[0]['name'] ... destekliyor
                'studio'         => $studios[0]['name'] ?? null,    // fallback için
                'genres'         => $genres,
                'mal_id'         => null,
            ];

            // URL’ler
            $detailsUrl   = "/local/anime/{$id}";
            $watchBaseUrl = "/local/anime/{$id}/watch";
            $recommendations = [];
            $comments = [];

            // follow durumu
            $isFollowing = false;
            if (isset($_SESSION['user_id'])) {
                require_once __DIR__ . '/../models/FollowModel.php';
                $fm = new FollowModel();
                $isFollowing = $fm->isFollowing((int)$_SESSION['user_id'], $id, 'local');
            }

            require_once __DIR__ . '/../views/anime/details.php';
        }

        public static function watch(int $id, int $ep = 1): void {
            $m = new AnimeLocalModel();
            $anime = $m->getById($id);
            if (!$anime) { http_response_code(404); echo "Not found"; return; }

            $animeDetails = [
                'title'       => $anime['title'],
                'id'          => $id,
                'poster_url'  => $anime['poster_url'] ?? null,
                'mal_id'      => null, // MAL yok
            ];

            $detailsUrl   = "/local/anime/{$id}";
            $watchBaseUrl = "{$detailsUrl}/watch";

            $episodes = $m->episodesByAnimeId($id);
            $byNo = [];
            foreach ($episodes as $row) { $byNo[(int)$row['ep_no']] = $row; }
            $current = $byNo[(int)$ep] ?? (reset($episodes) ?: null);

            $localPlayerUrl    = $current['stream_url'] ?? null;
            $localPlayerUrl = $localPlayerUrl ? normalizeEmbedUrl($localPlayerUrl) : null;
            $playerPoster      = $anime['poster_url'] ?? null;
            $rawTrailer        = $anime['trailer_url'] ?? null;
            $currentPromoUrl   = $rawTrailer ? normalizeEmbedUrl($rawTrailer) : null;
            $currentPromoTitle = $current['title'] ?? $anime['title'];

            $currentEpisode = $current ? (int)$current['ep_no'] : max(1,(int)$ep);
            $episodeCount   = max((int)($anime['total_episodes'] ?? 0), count($episodes));
            if ($episodeCount <= 0) $episodeCount = max(1, count($episodes));

            $comments = [];

            require __DIR__ . '/../views/anime/watch.php';
        }
    }
