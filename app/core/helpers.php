<?php
    function asset($path) {
        return rtrim(BASE_URL, '/') . '/' . ltrim($path, '/');
    }

    function cachedGet(string $url, int $ttl = 3600): string|false {
        $ttl = getenv('API_CACHE_TTL') ?: $ttl;
        $key = '/tmp/api_' . md5($url);
        if (file_exists($key) && time() - filemtime($key) < $ttl) {
            return file_get_contents($key);
        }
        $data = @file_get_contents($url);
        if ($data) file_put_contents($key, $data);
        return $data;
    }


