<?php
    require_once __DIR__ . '/../models/AnimeLocalModel.php';
    require_once __DIR__ . '/../models/CommentModel.php';

    class LocalAnimeController {
        public static function details(int $id): void {
            require_once __DIR__ . '/../models/AnimeLocalModel.php';
            $m = new AnimeLocalModel();
            $anime = $m->getById($id);
            if (!$anime) { http_response_code(404); echo "Not found"; return; }

            // View’un beklediği anahtarları taklit eden adapter:
            $animeDetails = [
                'title'        => $anime['title'],
                'synopsis'     => $anime['synopsis'] ?? '',
                'poster_url'   => $anime['poster_url'] ?? null,
                'release_date' => $anime['release_date'] ?? null,
                'total_episodes' => $anime['total_episodes'] ?? null,
                // MAL alanları yok; boş bırakmak sorun değil:
                'mal_id'       => null,
            ];
            $detailsUrl   = "/local/anime/{$id}";
            $watchBaseUrl = "/local/anime/{$id}/watch";
            // yerel öneriler?
            $recommendations = [];
            // Yorumları (şimdilik) geçelim
            $comments = [];

            require_once __DIR__ . '/../views/anime/details.php';
        }

        public static function watch(int $id, int $ep = 1): void {
            $m = new AnimeLocalModel();
            $anime = $m->getById($id);
            if (!$anime) { http_response_code(404); echo "Not found"; return; }

            $animeDetails = [
                'title' => $anime['title'],
                'id'    => $id,
                'mal_id'=> null, // MAL yok
            ];

            $detailsUrl   = "/local/anime/{$id}";
            $watchBaseUrl = "{$detailsUrl}/watch";

            $episodes = $m->episodesByAnimeId($id);
            // epNo=>row map
            $byNo = [];
            foreach ($episodes as $row) { $byNo[(int)$row['ep_no']] = $row; }
            $current = $byNo[(int)$ep] ?? (reset($episodes) ?: null);

            $localPlayerUrl    = $current['stream_url'] ?? null; // tercih edilen kaynak
            $playerPoster      = null;
            $currentPromoUrl   = null; // fallback yok; local oynat
            $currentPromoTitle = $current['title'] ?? $anime['title'];

            $currentEpisode = $current ? (int)$current['ep_no'] : max(1,(int)$ep);
            $episodeCount   = max((int)($anime['total_episodes'] ?? 0), count($episodes));
            if ($episodeCount <= 0) $episodeCount = max(1, count($episodes));

            $comments = [];

            require __DIR__ . '/../views/anime/watch.php';
        }



    }
