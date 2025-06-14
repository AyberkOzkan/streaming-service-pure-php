<?php
    function asset($path) {
        return rtrim(BASE_URL, '/') . '/' . ltrim($path, '/');
    }


