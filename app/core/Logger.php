<?php
    class Logger
    {
        protected static function log($level, $message) {
            $timestamp = date('Y-m-d H:i:s');
            $debugTrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
            $caller = isset($debugTrace[1]) ? $debugTrace[1] : null;
            $file = isset($caller['file']) ? basename($caller['file']) : 'unknown';
            $line = isset($caller['line']) ? $caller['line'] : 'unknown';

            $formattedMessage = sprintf(
                "[%s] [%s] [%s:%s] %s%s",
                $timestamp,
                strtoupper($level),
                $file,
                $line,
                $message,
                PHP_EOL
            );

            $logFile = __DIR__ . '/../../logs/' . strtolower($level) . '.log';
            error_log($formattedMessage, 3, $logFile);
        }

        public static function info($message) {
            self::log('info', $message);
        }

        public static function error($message) {
            self::log('error', $message);
        }

        public static function warning($message) {
            self::log('warning', $message);
        }
    }
