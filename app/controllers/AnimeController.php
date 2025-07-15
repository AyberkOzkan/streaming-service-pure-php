<?php 

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


    }