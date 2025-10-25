<?php
    require_once __DIR__ . '/../core/helpers.php';
    require_once __DIR__ . '/../models/AnimeLocalModel.php';

    class SearchController {
        // /search?q=...
        public static function quick(): void {
            header('Content-Type: application/json; charset=utf-8');

            $q = trim($_GET['q'] ?? '');
            if ($q === '' || mb_strlen($q) < 2) { echo json_encode(['data'=>[]]); return; }

            $localM = new AnimeLocalModel();
            $local  = $localM->searchLight($q, 5);

            // Jikan’dan da 5 sonuç
            $baseUrl = getenv('JIKAN_API_URL') ?: 'https://api.jikan.moe/v4';
            $jikan   = [];
            $url     = $baseUrl . '/anime?q=' . rawurlencode($q) . '&limit=5';
            $json    = cachedGet($url, 60); // 60 sn cache isteğe bağlı
            if ($json) {
                $d = json_decode($json, true);
                foreach (($d['data'] ?? []) as $a) {
                    $jikan[] = [
                        'source' => 'mal',
                        'mal_id' => $a['mal_id'],
                        'title'  => $a['title'],
                        'images' => $a['images'],
                        'href'   => '/anime/'.$a['mal_id'],
                    ];
                }
            }

            // Local’i üstte göstermek istersen:
            $combined = array_merge($local, $jikan);

            echo json_encode(['data' => $combined], JSON_UNESCAPED_SLASHES);
        }
    }
