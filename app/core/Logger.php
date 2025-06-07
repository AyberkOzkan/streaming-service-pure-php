<?php
class Logger {
    public static function error($message) {
        error_log('[ERROR] ' . $message . PHP_EOL, 3, __DIR__ . '/../../logs/error.log');
    }

    public static function info($message) {
        error_log('[INFO] ' . $message . PHP_EOL, 3, __DIR__ . '/../../logs/info.log');
    }
}
