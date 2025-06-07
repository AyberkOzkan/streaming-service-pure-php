<?php
define('DB_HOST', getenv('DB_HOST'));
define('DB_NAME', getenv('DB_NAME'));
define('DB_USER', getenv('DB_USER'));
define('DB_PASS', getenv('DB_PASS'));

try {
    $pdo = new PDO('pgsql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASS);
} catch (PDOException $e) {
    Logger::error('Veritabanı bağlantı hatası: ' . $e->getMessage());
    die('Veritabanı bağlantı hatası!');
}
