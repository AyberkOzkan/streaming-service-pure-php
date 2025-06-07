# Streaming Service Web (Pure PHP + Nginx + PostgreSQL + HTML + CSS + JS)

Bu notlar, saf PHP ile geliştirdiğim `streamingservice` adlı web projesinin sıfırdan kurulumu içindir.

---

## Ubuntu Server Kurulumu

- VirtualBox üzerinden GUI’siz Ubuntu Server kuruldu.
- Sistem güncellenerek gerekli teknolojiler kuruldu

```bash
    sudo apt update && sudo apt upgrade -y
    sudo apt install nginx php php-fpm php-pgsql postgresql postgresql-contrib git unzip curl composer ufw -y
```

- `ssh` ile erişim için sunucuya bağlantı denendi.

```bash
    sudo systemctl status ssh
    sudo ufw allow OpenSSH
    sudo systemctl enable ssh
```

---

## Apache Kaldırıldı

Varsayılan olarak gelen Apache web sunucusu kaldırıldı:

```bash
    sudo systemctl stop apache2
    sudo apt purge apache2 apache2-utils apache2-bin apache2.2-common -y
    sudo apt autoremove -y
    sudo systemctl disable apache2
```

## NGINX Yapılandırılması

- `nginx.conf` dosyası doğrudan düzenlendi.
- Gerekli konfigürasyonlar yapıldı.

```bash
    sudo nginx -t
    sudo systemctl restart nginx
```

## Yardımcı Scriptlerin Yazılması

- MVC yapısı ve gerekli izinler için `php_mvc.sh` yazıldı.
- Sunucuya web dosyalarını gönderebilmek için `web_update.sh` yazıldı.

### Proje Dizin yapısı

---

#### Proje Adı: _streamingservice_

#### Konum: _/var/www/streamingservice_

```    public/
    app/
    ├── controllers/
    ├── models/
    ├── views/
    ├── config/
    │     ├── db.php
    │     └── bootstrap.php
    └── core/
            ├── Logger.php
            └── env_loader.php
    logs/
    .env
    .gitignore
```

## Veritabanı Oluşturuldu

```sql
    CREATE DATABASE streamingservice;
    CREATE USER root WITH PASSWORD 'şifreniz';
    GRANT ALL PRIVILEGES ON DATABASE streamingservice TO root;
```

> `.env` dosyasındaki bilgiler veritabanı bağlantısı için kullanılmaktadır. Şifre aynı olmalıdır.

---

## Genel Durum

- [x] Ubuntu Server kuruldu  
- [x] Apache kaldırıldı  
- [x] Nginx + PHP yapılandırıldı  
- [x] Proje dizin yapısı script ile kuruldu  
- [x] Veritabanı PostgreSQL ile tanımlandı  
- [x] Web projesine tarayıcıdan erişildi  

---
