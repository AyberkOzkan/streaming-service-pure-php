<?php

    class Database {
        private static $instance = null;

        public static function connect() {
            if (self::$instance === null) {
                $host = getenv('DB_HOST');
                $dbname = getenv('DB_NAME');
                $user = getenv('DB_USER');
                $pass = getenv('DB_PASS');
                error_log("Connecting to database: $host, $dbname");
                error_log("Using user: $user");
                try {
                    $dsn = "pgsql:host=$host;dbname=$dbname";
                    self::$instance = new PDO($dsn, $user, $pass);
                    self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                } catch (PDOException $e) {
                    Logger::error('Veritabanı bağlantı hatası: ' . $e->getMessage());
                    die('Veritabanı bağlantı hatası!');
                }
            }

            return self::$instance;
        }
    }
