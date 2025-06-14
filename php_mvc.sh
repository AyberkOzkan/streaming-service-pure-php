#!/bin/bash

echo "### PHP MVC Proje Başlangıç Scripti ###"

# Sabit proje dizini (kullanıcıdan alınmaz)
BASE_DIR="/var/www"
read -p "Proje adı (örnek: streamingservice): " PROJECT_NAME
APP_ROOT="$BASE_DIR/$PROJECT_NAME"
BACKUP_DIR="${APP_ROOT}-backups"

# Veritabanı bilgileri
read -p "Veritabanı adı: " DB_NAME
read -p "Veritabanı kullanıcı adı: " DB_USER
read -sp "Veritabanı şifresi: " DB_PASS
echo ""

# Boşlukları temizle
PROJECT_NAME=$(echo "$PROJECT_NAME" | xargs)
DB_NAME=$(echo "$DB_NAME" | xargs)
DB_USER=$(echo "$DB_USER" | xargs)
DB_PASS=$(echo "$DB_PASS" | xargs)

echo "[1/10] ➜ Dizin yapısı oluşturuluyor..."
mkdir -p "$APP_ROOT"/{public,app/{controllers,models,views,config,core},logs} || { echo "❌ Dizin oluşturulamadı."; exit 1; }

echo "[2/10] ➜ index.php oluşturuluyor..."
cat <<EOF > "$APP_ROOT/public/index.php"
<?php
require_once __DIR__ . '/../app/config/bootstrap.php';
require_once __DIR__ . '/../app/router.php';
?>
EOF

echo "[3/10] ➜ bootstrap.php oluşturuluyor..."
cat <<EOF > "$APP_ROOT/app/config/bootstrap.php"
<?php
require_once __DIR__ . '/../core/env_loader.php';
require_once __DIR__ . '/../core/Logger.php';
require_once __DIR__ . '/db.php';
EOF

echo "[4/10] ➜ router.php oluşturuluyor..."
cat <<EOF > "$APP_ROOT/app/router.php"
<?php
\$uri = parse_url(\$_SERVER['REQUEST_URI'], PHP_URL_PATH);
switch (\$uri) {
    case '/':
        require_once __DIR__ . '/controllers/HomeController.php';
        \$controller = new HomeController();
        \$controller->index();
        break;
    default:
        http_response_code(404);
        echo "404 Not Found";
        break;
}
EOF

echo "[5/10] ➜ db.php oluşturuluyor..."
cat <<EOF > "$APP_ROOT/app/config/db.php"
<?php
define('DB_HOST', getenv('DB_HOST'));
define('DB_NAME', getenv('DB_NAME'));
define('DB_USER', getenv('DB_USER'));
define('DB_PASS', getenv('DB_PASS'));

try {
    \$pdo = new PDO('pgsql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASS);
} catch (PDOException \$e) {
    Logger::error('Veritabanı bağlantı hatası: ' . \$e->getMessage());
    die('Veritabanı bağlantı hatası!');
}
EOF

echo "[6/10] ➜ .env oluşturuluyor..."
cat <<EOF > "$APP_ROOT/.env"
DB_HOST=localhost
DB_NAME=$DB_NAME
DB_USER=$DB_USER
DB_PASS=$DB_PASS
EOF

echo "[7/10] ➜ env_loader.php oluşturuluyor..."
cat <<EOF > "$APP_ROOT/app/core/env_loader.php"
<?php
\$envPath = __DIR__ . '/../../.env';
if (file_exists(\$envPath)) {
    \$lines = file(\$envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach (\$lines as \$line) {
        if (strpos(trim(\$line), '#') === 0) continue;
        list(\$key, \$value) = explode('=', \$line, 2);
        putenv(trim(\$key) . '=' . trim(\$value));
    }
}
EOF

echo "[8/10] ➜ Logger.php oluşturuluyor..."
cat <<EOF > "$APP_ROOT/app/core/Logger.php"
<?php
class Logger {
    public static function error(\$message) {
        error_log('[ERROR] ' . \$message . PHP_EOL, 3, __DIR__ . '/../../logs/error.log');
    }

    public static function info(\$message) {
        error_log('[INFO] ' . \$message . PHP_EOL, 3, __DIR__ . '/../../logs/info.log');
    }
}
EOF

echo "[9/10] ➜ HomeController.php oluşturuluyor..."
cat <<EOF > "$APP_ROOT/app/controllers/HomeController.php"
<?php
class HomeController {
    public function index() {
        echo "<h1>Hoş geldiniz - $PROJECT_NAME</h1>";
    }
}
EOF

echo ""
echo "[10/10] ➜ backup dizini ve yetkiler ayarlanıyor..."
mkdir -p "$BACKUP_DIR"
chown -R $USER:www-data "$APP_ROOT" "$BACKUP_DIR"
chmod -R 755 "$APP_ROOT" "$BACKUP_DIR"

echo "$APP_ROOT ve $BACKUP_DIR dizinleri yetkilendirildi ($USER:www-data)"



echo "Proje kurulum tamamlandı: $APP_ROOT"
