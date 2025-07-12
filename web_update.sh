#!/bin/bash

# === Ayarlar ===
REMOTE_USER="streamingservice"
REMOTE_IP="192.168.1.107"
REMOTE_PATH="/var/www/streamingservice"
LOCAL_PATH=$(pwd)
SSH_PORT=22

BACKUP_BEFORE_UPLOAD=true
MAX_BACKUPS=5
BACKUP_PATH="$REMOTE_PATH-backups"
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
BACKUP_NAME="backup_$TIMESTAMP.tar.gz"

DEPLOY_LOGS="$HOME/deploy_logs"
mkdir -p "$DEPLOY_LOGS"
LOG_FILE="$DEPLOY_LOGS/deploy_$TIMESTAMP.log"

# Gönderilmeyecek dosyalar
EXCLUDE=(
  "--exclude=.git/"
  "--exclude=node_modules/"
  "--exclude=logs/"
  "--exclude=.env"
)

# Senkron sonrası uzak sunucuda yapılacak komut (opsiyonel)
REMOTE_COMMAND_AFTER_SYNC=""

# === Fonksiyonlar ===

function log() {
    echo -e "$1" | tee -a "$LOG_FILE"
}

function confirm() {
    read -p "Uzak sunucuya dosya aktarımı yapılacak. Devam ediyor musun? (e/h): " -n 1 -r
    echo
    [[ $REPLY =~ ^[Ee]$ ]] || exit 1
}

function check_connection() {
    log "SSH bağlantısı test ediliyor..."
    ssh -p $SSH_PORT -o ConnectTimeout=5 ${REMOTE_USER}@${REMOTE_IP} "echo Bağlantı başarılı." || {
        log "Bağlantı başarısız."
        exit 1
    }
}

function ensure_remote_dirs() {
    log "Uzak dizinler kontrol ediliyor..."
    ssh -p $SSH_PORT ${REMOTE_USER}@${REMOTE_IP} "mkdir -p $REMOTE_PATH $BACKUP_PATH"
}

function backup_remote_site() {
    if [ "$BACKUP_BEFORE_UPLOAD" = true ]; then
        log "Yedek alınıyor: $BACKUP_NAME"
        ssh -p $SSH_PORT ${REMOTE_USER}@${REMOTE_IP} \
        "cd $(dirname $REMOTE_PATH) && tar -czf $BACKUP_PATH/$BACKUP_NAME $(basename $REMOTE_PATH)" || {
            log "Yedek alınırken hata oluştu."
        }
    fi
}

function remote_cleanup() {
    if [ "$BACKUP_BEFORE_UPLOAD" = true ]; then
        log "Eski yedekler temizleniyor (en fazla $MAX_BACKUPS tane tutulur)..."
        ssh -p $SSH_PORT ${REMOTE_USER}@${REMOTE_IP} "
            cd $BACKUP_PATH && ls -t | tail -n +$((MAX_BACKUPS + 1)) | xargs --no-run-if-empty rm -f"
    fi
}

# function sync_project() {
#     log "Dosyalar aktarılıyor..."
#     rsync -avz --delete "${EXCLUDE[@]}" -e "ssh -p $SSH_PORT" \
#         "$LOCAL_PATH/" "${REMOTE_USER}@${REMOTE_IP}:${REMOTE_PATH}" | tee -a "$LOG_FILE"
# }

# function check_env_file() {
#     if [[ -f "$LOCAL_PATH/.env" ]]; then
#         if [[ ! -f "$LOCAL_PATH/.env.example" ]]; then
#             log ".env dosyası bulundu ama .env.example dosyası eksik. Lütfen .env.example dosyasını oluşturun."
#         fi
#     fi
# }

function after_sync_command() {
    if [[ -n "$REMOTE_COMMAND_AFTER_SYNC" ]]; then
        log "Uzak sunucuda senkron sonrası komut çalıştırılıyor: $REMOTE_COMMAND_AFTER_SYNC"
        ssh -p $SSH_PORT ${REMOTE_USER}@${REMOTE_IP} "$REMOTE_COMMAND_AFTER_SYNC"
    fi
}

function sync_project() {
    log "Dosyalar SCP ile gönderiliyor..."

    # Uzak dizini oluştur
    ssh -p $SSH_PORT ${REMOTE_USER}@${REMOTE_IP} "mkdir -p $REMOTE_PATH"
    FILES_TO_SEND=$(find "$LOCAL_PATH" -maxdepth 1 \( -type f -or -type d \) \
        | grep -vE "\.git$|node_modules$|logs$|\.sh$|\.md$" \
        | grep -v "$LOCAL_PATH$")

    if [[ -z "$FILES_TO_SEND" ]]; then
        log "Gönderilecek dosya bulunamadı."
        exit 1
    fi

    scp -r -P $SSH_PORT $FILES_TO_SEND "${REMOTE_USER}@${REMOTE_IP}:${REMOTE_PATH}/" || {
        log "Dosya gönderimi başarısız."
        exit 1
    }

    log "Dosyalar başarıyla senkronize edildi (scp)."
}



# === Çalıştır ===
log "### web_update.sh başlatıldı ###"
confirm
check_connection
# check_env_file
ensure_remote_dirs
backup_remote_site
remote_cleanup
sync_project
after_sync_command

log "Tamamlandı: $TIMESTAMP"
